<?php
/**
 * Plugin Name:       Bukvar Lite by PN Scripts
 * Plugin URI:        https://pnscripts.com/marketplace/bukvar
 * Description:       Writes new Bulgarian Cyrillic slugs and upload file names in Latin by the Bulgarian Transliteration Act (София to sofia).
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            PN Scripts
 * Author URI:        https://pnscripts.com
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       bukvar-lite
 *
 * @package BukvarLite
 */

defined( 'ABSPATH' ) || exit;

define( 'BUKVAR_LITE_VERSION', '1.0.0' );
define( 'BUKVAR_LITE_FILE', __FILE__ );

spl_autoload_register(
	static function ( string $class ): void {
		if ( 0 !== strpos( $class, 'BukvarLite\\' ) ) {
			return;
		}
		$path = __DIR__ . '/src/' . str_replace( '\\', '/', substr( $class, 11 ) ) . '.php';
		if ( is_readable( $path ) ) {
			require $path;
		}
	}
);

BukvarLite\Plugin::boot();
