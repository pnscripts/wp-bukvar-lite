<?php
/**
 * Minimal WordPress stand-ins so Plugin's filters can run without WordPress.
 *
 * @package BukvarLite
 */

declare(strict_types=1);

require dirname( __DIR__ ) . '/vendor/autoload.php';

define( 'ABSPATH', __DIR__ . '/' );
define( 'BUKVAR_LITE_FILE', dirname( __DIR__ ) . '/bukvar-lite.php' );

$GLOBALS['bukvar_lite_test'] = array( 'options' => array(), 'hooks' => array() );

function get_option( $name, $default = false ) {
	return $GLOBALS['bukvar_lite_test']['options'][ $name ] ?? $default;
}
function apply_filters( $hook, $value ) {
	return $value;
}
function add_filter( $hook, $callback, $priority = 10, $args = 1 ) {
	$GLOBALS['bukvar_lite_test']['hooks'][] = array( $hook, $priority );
	return true;
}
function add_action( $hook, $callback, $priority = 10, $args = 1 ) {
	return add_filter( $hook, $callback, $priority, $args );
}
function is_admin() {
	return false;
}
function plugin_basename( $file ) {
	return 'bukvar-lite/' . basename( $file );
}
