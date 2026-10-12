<?php
/**
 * Transliteration tables. Pure PHP, no WordPress calls.
 *
 * @package BukvarLite
 */

declare(strict_types=1);

namespace BukvarLite;

final class Tables {

	/**
	 * Bulgarian: the Streamlined System of the Transliteration Act
	 * (Закон за транслитерацията, ДВ бр. 19/2009), lower case.
	 * The word-final "ия" -> "ia" rule is applied by the Transliterator.
	 *
	 * @return array<string, string>
	 */
	public static function bulgarian(): array {
		return array(
			'а' => 'a',
			'б' => 'b',
			'в' => 'v',
			'г' => 'g',
			'д' => 'd',
			'е' => 'e',
			'ж' => 'zh',
			'з' => 'z',
			'и' => 'i',
			'й' => 'y',
			'к' => 'k',
			'л' => 'l',
			'м' => 'm',
			'н' => 'n',
			'о' => 'o',
			'п' => 'p',
			'р' => 'r',
			'с' => 's',
			'т' => 't',
			'у' => 'u',
			'ф' => 'f',
			'х' => 'h',
			'ц' => 'ts',
			'ч' => 'ch',
			'ш' => 'sh',
			'щ' => 'sht',
			'ъ' => 'a',
			'ь' => 'y',
			'ю' => 'yu',
			'я' => 'ya',
		);
	}

	/**
	 * Russian: a URL-friendly romanisation close to BGN/PCGN without
	 * diacritics (х -> kh, щ -> shch, ъ and ь dropped).
	 *
	 * @return array<string, string>
	 */
	public static function russian(): array {
		return array(
			'а' => 'a',
			'б' => 'b',
			'в' => 'v',
			'г' => 'g',
			'д' => 'd',
			'е' => 'e',
			'ё' => 'yo',
			'ж' => 'zh',
			'з' => 'z',
			'и' => 'i',
			'й' => 'y',
			'к' => 'k',
			'л' => 'l',
			'м' => 'm',
			'н' => 'n',
			'о' => 'o',
			'п' => 'p',
			'р' => 'r',
			'с' => 's',
			'т' => 't',
			'у' => 'u',
			'ф' => 'f',
			'х' => 'kh',
			'ц' => 'ts',
			'ч' => 'ch',
			'ш' => 'sh',
			'щ' => 'shch',
			'ъ' => '',
			'ы' => 'y',
			'ь' => '',
			'э' => 'e',
			'ю' => 'yu',
			'я' => 'ya',
		);
	}

	/**
	 * Ukrainian: the national system of the Cabinet of Ministers
	 * Resolution No. 55 (2010). Position-dependent letters (є, ї, й, ю, я)
	 * use the "inside the word" value here; the word-initial value is in
	 * ukrainianInitial(). "зг" -> "zgh" is applied by the Transliterator.
	 *
	 * @return array<string, string>
	 */
	public static function ukrainian(): array {
		return array(
			'а' => 'a',
			'б' => 'b',
			'в' => 'v',
			'г' => 'h',
			'ґ' => 'g',
			'д' => 'd',
			'е' => 'e',
			'є' => 'ie',
			'ж' => 'zh',
			'з' => 'z',
			'и' => 'y',
			'і' => 'i',
			'ї' => 'i',
			'й' => 'i',
			'к' => 'k',
			'л' => 'l',
			'м' => 'm',
			'н' => 'n',
			'о' => 'o',
			'п' => 'p',
			'р' => 'r',
			'с' => 's',
			'т' => 't',
			'у' => 'u',
			'ф' => 'f',
			'х' => 'kh',
			'ц' => 'ts',
			'ч' => 'ch',
			'ш' => 'sh',
			'щ' => 'shch',
			'ь' => '',
			'ю' => 'iu',
			'я' => 'ia',
		);
	}

	/**
	 * Ukrainian letters spelled differently at the start of a word.
	 *
	 * @return array<string, string>
	 */
	public static function ukrainianInitial(): array {
		return array(
			'є' => 'ye',
			'ї' => 'yi',
			'й' => 'y',
			'ю' => 'yu',
			'я' => 'ya',
		);
	}

	/**
	 * Letters found only in Russian (compared with Bulgarian and Ukrainian).
	 *
	 * @return list<string>
	 */
	public static function russianOnly(): array {
		return array( 'ё', 'ы', 'э' );
	}

	/**
	 * Letters found only in Ukrainian.
	 *
	 * @return list<string>
	 */
	public static function ukrainianOnly(): array {
		return array( 'є', 'і', 'ї', 'ґ' );
	}

	/**
	 * Last-resort values for Cyrillic letters outside the active alphabet,
	 * so that no Cyrillic letter is ever left in a slug.
	 *
	 * @return array<string, string>
	 */
	public static function fallback(): array {
		return array(
			'ё' => 'yo',
			'ы' => 'y',
			'э' => 'e',
			'є' => 'ye',
			'і' => 'i',
			'ї' => 'yi',
			'ґ' => 'g',
			'ў' => 'u',
			'ј' => 'j',
			'љ' => 'lj',
			'њ' => 'nj',
			'ђ' => 'dj',
			'ћ' => 'c',
			'џ' => 'dz',
			'ѓ' => 'gj',
			'ќ' => 'kj',
			'ѕ' => 'dz',
		);
	}
}
