<?php

declare(strict_types=1);

namespace BukvarLite\Tests;

use BukvarLite\FileName;
use BukvarLite\Transliterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TransliteratorTest extends TestCase {

	/**
	 * Every letter of the Bulgarian alphabet, with a place or personal name.
	 *
	 * @return array<string, array{string, string}>
	 */
	public static function bulgarianNames(): array {
		return array(
			'а' => array( 'Атанас', 'Atanas' ),
			'б' => array( 'Борис', 'Boris' ),
			'в' => array( 'Враца', 'Vratsa' ),
			'г' => array( 'Габрово', 'Gabrovo' ),
			'д' => array( 'Димитър', 'Dimitar' ),
			'е' => array( 'Елена', 'Elena' ),
			'ж' => array( 'Жеравна', 'Zheravna' ),
			'з' => array( 'Златоград', 'Zlatograd' ),
			'и' => array( 'Искър', 'Iskar' ),
			'й' => array( 'Йордан', 'Yordan' ),
			'к' => array( 'Карлово', 'Karlovo' ),
			'л' => array( 'Ловеч', 'Lovech' ),
			'м' => array( 'Мадара', 'Madara' ),
			'н' => array( 'Нели', 'Neli' ),
			'о' => array( 'Огняново', 'Ognyanovo' ),
			'п' => array( 'Пловдив', 'Plovdiv' ),
			'р' => array( 'Рилски', 'Rilski' ),
			'с' => array( 'София', 'Sofia' ),
			'т' => array( 'Търговище', 'Targovishte' ),
			'у' => array( 'Улица', 'Ulitsa' ),
			'ф' => array( 'Филипово', 'Filipovo' ),
			'х' => array( 'Хасково', 'Haskovo' ),
			'ц' => array( 'Цветелина', 'Tsvetelina' ),
			'ч' => array( 'Чавдар', 'Chavdar' ),
			'ш' => array( 'Шумен', 'Shumen' ),
			'щ' => array( 'Щастливец', 'Shtastlivets' ),
			'ъ' => array( 'Тъмно', 'Tamno' ),
			'ь' => array( 'Шьонберг', 'Shyonberg' ),
			'ю' => array( 'Юрий', 'Yuriy' ),
			'я' => array( 'Ямбол', 'Yambol' ),
		);
	}

	#[DataProvider( 'bulgarianNames' )]
	public function test_bulgarian_letters( string $cyrillic, string $latin ): void {
		$this->assertSame( $latin, ( new Transliterator() )->transliterate( $cyrillic ) );
	}

	/**
	 * @return array<string, array{string, string}>
	 */
	public static function bulgarianRules(): array {
		return array(
			'state name exception'           => array( 'България', 'Bulgaria' ),
			'state name in lower case'       => array( 'българия', 'bulgaria' ),
			'state name in capitals'         => array( 'БЪЛГАРИЯ', 'BULGARIA' ),
			'derived word follows the table' => array( 'български', 'balgarski' ),
			'final ия'                       => array( 'Мария', 'Maria' ),
			'final ия, Илия'                 => array( 'Илия', 'Ilia' ),
			'ия inside a word'               => array( 'Ияша', 'Iyasha' ),
			'ия before a suffix'             => array( 'Софиянец', 'Sofiyanets' ),
			'final ия before punctuation'    => array( 'в София, днес', 'v Sofia, dnes' ),
			'final ИЯ in capitals'           => array( 'СОФИЯ', 'SOFIA' ),
			'capital Щ in a title'           => array( 'Щастие', 'Shtastie' ),
			'Щ in capitals'                  => array( 'ЩАСТИЕ', 'SHTASTIE' ),
			'single capital letter'          => array( 'Ж', 'Zh' ),
			'capital Ц, lower rest'          => array( 'Цар', 'Tsar' ),
			'Ю at start'                     => array( 'Юлиана', 'Yuliana' ),
			'ьо'                             => array( 'Гьоте', 'Gyote' ),
			'ъ in the middle'                => array( 'Бургас и Търново', 'Burgas i Tarnovo' ),
			'mixed with Latin and digits'    => array( 'WordPress за 5 минути', 'WordPress za 5 minuti' ),
			'hyphenated'                     => array( 'Стара-Загора', 'Stara-Zagora' ),
			'sentence'                       => array( 'Как да изберем щори за кухнята', 'Kak da izberem shtori za kuhnyata' ),
			'щ in the middle'                => array( 'пещера', 'peshtera' ),
			'й at the end'                   => array( 'Николай', 'Nikolay' ),
			'ч ш ж together'                 => array( 'чашка жито', 'chashka zhito' ),
			'Latin only is untouched'        => array( 'Hello, world', 'Hello, world' ),
			'empty'                          => array( '', '' ),
		);
	}

	#[DataProvider( 'bulgarianRules' )]
	public function test_bulgarian_rules( string $cyrillic, string $latin ): void {
		$this->assertSame( $latin, ( new Transliterator() )->transliterate( $cyrillic ) );
	}

	public function test_exceptions_can_be_replaced(): void {
		$t = new Transliterator( 'bg', array(), array( 'Пловдив' => 'Philippopolis' ) );
		$this->assertSame( 'Balgaria', $t->transliterate( 'България' ) );
		$this->assertSame( 'philippopolis', $t->transliterate( 'пловдив' ) );
	}

	/**
	 * @return array<string, array{string, string}>
	 */
	public static function russianWords(): array {
		return array(
			'ы and щ'       => array( 'Щы', 'Shchy' ),
			'Москва'        => array( 'Москва', 'Moskva' ),
			'х is kh'       => array( 'Хабаровск', 'Khabarovsk' ),
			'ъ and ь drop'  => array( 'объявление', 'obyavlenie' ),
			'э'             => array( 'Эрмитаж', 'Ermitazh' ),
			'ё'             => array( 'ёлка', 'yolka' ),
			'final ия stays' => array( 'Россия', 'Rossiya' ),
		);
	}

	#[DataProvider( 'russianWords' )]
	public function test_russian_as_default( string $cyrillic, string $latin ): void {
		$this->assertSame( $latin, ( new Transliterator( 'ru' ) )->transliterate( $cyrillic ) );
	}

	public function test_russian_words_recognised_on_a_bulgarian_site(): void {
		$t = new Transliterator( 'bg', array( 'ru' ) );
		// "ы" marks "хлебы" as Russian (х is kh); "щи" has no Russian-only letter and stays Bulgarian.
		$this->assertSame( 'shti i khleby', $t->transliterate( 'щи и хлебы' ) );
		$this->assertSame( 'Shchyuchy', $t->transliterate( 'Щючьы' ) );
		$this->assertSame( 'Elektrichestvo', $t->transliterate( 'Электричество' ) );
		// Words without Russian-only letters stay Bulgarian.
		$this->assertSame( 'Shtastie', $t->transliterate( 'Щастие' ) );
	}

	public function test_russian_letters_without_the_russian_table_still_become_latin(): void {
		$t = new Transliterator( 'bg' );
		$this->assertSame( 'hleby', $t->transliterate( 'хлебы' ) );
		$this->assertSame( 'yolka', $t->transliterate( 'ёлка' ) );
		$this->assertSame( 'Ermitazh', $t->transliterate( 'Эрмитаж' ) );
	}

	/**
	 * @return array<string, array{string, string}>
	 */
	public static function ukrainianWords(): array {
		return array(
			'Київ'                  => array( 'Київ', 'Kyiv' ),
			'Запоріжжя'             => array( 'Запоріжжя', 'Zaporizhzhia' ),
			'Згурівка (зг)'          => array( 'Згурівка', 'Zghurivka' ),
			'Розгон (зг inside)'     => array( 'Розгон', 'Rozghon' ),
			'Єнакієве (є initial)'   => array( 'Єнакієве', 'Yenakiieve' ),
			'Їжакевич (ї initial)'   => array( 'Їжакевич', 'Yizhakevych' ),
			'Йосипівка (й initial)'  => array( 'Йосипівка', 'Yosypivka' ),
			'Стрий (й final)'        => array( 'Стрий', 'Stryi' ),
			'Юрій'                  => array( 'Юрій', 'Yurii' ),
			'Яготин'                => array( 'Яготин', 'Yahotyn' ),
			'Костянтин'             => array( 'Костянтин', 'Kostiantyn' ),
			'Ґалаґан'               => array( 'Ґалаґан', 'Galagan' ),
			'Харків'                => array( 'Харків', 'Kharkiv' ),
			'Щербухи'               => array( 'Щербухи', 'Shcherbukhy' ),
			'апостроф'              => array( "П'ятихатки", 'Piatykhatky' ),
			'typographic apostrophe' => array( 'Короп’є', 'Koropie' ),
		);
	}

	#[DataProvider( 'ukrainianWords' )]
	public function test_ukrainian_as_default( string $cyrillic, string $latin ): void {
		$this->assertSame( $latin, ( new Transliterator( 'uk' ) )->transliterate( $cyrillic ) );
	}

	public function test_ukrainian_words_recognised_on_a_bulgarian_site(): void {
		$t = new Transliterator( 'bg', array( 'uk' ) );
		$this->assertSame( 'Kyiv i Sofia', $t->transliterate( 'Київ и София' ) );
		$this->assertSame( 'Kharkiv', $t->transliterate( 'Харків' ) );
	}

	public function test_detect(): void {
		$t = new Transliterator( 'bg', array( 'ru', 'uk' ) );
		$this->assertSame( 'bg', $t->detect( 'щастие' ) );
		$this->assertSame( 'ru', $t->detect( 'хлебы' ) );
		$this->assertSame( 'uk', $t->detect( 'Київ' ) );
	}

	public function test_unknown_default_alphabet_is_rejected(): void {
		$this->expectException( \InvalidArgumentException::class );
		new Transliterator( 'sr' );
	}

	public function test_no_cyrillic_letter_ever_survives(): void {
		$t   = new Transliterator( 'bg' );
		$all = 'абвгдежзийклмнопрстуфхцчшщъьюяёыэєіїґўјљњђћџѓќѕ';
		$out = $t->transliterate( $all . ' ' . mb_strtoupper( $all, 'UTF-8' ) );
		$this->assertFalse( Transliterator::containsCyrillic( $out ), $out );
	}

	public function test_contains_cyrillic(): void {
		$this->assertTrue( Transliterator::containsCyrillic( 'abc д' ) );
		$this->assertFalse( Transliterator::containsCyrillic( 'abc d' ) );
	}

	/**
	 * @return array<string, array{string, bool, string}>
	 */
	public static function fileNames(): array {
		return array(
			'photo lower case'      => array( 'Снимка на София 2026.JPG', true, 'snimka-na-sofia-2026.jpg' ),
			'photo keep case'       => array( 'Снимка на София 2026.JPG', false, 'Snimka-na-Sofia-2026.JPG' ),
			'no extension'          => array( 'Договор', true, 'dogovor' ),
			'multiple dots'         => array( 'отчет.финал.pdf', true, 'otchet.final.pdf' ),
			'punctuation collapses' => array( 'Цени (нови) — 2026!!.xlsx', true, 'tseni-novi-2026.xlsx' ),
			'Latin untouched'       => array( 'My Photo.JPG', true, 'My Photo.JPG' ),
			'only symbols left'     => array( '№.png', true, '№.png' ),
			'state name'            => array( 'Карта на България.png', true, 'karta-na-bulgaria.png' ),
		);
	}

	#[DataProvider( 'fileNames' )]
	public function test_file_names( string $name, bool $lower, string $expected ): void {
		$this->assertSame( $expected, FileName::clean( $name, new Transliterator(), $lower ) );
	}
}
