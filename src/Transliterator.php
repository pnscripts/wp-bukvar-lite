<?php
/**
 * Cyrillic to Latin transliteration. Pure PHP (needs mbstring), no WordPress calls.
 *
 * @package BukvarLite
 */

declare(strict_types=1);

namespace BukvarLite;

final class Transliterator {

	public const BULGARIAN = 'bg';
	public const RUSSIAN   = 'ru';
	public const UKRAINIAN = 'uk';

	/** @var string */
	private $default;

	/** @var array<string, bool> */
	private $enabled;

	/** @var array<string, string> lower-case word => Latin */
	private $exceptions = array();

	/**
	 * @param string               $default    Alphabet used for words that do not reveal their language.
	 * @param list<string>         $extra      Extra alphabets recognised by their own letters (ru, uk).
	 * @param array<string,string>|null $exceptions Whole-word exceptions; null uses the defaults.
	 */
	public function __construct( string $default = self::BULGARIAN, array $extra = array(), ?array $exceptions = null ) {
		if ( ! in_array( $default, array( self::BULGARIAN, self::RUSSIAN, self::UKRAINIAN ), true ) ) {
			throw new \InvalidArgumentException( 'Unknown alphabet: ' . $default );
		}
		$this->default = $default;
		$this->enabled = array( $default => true );
		foreach ( $extra as $alphabet ) {
			if ( in_array( $alphabet, array( self::RUSSIAN, self::UKRAINIAN, self::BULGARIAN ), true ) ) {
				$this->enabled[ $alphabet ] = true;
			}
		}
		foreach ( $exceptions ?? self::defaultExceptions() as $word => $latin ) {
			$this->exceptions[ mb_strtolower( (string) $word, 'UTF-8' ) ] = (string) $latin;
		}
	}

	/**
	 * The name of the state keeps its established Latin form "Bulgaria"
	 * instead of the letter-by-letter "Balgaria".
	 *
	 * @return array<string, string>
	 */
	public static function defaultExceptions(): array {
		return array( 'България' => 'Bulgaria' );
	}

	public static function containsCyrillic( string $text ): bool {
		return 1 === preg_match( '/\p{Cyrillic}/u', $text );
	}

	/**
	 * Which alphabet a single word is read in.
	 */
	public function detect( string $word ): string {
		$lower = mb_strtolower( $word, 'UTF-8' );
		if ( isset( $this->enabled[ self::UKRAINIAN ] ) && self::hasAny( $lower, Tables::ukrainianOnly() ) ) {
			return self::UKRAINIAN;
		}
		if ( isset( $this->enabled[ self::RUSSIAN ] ) && self::hasAny( $lower, Tables::russianOnly() ) ) {
			return self::RUSSIAN;
		}
		return $this->default;
	}

	/**
	 * Transliterates every Cyrillic word in $text; everything else is kept as it is.
	 */
	public function transliterate( string $text ): string {
		if ( '' === $text || ! self::containsCyrillic( $text ) ) {
			return $text;
		}
		if ( isset( $this->enabled[ self::UKRAINIAN ] ) ) {
			// Ukrainian apostrophes between letters are not transliterated.
			$text = (string) preg_replace( "/(?<=\\p{Cyrillic})['\x{2019}\x{02BC}](?=\\p{Cyrillic})/u", '', $text );
		}
		$parts = preg_split( '/(\p{L}+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE );
		if ( false === $parts ) {
			return $text;
		}
		$out = '';
		foreach ( $parts as $i => $part ) {
			$out .= ( 1 === $i % 2 && self::containsCyrillic( $part ) ) ? $this->word( $part ) : $part;
		}
		return $out;
	}

	private function word( string $word ): string {
		$letters = mb_str_split( $word, 1, 'UTF-8' );
		$lower   = mb_strtolower( $word, 'UTF-8' );
		$caps    = self::isAllCaps( $letters );

		if ( isset( $this->exceptions[ $lower ] ) ) {
			return self::applyCase( $this->exceptions[ $lower ], $word, $caps );
		}

		$alphabet = $this->detect( $word );
		$table    = self::table( $alphabet );
		$initial  = self::UKRAINIAN === $alphabet ? Tables::ukrainianInitial() : array();
		$fallback = Tables::fallback();
		$count    = count( $letters );
		$out      = '';

		for ( $i = 0; $i < $count; $i++ ) {
			$char = $letters[ $i ];
			$low  = mb_strtolower( $char, 'UTF-8' );
			$next = $i + 1 < $count ? mb_strtolower( $letters[ $i + 1 ], 'UTF-8' ) : '';

			if ( self::BULGARIAN === $alphabet && 'и' === $low && 'я' === $next && $i + 2 === $count ) {
				// Word-final "ия" is written "ia" (София -> Sofia).
				$out .= self::caseLike( 'i', $char, $caps ) . self::caseLike( 'a', $letters[ $i + 1 ], $caps );
				break;
			}
			if ( self::UKRAINIAN === $alphabet && 'з' === $low && 'г' === $next ) {
				// "зг" is "zgh" so that it is not read as "ж".
				$out .= self::caseLike( 'z', $char, $caps ) . self::caseLike( 'gh', $letters[ $i + 1 ], $caps );
				++$i;
				continue;
			}

			if ( 0 === $i && isset( $initial[ $low ] ) ) {
				$latin = $initial[ $low ];
			} elseif ( isset( $table[ $low ] ) ) {
				$latin = $table[ $low ];
			} elseif ( isset( $fallback[ $low ] ) ) {
				$latin = $fallback[ $low ];
			} else {
				$latin = $char;
			}
			$out .= ( $latin === $char ) ? $char : self::caseLike( $latin, $char, $caps );
		}
		return $out;
	}

	/**
	 * @return array<string, string>
	 */
	private static function table( string $alphabet ): array {
		static $cache = array();
		if ( ! isset( $cache[ $alphabet ] ) ) {
			switch ( $alphabet ) {
				case self::RUSSIAN:
					$cache[ $alphabet ] = Tables::russian();
					break;
				case self::UKRAINIAN:
					$cache[ $alphabet ] = Tables::ukrainian();
					break;
				default:
					$cache[ $alphabet ] = Tables::bulgarian();
			}
		}
		return $cache[ $alphabet ];
	}

	/**
	 * "Щ" in "Щастие" becomes "Sht"; in "ЩАСТИЕ" it becomes "SHT".
	 */
	private static function caseLike( string $latin, string $source, bool $caps ): string {
		if ( '' === $latin || mb_strtolower( $source, 'UTF-8' ) === $source ) {
			return $latin;
		}
		return $caps ? strtoupper( $latin ) : ucfirst( $latin );
	}

	private static function applyCase( string $latin, string $word, bool $caps ): string {
		if ( $caps ) {
			return strtoupper( $latin );
		}
		if ( mb_strtolower( $word, 'UTF-8' ) === $word ) {
			return strtolower( $latin );
		}
		return $latin;
	}

	/**
	 * A word counts as written in capitals when it has two or more letters and no lower-case letter.
	 *
	 * @param list<string> $letters
	 */
	private static function isAllCaps( array $letters ): bool {
		if ( count( $letters ) < 2 ) {
			return false;
		}
		foreach ( $letters as $letter ) {
			if ( mb_strtoupper( $letter, 'UTF-8' ) !== $letter ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * @param list<string> $needles
	 */
	private static function hasAny( string $haystack, array $needles ): bool {
		foreach ( $needles as $needle ) {
			if ( false !== mb_strpos( $haystack, $needle, 0, 'UTF-8' ) ) {
				return true;
			}
		}
		return false;
	}
}
