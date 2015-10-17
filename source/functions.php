<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', '<%= pkg.templateName %>' );
define( 'CHILD_THEME_URL', 'http://jhtechservices.com/' );
define( 'CHILD_THEME_VERSION', '<%= pkg.version %>' );

//* Enqueue Google Fonts
add_action( 'wp_enqueue_scripts', 'genesis_sample_google_fonts' );
function genesis_sample_google_fonts() {

	wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css?family=Noticia+Text:400,700,400italic|Raleway:400,700', array(), CHILD_THEME_VERSION );

}
add_action( 'wp_enqueue_scripts', 'source__enqueue_script');
function source__enqueue_script() {
	wp_enqueue_script( 'source__scripts', get_bloginfo( 'stylesheet_directory' ) . '/js/script.min.js', array( 'jquery' ), '1.0.0' );
}

//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

//* Add Accessibility support
add_theme_support( 'genesis-accessibility', array( 'headings', 'drop-down-menu',  'search-form', 'skip-links', 'rems' ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Add support for custom background
add_theme_support( 'custom-background' );

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );



//* Move footer widgets into footer tag
remove_action( 'genesis_before_footer', 'genesis_footer_widget_areas' );
add_action('genesis_footer', 'genesis_footer_widget_areas',5);

//* removing structural wraps in footer and footer-widgets
add_theme_support( 'genesis-structural-wraps', array(
	'header',
	'nav',
	'subnav',
	'site-inner',
	'footer'
) );

//* Register widget areas
genesis_register_sidebar( array(
	'id'          => 'parallax-section-below-header',
	'name'        => __( 'Parallax Section Below Header', 'your-theme-slug' ),
	'description' => __( 'This is the parallax section below header.', 'your-theme-slug' ),
) );
//* Remove Header
remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
remove_action( 'genesis_header', 'genesis_do_header' );
remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );
//* Hooks parallax-section-below-header widget area after header
add_action( 'genesis_after_header', 'parallax_section_below_header' );
function parallax_section_below_header() {

	if (is_front_page()) {

		genesis_widget_area( 'parallax-section-below-header', array(
			'before' => '<div class="below-header parallax-section widget-area"><div class="wrap">',
			'after'  => '</div></div>',
		) );
	} else {
		echo '<div class="below-header nav-background"></div>';
	}
}


//* Enqueue parallax script
add_action( 'wp_enqueue_scripts', 'enqueue_parallax_script' );
function enqueue_parallax_script() {

	if ( ! wp_is_mobile() ) {

		wp_enqueue_script( 'parallax-script', get_bloginfo( 'stylesheet_directory' ) . '/js/parallax.js', array( 'jquery' ), '1.0.0' );

	}

}

// create shortcode to put in copyright in a widget (footer)
add_shortcode('creds', 'source__creds');

function source__creds() {
	return (do_shortcode(sprintf( '[footer_copyright before="%s "] &#x000B7; JH Tech Services', __( 'Copyright', 'genesis' ))));
}


/* Returns the img tag with a default image if post has category in mry_catDefaultImages array
	used in page-knowledgbase.php.
	$size: Thumb or Full (default: Full)
*/
function source__add_default_image($size='Full') {
	$catDefaultImages = array(
		'JavaScript' => 'javaScript.jpg',
		'PHP' => 'php.jpg',
		'CSS' => 'css.jpg',
		'Exchange' => 'exchangeDefault.jpg',
		'Wordpress' => 'wordpress.jpg',
		'Genesis' => 'genesis.jpg');

	$catDefault = array_keys($catDefaultImages);
	$objCategories = get_the_category();
	$categories = array();
	foreach ($objCategories as $obj) {
		$categories[] = $obj->name;
	}
	$match = '';
	$intersect = array_intersect($categories,$catDefault);
	foreach ($intersect as $cat){
		$match = $catDefaultImages[$cat];
	}
	if ($match == ''){
		$match =  "defaultArticle.jpg";
	}
	$match=substr_replace($match, $size,-4,0);
	return sprintf('<img src="%s/images/%s" />',get_stylesheet_directory_uri(),$match);
}

//* Customize search form input box text
add_filter( 'genesis_search_text', 'jhts_search_text' );
function jhts_search_text( $text ) {
	return esc_attr( 'Search this knowledgebase...' );
}
