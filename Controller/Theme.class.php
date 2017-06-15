<?php
namespace Octopus\Controller;
class Theme extends Base {

	public function init() {

	}

	/**
	 * Add slug of current page to body class
	 */
	function add_slug_to_body_class() {
		add_filter( 'body_class', function ( $classes ) {
			global $post;
			if ( is_home() ) {
				$key = array_search( 'blog', $classes );
				if ( $key > - 1 ) {
					unset( $classes[ $key ] );
				}
			} elseif ( is_page() ) {
				$classes[] = sanitize_html_class( $post->post_name );
			} elseif ( is_singular() ) {
				$classes[] = sanitize_html_class( $post->post_name );
			}

			return $classes;
		} );
	}

	/**
	 * Add css file to editor
	 *
	 * @param array $styles_src
	 */
	function add_editor_styles( $styles_src = array() ) {
		if ( ! empty( $styles_src ) ) {
			foreach ( $styles_src as $style_src ) {
				add_editor_style( $style_src );
			}
		}
	}

	/**
	 * Change logo of login page
	 *
	 * @param $url
	 * @param int $width
	 * @param int $height
	 */
	function change_login_logo( $url, $width = 280, $height = 78 ) {
		$this->add_attribute( 'login_logo', $url, false );
		$this->add_attribute( 'login_logo_w', $width, false );
		$this->add_attribute( 'login_logo_h', $height, false );

		add_action( 'login_head', function () {
			if ( ! empty( $this->attributes['login_logo'] ) ) {

				$w = $this->get_attribute( 'login_logo_w' );
				$h = $this->get_attribute( 'login_logo_h' );

				echo "<style type='text/css'>
	                .login h1 a {
	                    background-image: url('" . $this->get_attribute( 'login_logo' ) . "');
	                    background-size: {$w}px {$h}px;
	                    width: {$w}px;
	                    height: {$h}px;
	                }
                </style>";
			}
		} );
	}

	function change_login_logo_link( $link ) {
		$this->add_attribute( 'login_logo_link', $link, false );

		add_filter( 'login_headerurl', function () {
			return $this->get_attribute( 'login_logo_link' );
		} );
	}

	function change_login_logo_title( $title ) {
		$this->add_attribute( 'login_logo_title', $title, false );

		add_filter( 'login_headertitle', function () {
			return $this->get_attribute( 'login_logo_title' );
		} );
	}

	function set_default_editor() {
		add_filter( 'wp_default_editor', create_function( '', 'return "tinymce";' ) );
	}

	function remove_wp_generator() {
		remove_action( 'wp_head', 'wp_generator' );
	}

	function change_admin_footer_text( $text = '' ) {
		$this->add_attribute( 'admin_footer_text', $text, false );
		add_filter( 'admin_footer_text', function () {
			return $this->get_attribute( 'admin_footer_text' );
		} );
	}

	function remove_contextual_help() {
		add_filter( 'contextual_help', function ( $old_help, $screen_id, $screen ) {
			$screen->remove_help_tabs();

			return $old_help;
		}, 999, 3 );
	}

	function filter_remove_wp_css_js_version( $src ) {
		if ( strpos( $src, 'ver=' ) ) {
			$src = remove_query_arg( 'ver', $src );
		}

		return $src;
	}

	function remove_wp_css_js_version() {
		add_filter( 'style_loader_src', array( $this, 'filter_remove_wp_css_js_version' ), 9999 );
		add_filter( 'script_loader_src', array( $this, 'filter_remove_wp_css_js_version' ), 9999 );
	}


	function remove_rss_version() {
		add_filter( 'the_generator', function () {
			return '';
		} );
	}

	function remove_wp_logo() {
		add_action( 'wp_before_admin_bar_render', function () {
			global $wp_admin_bar;
			$wp_admin_bar->remove_menu( 'wp-logo' );
		} );
	}

