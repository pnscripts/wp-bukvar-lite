<?php
/**
 * WordPress wiring: two filters and three switches on Settings > Permalinks.
 *
 * @package BukvarLite
 */

declare(strict_types=1);

namespace BukvarLite;

defined( 'ABSPATH' ) || exit;

final class Plugin {

	public const OPTION = 'bukvar_lite_settings';

	/** @var Transliterator|null */
	private static $transliterator;

	/** @var bool True while WordPress resolves the front-end request into a query. */
	private static $resolving = false;

	public static function boot(): void {
		add_filter( 'sanitize_title', array( self::class, 'filterTitle' ), 9, 3 );
		add_filter( 'sanitize_file_name', array( self::class, 'filterFileName' ), 9, 2 );
		// Term lookups by slug run through sanitize_title() in the "save" context.
		// While the main query is resolved they must see the slug as stored, or
		// archives of terms that still have Cyrillic slugs would answer 404.
		add_action( 'parse_request', array( self::class, 'startResolving' ), 20 );
		add_action( 'wp', array( self::class, 'stopResolving' ), PHP_INT_MIN );
		add_action( 'admin_init', array( self::class, 'registerSettings' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( BUKVAR_LITE_FILE ), array( self::class, 'actionLinks' ) );
	}

	/**
	 * @return array{slugs: bool, files: bool, files_lowercase: bool}
	 */
	public static function settings(): array {
		$defaults = array(
			'slugs'           => true,
			'files'           => true,
			'files_lowercase' => true,
		);
		$saved    = get_option( self::OPTION, array() );
		$settings = array_merge( $defaults, is_array( $saved ) ? array_intersect_key( $saved, $defaults ) : array() );
		return array_map( 'boolval', $settings );
	}

	/**
	 * @param mixed $input
	 * @return array{slugs: bool, files: bool, files_lowercase: bool}
	 */
	public static function sanitize( $input ): array {
		$input = is_array( $input ) ? $input : array();
		return array(
			'slugs'           => ! empty( $input['slugs'] ),
			'files'           => ! empty( $input['files'] ),
			'files_lowercase' => ! empty( $input['files_lowercase'] ),
		);
	}

	public static function transliterator(): Transliterator {
		if ( null === self::$transliterator ) {
			/**
			 * Whole-word exceptions, Cyrillic => Latin. The default keeps "България" as "Bulgaria".
			 *
			 * @param array<string, string> $exceptions
			 */
			$exceptions           = (array) apply_filters( 'bukvar_lite_exceptions', Transliterator::defaultExceptions() );
			self::$transliterator = new Transliterator( Transliterator::BULGARIAN, array(), $exceptions );
		}
		return self::$transliterator;
	}

	public static function startResolving(): void {
		self::$resolving = ! is_admin();
	}

	public static function stopResolving(): void {
		self::$resolving = false;
	}

	/**
	 * Runs before sanitize_title_with_dashes(), which then lower-cases and hyphenates the Latin text.
	 * Only the "save" context is changed: lookups of existing Cyrillic slugs keep working.
	 *
	 * @param mixed  $title
	 * @param mixed  $raw_title
	 * @param string $context
	 * @return mixed
	 */
	public static function filterTitle( $title, $raw_title = '', $context = 'save' ) {
		if ( 'save' !== $context || self::$resolving || ! is_string( $title ) || ! Transliterator::containsCyrillic( $title ) ) {
			return $title;
		}
		if ( ! self::settings()['slugs'] ) {
			return $title;
		}
		return self::transliterator()->transliterate( $title );
	}

	/**
	 * @param mixed $filename
	 * @param mixed $filename_raw
	 * @return mixed
	 */
	public static function filterFileName( $filename, $filename_raw = '' ) {
		if ( ! is_string( $filename ) || ! Transliterator::containsCyrillic( $filename ) ) {
			return $filename;
		}
		$settings = self::settings();
		if ( ! $settings['files'] ) {
			return $filename;
		}
		return FileName::clean( $filename, self::transliterator(), $settings['files_lowercase'] );
	}

	public static function registerSettings(): void {
		register_setting(
			'permalink',
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( self::class, 'sanitize' ),
				'default'           => array(),
			)
		);
		add_settings_section(
			'bukvar_lite',
			__( 'Latin slugs and file names (Bukvar Lite)', 'bukvar-lite' ),
			static function (): void {
				echo '<p>' . esc_html__( 'Bulgarian Cyrillic is written in Latin by the Streamlined System of the Bulgarian Transliteration Act: София becomes sofia.', 'bukvar-lite' ) . '</p>';
			},
			'permalink'
		);
		$fields = array(
			'slugs'           => __( 'Write new slugs in Latin', 'bukvar-lite' ),
			'files'           => __( 'Write uploaded file names in Latin', 'bukvar-lite' ),
			'files_lowercase' => __( 'Lower-case file names', 'bukvar-lite' ),
		);
		foreach ( $fields as $key => $label ) {
			add_settings_field(
				'bukvar_lite_' . $key,
				esc_html( $label ),
				static function () use ( $key ): void {
					printf(
						'<input type="hidden" name="%1$s[%2$s]" value="0"><input type="checkbox" id="bukvar_lite_%2$s" name="%1$s[%2$s]" value="1" %3$s>',
						esc_attr( self::OPTION ),
						esc_attr( $key ),
						checked( self::settings()[ $key ], true, false )
					);
				},
				'permalink',
				'bukvar_lite'
			);
		}
		// The Permalinks screen saves its own fields only; save ours on the same submit.
		if ( isset( $_POST[ self::OPTION ], $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'update-permalink' ) && current_user_can( 'manage_options' ) ) {
			update_option( self::OPTION, self::sanitize( wp_unslash( $_POST[ self::OPTION ] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitize() casts every key to bool.
		}
	}

	/**
	 * @param array<int|string, string> $links
	 * @return array<int|string, string>
	 */
	public static function actionLinks( array $links ): array {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-permalink.php#bukvar_lite_slugs' ) ) . '">' . esc_html__( 'Settings', 'bukvar-lite' ) . '</a>' );
		return $links;
	}
}
