=== Bukvar Lite by PN Scripts ===
Contributors: pnscripts
Tags: transliteration, cyrillic, slug, bulgarian, permalink
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

New Bulgarian Cyrillic slugs and upload file names in Latin, by the official Bulgarian Transliteration Act: София becomes sofia.

== Description ==

WordPress keeps Cyrillic slugs as long percent-encoded addresses such as `/%d0%b7%d0%b0-%d0%bd%d0%b0%d1%81/`. Bukvar Lite writes them in Latin the way the Bulgarian Transliteration Act (Закон за транслитерацията, 2009) prescribes: `/za-nas/`.

= What it does =

* Post, page, custom post type, term and attachment slugs are written in Latin when they are saved.
* Uploaded file names are written in Latin too: `Снимка на Пловдив.png` becomes `snimka-na-plovdiv.png` (lower case can be switched off).
* The Streamlined System of the Act: щ is sht, ъ is a, ю is yu, я is ya, and "ия" at the end of a word is ia (София is Sofia). The state name България stays Bulgaria. Capitals follow the word: Щастие is Shtastie.
* Other Cyrillic letters (Russian, Ukrainian, Serbian) never stay in a slug: they get a plain Latin form.
* Three switches on Settings, Permalinks. No other screens, no notices, no tracking.

= What it does not do =

* Existing slugs are not changed. Only content saved after activation gets Latin slugs, so no address of your site changes on its own.
* Files already on disk keep their names; only new uploads are renamed.
* It transliterates letter by letter; it does not translate.

The full edition, Bukvar, adds a batch converter for existing slugs with a dry run and 301 redirects, Russian and Ukrainian tables and WP-CLI commands. It is a separate plugin; Bukvar Lite is complete on its own and never asks for payment.

== Installation ==

1. Install from Plugins, Add New (search "Bukvar Lite"), or upload the `bukvar-lite` folder to `/wp-content/plugins/`.
2. Activate it. New slugs and uploads are written in Latin from then on.
3. Optional: Settings, Permalinks, section "Latin slugs and file names", to switch slugs, file names or lower case off.

== Frequently Asked Questions ==

= Can I add my own exceptions? =

Yes, with the `bukvar_lite_exceptions` filter: an array of whole Cyrillic words to their Latin form.

= Will my existing addresses change? =

No. Only slugs saved after activation are written in Latin. Lookups of existing Cyrillic slugs keep working.

= What happens when I delete the plugin? =

Its settings are removed. Slugs and file names it wrote stay Latin.

= Does it work with WooCommerce? =

Products, product categories, tags and attribute terms are ordinary posts and terms, so their new slugs are written in Latin the same way.

== Screenshots ==

1. A Bulgarian page title and the Latin slug Bukvar Lite writes for it.
2. The three switches on Settings, Permalinks.
3. Transliteration rules of the Streamlined System.

== Changelog ==

= 1.0.0 =
* First release.
