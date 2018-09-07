<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Helper Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Helper' ) ) {

	class Exopite_Simple_Options_Framework_Helper {

		/**
		 * Get language defaults
		 *
		 * ToDos:
		 * - add options to disable multilang
		 * - automatically save in value[current] also if no multilang plugin installed
		 *   this case without multilang plugin installed, return 'all'
		 *   because then developer will see the options and recognise the lang param,
		 *   then may think about to "turn off" the function or handle different languages
		 */
		public static function get_language_defaults( $enabled = true ) {

			if ( ! $enabled ) {
				return false;
			}
			$multilang = array();


			// Fallbacks
			$default                = mb_substr( get_locale(), 0, 2 );
			$multilang['default']   = $default;
			$multilang['current']   = $default;
			$multilang['languages'] = array( $default );

			// Sitepress is WPML

			if ( class_exists( 'SitePress' ) || class_exists( 'Polylang' ) || function_exists( 'qtranxf_getSortedLanguages' ) ) {


				if ( class_exists( 'SitePress' ) ) {

					global $sitepress;
					$multilang['default'] = $sitepress->get_default_language();
					$multilang['current'] = $sitepress->get_current_language();
					$active_languages     = $sitepress->get_active_languages();

					if ( is_array( $active_languages ) ) {
						$multilang['languages'] = array_keys( $active_languages );
					}

				} else if ( class_exists( 'Polylang' ) ) {

					// These checks of function_exists() and method_exists() added as deactivating polylang was giving fatal error

					global $polylang;


					if ( function_exists( 'pll_current_language' ) ) {

						$current = pll_current_language();

					}

					if ( function_exists( 'pll_default_language' ) ) {
						$default = pll_default_language();
					}

					if (
						// if i do not check for is_object( $polylang ), it gives $polylang as NULL
						// in short, these polylang methods not available to us while calling from plugin (not exopite framework)
//						is_object( $polylang ) &&
						property_exists( $polylang, 'model' ) &&
						method_exists( $polylang->model, 'get_languages_list' )
					) {

						$poly_langs = $polylang->model->get_languages_list();
					}


					if ( isset( $poly_langs ) && is_array( $poly_langs ) ) {
						foreach ( $poly_langs as $p_lang ) {
							$languages[ $p_lang->slug ] = $p_lang->slug;
						}
					}


					$multilang['default'] = $default;
					// When all languages selected, then $current is false, so make $current as $default
					$multilang['current']   = ( isset( $current ) && $current ) ? $current : $multilang['current'];
					$multilang['languages'] = ( isset( $languages ) && $languages ) ? $languages : $multilang['languages'];


				} else if ( function_exists( 'qtranxf_getSortedLanguages' ) ) {

					global $q_config;
					$multilang['default']   = $q_config['default_language'];
					$multilang['current']   = $q_config['language'];
					$multilang['languages'] = qtranxf_getSortedLanguages(false);

//				var_dump( $multilang['languages']); die();


				}

			}



			$multilang = apply_filters( 'exopite_sof_language_defaults', $multilang );

			return ( ! empty( $multilang ) ) ? $multilang : false;

		}


		public static function get_current_language_code() {

			$multilang = self::get_language_defaults();

			return $multilang['current'];
		}

	}

}
