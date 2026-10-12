<?php
/**
 * Removes Bukvar Lite's settings. Slugs and file names it wrote stay Latin.
 *
 * @package BukvarLite
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'bukvar_lite_settings' );