	function remove_default_widgets( $list_widgets = array() ) {
		if ( ! empty( $list_widgets ) ) {
			$this->add_attribute( 'removed_widgets', $list_widgets );
		} else {
			$this->attributes['removed_widgets'] = apply_filters( 'octp_default_removed_widgets', array(
				'WP_Widget_Pages',
				'WP_Widget_Calendar',
				'WP_Widget_Archives',
				'WP_Widget_Meta',
				'WP_Widget_Search',
				'WP_Widget_Text',
				'WP_Widget_Categories',
				'WP_Widget_Recent_Posts',
				'WP_Widget_Recent_Comments',
				'WP_Widget_RSS',
				'WP_Widget_Tag_Cloud',
				'WP_Nav_Menu_Widget',
			) );
		}

		add_action( 'widgets_init', function () {
			$removed_widgets = $this->get_attribute( 'removed_widgets' );
			if ( ! empty( $removed_widgets ) ) {
				foreach ( $removed_widgets as $item ) {
					unregister_widget( $item );
				}
			}
		}, 11 );
	}

	function remove_meta_boxes( $list_meta_box = array() ) {
		if ( ! empty( $list_meta_box ) ) {
			$this->add_attribute( 'removed_meta_boxes', $list_meta_box );

			add_action( 'admin_menu', function () {
				$meta_boxes = $this->get_attribute( 'removed_meta_boxes' );
				if ( ! empty( $meta_boxes ) ) {
					foreach ( $meta_boxes as $item ) {
						$item['context'] = empty( $item['context'] ) ? 'normal' : $item['context'];
						remove_meta_box( $item['id'], $item['screen'], $item['context'] );
					}
				}
			}, 11 );
		}
	}

	function load_theme_textdomain( $textdomains = array() ) {
		if ( ! empty( $textdomains ) ) {
			$this->add_attribute( 'textdomains', $textdomains );
			add_action( 'after_setup_theme', function () {
				$textdomains = $this->get_attribute( 'textdomains' );
				if ( ! empty( $textdomains ) ) {
					foreach ( $textdomains as $k => $url ) {
						load_theme_textdomain( $k, $url );
					}
				}
			} );
		}
	}

	function add_theme_support( $features = array() ) {
		if ( ! empty( $features ) ) {
			$this->add_attribute( 'theme_support_features', $features );
		} else {
			$this->attributes['theme_support_features'] = apply_filters( 'octp_default_theme_support_features', array(
				'automatic-feed-links' => '',
				'title-tag'            => '',
				'post-thumbnails'      => '',
				'html5'                => array(
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
				)
			) );
		}

		add_action( 'after_setup_theme', function () {
			$features = $this->get_attribute( 'theme_support_features' );
			if ( ! empty( $features ) ) {
				foreach ( $features as $k => $item ) {
					if ( ! empty( $item ) ) {
						add_theme_support( $k, $item );
					} else {
						add_theme_support( $k );
					}
				}
			}
		} );
	}

	/**
	 * @deprecated
	 *
	 * @param array $nav_menus
	 */
	function register_nav_menus( $nav_menus = array() ) {
		if ( ! empty( $nav_menus ) ) {
			$this->add_attribute( 'nav_menus', $nav_menus );

			add_action( 'after_setup_theme', function () {
				$nav_menus = $this->get_attribute( 'nav_menus' );
				if ( ! empty( $nav_menus ) ) {
					register_nav_menus( $nav_menus );
				}
			} );
		}
	}

	static function locate_file( $file, $is_url = true ) {

		$theme_url        = get_template_directory_uri();
		$theme_path       = get_template_directory();
		$child_theme_url  = get_stylesheet_directory_uri();
		$child_theme_path = get_stylesheet_directory();


		if ( empty( $file ) ) {
			return false;
		}

		if ( $file[0] == "/" ) {
			$file = substr( $file, 1 );
		}

		//$base_name = otp_get_setting( 'slug' ) . '/';

		$file_paths = array(
			$child_theme_path . '/' . $file,
			$theme_path . '/' . $file
		);
		$file_urls  = array(
			$child_theme_url . '/' . $file,
			$theme_url . '/' . $file
		);

		foreach ( $file_paths as $k => $path ) {
			if ( file_exists( $path ) ) {
				if ( $is_url ) {
					return $file_urls[ $k ];
				} else {
					return $path;
				}
			}
		}

		return false;
	}

}   // EOC

