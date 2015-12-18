<?php
/**
 * store functions and definitions
 *
 * @package store
 */

add_filter('relevanssi_hits_filter', 'order_the_results');
function order_the_results($hits) {
    global $wp_query;
 
    $likes = array();
	foreach ($hits[0] as $hit) {
		$likecount = get_post_meta($hit->ID, 'preco', true);
    	if (!isset($likes[$likecount])) $likes[$likecount] = array();
    			array_push($likes[$likecount], $hit);
		}

		ksort($likes);
  		$sorted_hits = array();
	foreach ($likes as $likecount => $year_hits) {
     		$sorted_hits = array_merge($sorted_hits, $year_hits);
    }
	$hits[0] = $sorted_hits;

    return $hits;
}

// SESSION HANDLING ====================================================
add_action('init','session_start_');
function session_start_(){
	session_start();
	if(isset($_GET['limpar_cesta'])){
		unset($_SESSION['cart_items']);
		wp_redirect( home_url() ); exit;
	}
}

// ADICIONANDO ITEMS AO CARRINHO =======================================
add_action('init','add_to_cart');
function add_to_cart(){
	if(!isset($_GET['add_to_cart']))
		return;

 	if(!is_array($_SESSION['cart_items']))
 		$_SESSION['cart_items'] = array();

 	$refcode = $_GET['add_to_cart'];
 	if(isset($_SESSION['cart_items'][$refcode])){
 		$_SESSION['cart_items'][$refcode]++;
 	}else{
 		$_SESSION['cart_items'][$refcode] = 1;
 	}
}


// GERANDO A LISTA DE ITENS DO CARRINHO ===============================
add_shortcode('cart','view_cart');
function view_cart(){
	if(isset($_SESSION['cart_items'])){
		// The Query
		$the_query = new WP_Query(array(
			'post_type' => 'produto',
			'meta_key' 	=> 'preco',
			'orderby' => array('title' => 'ASC','meta_value_num'=>'ASC'),
			'meta_query' => array(
				array(
					'key'     => 'refcode',
					'value'   => array_keys($_SESSION['cart_items']),
					'compare' => 'IN',
				),
			),
			'posts_per_page' => -1
		));

		// The Loop
		if ( $the_query->have_posts() ) {
			
			$estabelecimentos = array();
			while ( $the_query->have_posts() ) {

				$the_query->the_post();
				$estabelecimentos[get_field('disponivel_em')->post_title][] = $the_query->post;
			}

			$sorted = array();
			
			$i = 0;
			foreach($estabelecimentos as $k => $items){
				if(count($items) < count($_SESSION['cart_items']))
					continue;

				$class = ($i==0)?'class="best"':'';
				$html = "<div>";
				$melhor_compra = ($i==0)?" - Melhor compra!":"";
				$html .= "<h3 $class>".$k.$melhor_compra."</h3>";
				
				$html .= '<table>';
				$html .= '<tr><td><strong>Produto</strong></td><td><strong>Quantidade</strong></td><td><strong>Preço</strong></td><td><strong>Total</strong></td></tr>';
				$total = 0;
				foreach($items as $item){
					$qtd = $_SESSION['cart_items'][get_field('refcode',$item->ID)];
					$preco = number_format(get_field('preco',$item->ID),2,",",".");
					$html .= "<tr>";
					$html .= '<td>' . $item->post_title . '</td>';
					$html .= '<td>' . $qtd . '</td>';
					$html .= '<td>R$'.$preco.'</td>';
					$html .= '<td>R$'.number_format(get_field('preco',$item->ID)*$qtd,2,",",".").'</td>';
					$html .= "</tr>";
					$total += get_field('preco',$item->ID)*$qtd;
				}
				$html .= "<tr><td></td><td></td><td>TOTAL</td><td><strong class='red'>R$ ".number_format($total,2,",",".")."</strong></td></tr>";
				
				$html .= '</table>';
				$html .= "</div>";
				$sorted[number_format($total,2,",",".").'_'.$k] = $html;
				$i++;
			}
			ksort($sorted);
			echo implode("",$sorted);
			echo "<a href='?limpar_cesta=1'>Limpar cestas</a>";
			
		} else {
			// no posts found
			echo "sem produtos no carrinho";
		}
		/* Restore original Post Data */
		wp_reset_postdata();
	}

}

if ( ! function_exists( 'store_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function store_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on store, use a find and replace
	 * to change 'store' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'store', get_template_directory() . '/languages' );

	/**
	 * Set the content width based on the theme's design and stylesheet.
	 */
	 global $content_width;
	 if ( ! isset( $content_width ) ) {
		$content_width = 640; /* pixels */
	 }
	 
	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 *
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'store' ),
		'top' => __( 'Top Menu', 'store' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'store_custom_background_args', array(
		'default-color' => 'f7f5ee',
		'default-image' => '',
	) ) );
	
	add_image_size('store-sq-thumb', 600,600, true );
	add_image_size('store-thumb', 540,450, true );
	add_image_size('pop-thumb',542, 340, true );
	
	//Declare woocommerce support
	add_theme_support('woocommerce');
	
}
endif; // store_setup
add_action( 'after_setup_theme', 'store_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function store_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'store' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title title-font">',
		'after_title'   => '</h1>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Footer 1', 'store' ), 
		'id'            => 'footer-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title title-font">',
		'after_title'   => '</h1>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 2', 'store' ), 
		'id'            => 'footer-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title title-font">',
		'after_title'   => '</h1>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 3', 'store' ), 
		'id'            => 'footer-3',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title title-font">',
		'after_title'   => '</h1>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Footer 4', 'store' ), 
		'id'            => 'footer-4',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title title-font">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'store_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function store_scripts() {
	wp_enqueue_style( 'store-style', get_stylesheet_uri() );
	
	wp_enqueue_style('store-title-font', '//fonts.googleapis.com/css?family='.str_replace(" ", "+", get_theme_mod('store_title_font', 'Lato') ).':100,300,400,700' );
	
	wp_enqueue_style('store-body-font', '//fonts.googleapis.com/css?family='.str_replace(" ", "+", get_theme_mod('store_body_font', 'Open Sans') ).':100,300,400,700' );
	
	wp_enqueue_style( 'store-fontawesome-style', get_template_directory_uri() . '/assets/font-awesome/css/font-awesome.min.css' );
	
	wp_enqueue_style( 'store-bootstrap-style', get_template_directory_uri() . '/assets/bootstrap/css/bootstrap.min.css' );
	
	wp_enqueue_style( 'store-hover-style', get_template_directory_uri() . '/assets/css/hover.min.css' );

	wp_enqueue_style( 'store-slicknav', get_template_directory_uri() . '/assets/css/slicknav.css' );
	
	wp_enqueue_style( 'store-swiperslider-style', get_template_directory_uri() . '/assets/css/swiper.min.css' );
	
	wp_enqueue_style( 'store-main-theme-style', get_template_directory_uri() . '/assets/css/'.get_theme_mod('store_skin', 'default').'.css' );

	wp_enqueue_script( 'store-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );
	
	wp_enqueue_script( 'store-externaljs', get_template_directory_uri() . '/js/external.js', array('jquery'), '20120206', true );

	wp_enqueue_script( 'store-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	
	wp_enqueue_script( 'store-custom-js', get_template_directory_uri() . '/js/custom.js', array('store-externaljs') );
}
add_action( 'wp_enqueue_scripts', 'store_scripts' );

/**
 * Include the Custom Functions of the Theme.
 */
require get_template_directory() . '/framework/theme-functions.php';

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Implement the Custom CSS Mods.
 */
require get_template_directory() . '/inc/css-mods.php';


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
