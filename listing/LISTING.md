# Bukvar Lite: WordPress.org submission

Nothing has been submitted. Account `pnscripts` exists on WordPress.org (2FA required for committers).

1. Build: `./build.sh` → `dist/bukvar-lite-1.0.0.zip`. Smoke test passed on WordPress 7.1.3 / PHP 8.4 (`tools/smoke.sh`).
2. Owner: https://wordpress.org/plugins/developers/add/ → upload the zip, accept the guidelines checkbox. Requested slug: `bukvar-lite` (from the plugin name "Bukvar Lite by PN Scripts"; ask the reviewer to keep this slug if they propose another).
3. Before uploading, run the official Plugin Check plugin (https://wordpress.org/plugins/plugin-check/) on a test site: the upload form runs it too.
4. After approval: SVN `trunk/` = contents of the zip, `tags/1.0.0/` = same, `assets/` = `dist/wporg-assets/` (banners, icons, screenshot-1..3.png that match the three `== Screenshots ==` captions).

Guideline check (detailed guidelines, last updated 2026-03-11):
- GPL-3.0-or-later code, no third-party code (g1, g2).
- Complete in its scope: no locked features, no trial, no licence key (g5); the full edition is mentioned once in readme.txt, no admin upsell notices at all (g11).
- No external calls, no tracking, no remote assets (g6–g8); no front-end credit links (g10).
- 5 tags, no competitor tags (g12). Name does not start with a trademark (g17).
