#!/usr/bin/env bash
# Builds dist/bukvar-lite-<version>.zip (the installable plugin, no dev files)
# and dist/wporg-assets/ (the files for the SVN /assets folder).
set -euo pipefail
ROOT="$(cd "$(dirname "$0")" && pwd)"
VERSION="$(sed -n 's/^ \* Version: *//p' "$ROOT/bukvar-lite.php" | tr -d '[:space:]')"
STABLE="$(sed -n 's/^Stable tag: *//p' "$ROOT/readme.txt" | tr -d '[:space:]')"
[ "$VERSION" = "$STABLE" ] || { echo "Version $VERSION and Stable tag $STABLE differ" >&2; exit 1; }
STAGE="$ROOT/build/stage/bukvar-lite"
rm -rf "$ROOT/build/stage" && mkdir -p "$STAGE" "$ROOT/dist"
cp -r "$ROOT/bukvar-lite.php" "$ROOT/readme.txt" "$ROOT/LICENSE" "$ROOT/uninstall.php" "$ROOT/src" "$STAGE/"
rm -f "$ROOT/dist/bukvar-lite-$VERSION.zip"
(cd "$ROOT/build/stage" && zip -qr "$ROOT/dist/bukvar-lite-$VERSION.zip" bukvar-lite)
rm -rf "$ROOT/dist/wporg-assets" && cp -r "$ROOT/assets" "$ROOT/dist/wporg-assets"
echo "dist/bukvar-lite-$VERSION.zip"
