#!/usr/bin/env bash
# Smoke test against a real WordPress on SQLite, all inside build/ (gitignored).
# Downloads WordPress core, the SQLite Database Integration plugin and WP-CLI once
# into build/cache, installs a throwaway site, installs the plugin from dist/ and
# checks slugs, file names, the settings and uninstall.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
BUILD="$ROOT/build"; CACHE="$BUILD/cache"; SITE="$BUILD/wp"
PORT="${PORT:-18793}"; URL="http://127.0.0.1:$PORT"
ZIP="$(ls "$ROOT"/dist/bukvar-lite-*.zip | head -1)"
mkdir -p "$CACHE"
fetch() { [ -s "$CACHE/$2" ] || curl -fsSL -o "$CACHE/$2" "$1"; }
fetch https://wordpress.org/latest.zip wordpress.zip
fetch https://downloads.wordpress.org/plugin/sqlite-database-integration.latest-stable.zip sqlite.zip
fetch https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar wp-cli.phar
rm -rf "$SITE" && mkdir -p "$SITE"
unzip -q "$CACHE/wordpress.zip" -d "$BUILD/tmp-wp" && mv "$BUILD/tmp-wp/wordpress/"* "$SITE/" && rm -rf "$BUILD/tmp-wp"
unzip -q "$CACHE/sqlite.zip" -d "$SITE/wp-content/plugins/"
sed -e "s#{SQLITE_IMPLEMENTATION_FOLDER_PATH}#$SITE/wp-content/plugins/sqlite-database-integration#" \
    -e "s#{SQLITE_PLUGIN}#sqlite-database-integration/load.php#" \
    "$SITE/wp-content/plugins/sqlite-database-integration/db.copy" > "$SITE/wp-content/db.php"
WP="php -d memory_limit=512M $CACHE/wp-cli.phar --path=$SITE --quiet"
$WP config create --dbname=wp --dbuser=wp --dbpass=wp --skip-check
$WP config set WP_DEBUG true --raw; $WP config set WP_DEBUG_LOG true --raw; $WP config set WP_DEBUG_DISPLAY false --raw
$WP core install --url="$URL" --title="Bukvar Lite smoke" --admin_user=admin \
    --admin_password="$(head -c 18 /dev/urandom | base64)" --admin_email=admin@example.test --skip-email
$WP rewrite structure '/%postname%/' --hard
VERSION="$($WP core version)"
fail() { echo "FAIL: $*" >&2; exit 1; }
check() { [ "$2" = "$3" ] || fail "$1: expected '$3', got '$2'"; echo "ok   $1: $2"; }

OLD=$($WP post create --post_type=page --post_title='За нас' --post_status=publish --porcelain)
OLD_SLUG=$($WP post get "$OLD" --field=post_name)
$WP plugin install "$ZIP" --activate
check "existing slug untouched" "$($WP post get "$OLD" --field=post_name)" "$OLD_SLUG"
P1=$($WP post create --post_title='Как да изберем щори за кухнята' --post_status=publish --porcelain)
check "new post slug" "$($WP post get "$P1" --field=post_name)" "kak-da-izberem-shtori-za-kuhnyata"
P2=$($WP post create --post_title='София, България' --post_status=publish --porcelain)
check "law rules (ия, state name)" "$($WP post get "$P2" --field=post_name)" "sofia-bulgaria"
T1=$($WP term create post_tag 'Щастливец' --porcelain)
check "new term slug" "$($WP term get post_tag "$T1" --field=slug)" "shtastlivets"
php -r 'file_put_contents($argv[1], base64_decode("iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=="));' "$BUILD/Снимка на Пловдив.png"
A1=$($WP media import "$BUILD/Снимка на Пловдив.png" --porcelain)
check "upload file name" "$(basename "$($WP post meta get "$A1" _wp_attached_file)")" "snimka-na-plovdiv.png"
$WP option update bukvar_lite_settings '{"slugs":false,"files":true,"files_lowercase":true}' --format=json
POFF=$($WP post create --post_title='Без транслитерация' --post_status=draft --post_name='без-транслитерация' --porcelain)
case "$($WP post get "$POFF" --field=post_name)" in %d0*) echo "ok   slugs switch off keeps Cyrillic";; *) fail "slugs switch ignored";; esac
$WP option delete bukvar_lite_settings

cat > "$BUILD/router.php" <<'PHP'
<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path !== '/' && is_file($_SERVER['DOCUMENT_ROOT'] . urldecode($path))) { return false; }
require $_SERVER['DOCUMENT_ROOT'] . '/index.php';
PHP
if curl -s -o /dev/null "$URL/"; then fail "port $PORT is in use; run with PORT=<free port>"; fi
(cd "$SITE" && exec php -S 127.0.0.1:$PORT "$BUILD/router.php" >"$BUILD/server.log" 2>&1) &
SERVER=$!
trap 'kill $SERVER 2>/dev/null || true' EXIT
for _ in $(seq 1 50); do curl -s -o /dev/null "$URL/" && break; sleep 0.1; done
check "existing Cyrillic page still 200" "$(curl -s -o /dev/null -w '%{http_code}' "$URL/$OLD_SLUG/")" "200"
check "new Latin post 200" "$(curl -s -o /dev/null -w '%{http_code}' "$URL/sofia-bulgaria/")" "200"
COOKIE="$($WP eval 'echo LOGGED_IN_COOKIE."=".wp_generate_auth_cookie(1, time()+600, "logged_in")."; ".AUTH_COOKIE."=".wp_generate_auth_cookie(1, time()+600, "auth");')"
curl -s -b "$COOKIE" "$URL/wp-admin/options-permalink.php" > "$BUILD/permalink.html"
grep -q 'id="bukvar_lite_slugs"' "$BUILD/permalink.html" || fail "settings fields missing on Settings > Permalinks"
echo "ok   three switches on Settings > Permalinks"
NONCE=$(sed -n 's/.*name="_wpnonce" value="\([a-f0-9]*\)".*/\1/p' "$BUILD/permalink.html" | head -1)
curl -s -o /dev/null -b "$COOKIE" --data-urlencode "_wpnonce=$NONCE" --data-urlencode "_wp_http_referer=/wp-admin/options-permalink.php" \
  --data-urlencode "permalink_structure=/%postname%/" --data-urlencode "selection=/%postname%/" \
  --data-urlencode "bukvar_lite_settings[slugs]=1" --data-urlencode "bukvar_lite_settings[files]=1" --data-urlencode "bukvar_lite_settings[files_lowercase]=0" \
  "$URL/wp-admin/options-permalink.php"
check "settings saved from the Permalinks form" "$($WP option get bukvar_lite_settings --format=json)" '{"slugs":true,"files":true,"files_lowercase":false}'
check "plugins screen 200" "$(curl -s -o /dev/null -w '%{http_code}' -b "$COOKIE" "$URL/wp-admin/plugins.php")" "200"
if [ -f "$SITE/wp-content/debug.log" ] && grep 'plugins/bukvar-lite/' "$SITE/wp-content/debug.log"; then fail "PHP notices from Bukvar Lite"; fi
echo "ok   no PHP notices or warnings from Bukvar Lite"
$WP plugin uninstall bukvar-lite --deactivate
check "uninstall removes settings" "$($WP option get bukvar_lite_settings 2>/dev/null || echo gone)" "gone"
check "uninstall keeps Latin slugs" "$($WP post get "$P2" --field=post_name)" "sofia-bulgaria"
echo "SMOKE OK on WordPress $VERSION (SQLite), PHP $(php -r 'echo PHP_VERSION;')"
