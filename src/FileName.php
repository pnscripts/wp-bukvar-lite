<?php
/**
 * Upload file name cleaning. Pure PHP, no WordPress calls.
 *
 * @package BukvarLite
 */

declare(strict_types=1);

namespace BukvarLite;

final class FileName {

	/**
	 * "Снимка на София 2026.JPG" -> "snimka-na-sofia-2026.jpg" (lower case on)
	 * or "Snimka-na-Sofia-2026.JPG" (lower case off).
	 * Names without Cyrillic letters are returned unchanged.
	 */
	public static function clean( string $name, Transliterator $transliterator, bool $lowercase = true ): string {
		if ( ! Transliterator::containsCyrillic( $name ) ) {
			return $name;
		}
		$dot       = strrpos( $name, '.' );
		$base      = false === $dot ? $name : substr( $name, 0, $dot );
		$extension = false === $dot ? '' : substr( $name, $dot + 1 );

		$base = $transliterator->transliterate( $base );
		$base = (string) preg_replace( '/[^A-Za-z0-9._-]+/', '-', $base );
		$base = (string) preg_replace( '/-{2,}/', '-', $base );
		$base = trim( $base, '-._' );
		if ( '' === $base ) {
			$base = 'file';
		}
		$extension = (string) preg_replace( '/[^A-Za-z0-9]+/', '', $transliterator->transliterate( $extension ) );

		if ( $lowercase ) {
			$base      = strtolower( $base );
			$extension = strtolower( $extension );
		}
		return '' === $extension ? $base : $base . '.' . $extension;
	}
}
