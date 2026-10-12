<?php
/**
 * Plugin filters with stubbed WordPress functions.
 *
 * @package BukvarLite
 */

declare(strict_types=1);

namespace BukvarLite\Tests;

use BukvarLite\Plugin;
use PHPUnit\Framework\TestCase;

final class PluginTest extends TestCase {

	protected function setUp(): void {
		$GLOBALS['bukvar_lite_test']['options'] = array();
	}

	public function test_boot_hooks_the_two_filters_before_wordpress_sanitises(): void {
		Plugin::boot();
		$this->assertContains( array( 'sanitize_title', 9 ), $GLOBALS['bukvar_lite_test']['hooks'] );
		$this->assertContains( array( 'sanitize_file_name', 9 ), $GLOBALS['bukvar_lite_test']['hooks'] );
	}

	public function test_new_slugs_are_latin_only_in_the_save_context(): void {
		$this->assertSame( 'Za nas', Plugin::filterTitle( 'За нас', 'За нас', 'save' ) );
		$this->assertSame( 'Sofia', Plugin::filterTitle( 'София' ) );
		$this->assertSame( 'За нас', Plugin::filterTitle( 'За нас', 'За нас', 'query' ) );
		$this->assertSame( 'already latin', Plugin::filterTitle( 'already latin' ) );
	}

	public function test_lookups_of_existing_cyrillic_slugs_are_left_alone_while_resolving(): void {
		Plugin::startResolving();
		$this->assertSame( 'софия', Plugin::filterTitle( 'софия' ) );
		Plugin::stopResolving();
		$this->assertSame( 'sofia', Plugin::filterTitle( 'софия' ) );
	}

	public function test_file_names_follow_the_switches(): void {
		$this->assertSame( 'snimka-na-plovdiv.png', Plugin::filterFileName( 'Снимка на Пловдив.png' ) );
		$GLOBALS['bukvar_lite_test']['options'][ Plugin::OPTION ] = array( 'files_lowercase' => false );
		$this->assertSame( 'Snimka-na-Plovdiv.png', Plugin::filterFileName( 'Снимка на Пловдив.png' ) );
		$GLOBALS['bukvar_lite_test']['options'][ Plugin::OPTION ] = array( 'files' => false, 'slugs' => false );
		$this->assertSame( 'Снимка.png', Plugin::filterFileName( 'Снимка.png' ) );
		$this->assertSame( 'София', Plugin::filterTitle( 'София' ) );
	}

	public function test_settings_are_booleans_and_unknown_keys_are_dropped(): void {
		$GLOBALS['bukvar_lite_test']['options'][ Plugin::OPTION ] = array( 'slugs' => '0', 'evil' => 'x' );
		$this->assertSame( array( 'slugs' => false, 'files' => true, 'files_lowercase' => true ), Plugin::settings() );
		$this->assertSame( array( 'slugs' => true, 'files' => false, 'files_lowercase' => false ), Plugin::sanitize( array( 'slugs' => '1', 'files' => '0' ) ) );
		$this->assertSame( array( 'slugs' => false, 'files' => false, 'files_lowercase' => false ), Plugin::sanitize( 'junk' ) );
	}
}
