<?php
/**
 * Display all freesiaempire functions and definitions
 *
 * @package Theme Freesia
 * @subpackage Freesia Empire
 * @since Freesia Empire 1.0
 */

/************************************************************************************************/
if ( ! function_exists( 'freesiaempire_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function freesiaempire_setup() {
	/**
	 * Set the content width based on the theme's design and stylesheet.
	 */
	global $content_width;
	if ( ! isset( $content_width ) ) {
			$content_width=790;
	}

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on freesiaempire, use a find and replace
	 * to change 'freesia-empire' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'freesia-empire', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );
	add_theme_support('post-thumbnails');

	/*
	 * Let WordPress manage the document title.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	//add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in three location.
	register_nav_menus( array(
		'primary' => __( 'Main Menu', 'freesia-empire' ),
		'social-link'  => __( 'Add Social Icons Only', 'freesia-empire' ),
	) );
	add_image_size('freesiaempire_slider_image', 1920, 1080, true);

	/*
	 * Switch default core markup for comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'comment-form', 'comment-list', 'gallery', 'caption',
	) );


	// Add support for responsive embeds.
	add_theme_support( 'responsive-embeds' );

	add_theme_support( 'gutenberg', array(
			'colors' => array(
				'#6897e5',
			),
		) );
	add_theme_support( 'align-wide' );


	/**
	 * Add support for the Aside Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio' ) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'freesiaempire_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	add_editor_style( array( 'css/editor-style.css', 'genericons/genericons.css', '//fonts.googleapis.com/css?family=Roboto:400,300,500,700' ) );

	/**
	* Making the theme Woocommrece compatible
	*/

	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
endif; // freesiaempire_setup
add_action( 'after_setup_theme', 'freesiaempire_setup' );

/***************************************************************************************/
function freesiaempire_content_width() {
	if ( is_page_template( 'page-templates/gallery-template.php' ) || is_attachment() ) {
		global $content_width;
		$content_width = 1170;
	}
}
add_action( 'template_redirect', 'freesiaempire_content_width' );

/***************************************************************************************/
if(!function_exists('freesiaempire_get_theme_options')):
	function freesiaempire_get_theme_options() {
	    return wp_parse_args(  get_option( 'freesiaempire_theme_options', array() ),  freesiaempire_get_option_defaults_values() );
	}
endif;

if (!is_child_theme()){
	require get_template_directory() . '/inc/welcome-notice.php';
}

/***************************************************************************************/
require get_template_directory() . '/inc/customizer/freesiaempire-default-values.php';
require( get_template_directory() . '/inc/settings/freesiaempire-functions.php' );
require( get_template_directory() . '/inc/settings/freesiaempire-common-functions.php' );
require get_template_directory() . '/inc/jetpack.php';

/************************ Freesia Empire Widgets  *****************************/
require get_template_directory() . '/inc/widgets/widgets-functions/contactus-widgets.php';
require get_template_directory() . '/inc/widgets/widgets-functions/post-widgets.php';
require get_template_directory() . '/inc/widgets/widgets-functions/register-widgets.php';
require get_template_directory() . '/inc/widgets/widgets-functions/testimonials-widgets.php';
require get_template_directory() . '/inc/widgets/widgets-functions/portfolio-widgets.php';

/************************ Freesia Empire Customizer  *****************************/
require get_template_directory() . '/inc/customizer/functions/sanitize-functions.php';
require get_template_directory() . '/inc/customizer/functions/register-panel.php';
function freesiaempire_customize_register( $wp_customize ) {
if(!class_exists('Freesia_Empire_Plus_Features') && !class_exists('Freesia_Business_Customize_upgrade') && !class_exists('Freesia_Corporate_Customize_upgrade')){
	class freesiaempire_upgrade extends WP_Customize_Control {
		public function render_content() { ?>
			<a title="<?php esc_attr_e( 'Review Freesia Empire', 'freesia-empire' ); ?>" href="<?php echo esc_url( 'https://wordpress.org/support/view/theme-reviews/freesia-empire/' ); ?>" target="_blank" id="about_freesiaempire">
			<?php _e( 'Review Freesia Empire', 'freesia-empire' ); ?>
			</a><br/>
			<a href="<?php echo esc_url( 'https://themefreesia.com/theme-instruction/freesia-empire/' ); ?>" title="<?php esc_attr_e( 'Theme Instructions', 'freesia-empire' ); ?>" target="_blank" id="about_freesiaempire">
			<?php _e( 'Theme Instructions', 'freesia-empire' ); ?>
			</a><br/>
			<a href="<?php echo esc_url( 'https://tickets.themefreesia.com/' ); ?>" title="<?php esc_attr_e( 'Support Ticket', 'freesia-empire' ); ?>" target="_blank" id="about_freesiaempire">
			<?php _e( 'Forum', 'freesia-empire' ); ?>
			</a><br/>
			<a href="<?php echo esc_url( 'https://demo.themefreesia.com/freesia-empire/' ); ?>" title="<?php esc_attr_e( 'View Demo', 'freesia-empire' ); ?>" target="_blank" id="about_freesiaempire">
			<?php _e( 'View Demo', 'freesia-empire' ); ?>
			</a><br/>
			<a href="<?php echo esc_url(home_url('/')).'wp-admin/theme-install.php?search=author:themefreesia'; ?>" title="<?php esc_attr_e( 'View ThemeFreesia Themes', 'freesia-empire' ); ?>" target="_blank" id="about_freesiaempire">
			<?php _e( 'View ThemeFreesia Themes', 'freesia-empire' ); ?>
			</a><br/>
		<?php
		}
	}
	$wp_customize->add_section('freesiaempire_upgrade_links', array(
		'title'					=> __('About Freesia Empire', 'freesia-empire'),
		'priority'				=> 2,
	));
	$wp_customize->add_setting( 'freesiaempire_upgrade_links', array(
		'default'				=> false,
		'capability'			=> 'edit_theme_options',
		'sanitize_callback'	=> 'wp_filter_nohtml_kses',
	));
	$wp_customize->add_control(
		new freesiaempire_upgrade(
		$wp_customize,
		'freesiaempire_upgrade_links',
			array(
				'section'				=> 'freesiaempire_upgrade_links',
				'settings'				=> 'freesiaempire_upgrade_links',
			)
		)
	);
}
	require get_template_directory() . '/inc/customizer/functions/design-options.php';
	require get_template_directory() . '/inc/customizer/functions/theme-options.php';
	require get_template_directory() . '/inc/customizer/functions/frontpage-features.php';
	require get_template_directory() . '/inc/customizer/functions/featured-content-customizer.php' ;
}

add_action( 'customize_register', 'freesiaempire_customize_register' );
add_action( 'customize_preview_init', 'freesiaempire_customize_preview_js' );

if(!class_exists('Freesia_Empire_Plus_Features')){
	if(!function_exists('freesia_business_customize_register') && !function_exists('freesia_corporate_customize_register')){
		// Add Upgrade to Pro Button.
		require_once( trailingslashit( get_template_directory() ) . 'inc/upgrade-plus/class-customize.php' );
	}
}
/**************************************************************************************/
function freesiaempire_hide_previous_custom_css( $wp_customize ) {
	// Bail if not WP 4.7.
	if ( ! function_exists( 'wp_get_custom_css_post' ) ) {
		return;
	}
		$wp_customize->remove_control( 'freesiaempire_theme_options[freesiaempire_custom_css]' );
}
add_action( 'customize_register', 'freesiaempire_hide_previous_custom_css');
/**************************************************************************************/

// Add Post Class Clearfix
function freesiaempire_post_class_clearfix( $classes ) {
	$classes[] = 'clearfix';
	return $classes;
}
add_filter( 'post_class', 'freesiaempire_post_class_clearfix' );

/******************* Front Page *************************/
function freesiaempire_display_front_page(){
	require get_template_directory() . '/index.php';
}

add_action('freesiaempire_show_front_page','freesiaempire_display_front_page');

/******************* Freesia Empire Header Display *************************/
function freesiaempire_header_display(){
	$freesiaempire_settings = freesiaempire_get_theme_options();
	$header_display = $freesiaempire_settings['freesiaempire_header_display'];
	$header_logo = $freesiaempire_settings['freesiaempire-img-upload-header-logo'];
	if ($header_display == 'header_logo' || $header_display == 'header_text' || $header_display == 'show_both')	{
		echo '<div id="site-branding">';
			if($header_display != 'header_text'){ ?>
				<a href="<?php echo esc_url(home_url('/'));?>" title="<?php echo esc_attr(get_bloginfo('name', 'display'));?>" rel="home"> <img src="<?php echo esc_url($header_logo);?>" id="site-logo" alt="<?php echo esc_attr(get_bloginfo('name', 'display'));?>"></a>
			<?php }
				if (is_home() || is_front_page()){ ?>
				<h1 id="site-title"> <?php }else{?> <h2 id="site-title"> <?php } ?>
				<a href="<?php echo esc_url(home_url('/'));?>" title="<?php echo esc_html(get_bloginfo('name', 'display'));?>" rel="home"> <?php bloginfo('name');?> </a>
				<?php if(is_home() || is_front_page()){ ?>
				</h1>  <!-- end .site-title -->
				<?php } else { ?> </h2> <!-- end .site-title --> <?php }

				$site_description = get_bloginfo( 'description', 'display' );
				if ($site_description){?>
					<div id="site-description"> <?php bloginfo('description');?> </div> <!-- end #site-description -->
		<?php }
		echo '</div>'; // end #site-branding
	}
}

add_action('freesiaempire_site_branding','freesiaempire_header_display');
//ウィジェット追加
register_sidebar(array('name' => '右サイドバー'));
register_sidebar(array('name' => '左サイドバー'));
register_nav_menu( 'footer-menu', 'フッターメニュー' );

//JS読み込み
function twpp_enqueue_scripts() {
  wp_enqueue_script(
    'share2-script',
    get_template_directory_uri() . '/js/share2.js'
  );
}
add_action( 'wp_enqueue_scripts', 'twpp_enqueue_scripts' );

//Include Functions
get_template_part('functions/title');
get_template_part('functions/category_list');
get_template_part('functions/monthly_total');
get_template_part('functions/option_items');
get_template_part('functions/update_wallet_asset');

//wp ajax通信をつかう
function add_ajaxurl() {
?>
  <script>
    var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
  </script>
<?php
}
add_action( 'wp_head', 'add_ajaxurl', 1 );

// mypage,household category_sub用
function ajax_get_option(){
	global $wpdb;
	$items = [];
  $selected = $_POST['selecter'];
  $p_option_datas = $wpdb->get_col( $wpdb->prepare(
    "SELECT category_sub
		 FROM $wpdb->purchases_category
     WHERE category_main = %s",
     $selected
  ) );
	//category_sub配列の作成
	foreach($p_option_datas as $row_data) {
		array_push($items, $row_data);
	}
	$i_option_datas = $wpdb->get_col( $wpdb->prepare(
		"SELECT category_sub
		 FROM $wpdb->incomes_category
		 WHERE category_main = %s",
		 $selected
	) );
  foreach($i_option_datas as $row_data) {
    array_push($items, $row_data);
  }
  echo json_encode($items);
	wp_die();
}
add_action( 'wp_ajax_ajax_get_option', 'ajax_get_option' );
add_action( 'wp_ajax_nopriv_ajax_get_option', 'ajax_get_option' );

//データベース操作系
function ajax_db(){
	global $wpdb;
	//household targetChangeBtn
	if (!empty($_POST['nowTarget']))
	{
	  $boolean = $_POST['nowTarget'];
	  $id = $_POST['id'];
	  $source = $_POST['source'];

	  if($source == "支出") {
	    $db_type = "wp1_purchases";
	  } else if ($source == "収入") {
	    $db_type = "wp1_incomes";
	  }
	  $wpdb->update($db_type,
	  array(
	    'target' => $boolean
	  ),
	  array(
	    'id' => $id
	  ),
	  array(
	    '%d'
	  ));
		echo json_encode($boolean);
		wp_die();
	}
	//targetChangeIpt
	else if (!empty($_POST['changeDataType']))
	{
		$change_data_type = $_POST['changeDataType'];
		$p_i_id = $_POST['id'];
		$source = $_POST['source'];
		$value = $_POST['value'];

		if($source == "支出") {
			$db_type = "wp1_purchases";
			$target_data = $wpdb->get_row( $wpdb->prepare(
				"SELECT * FROM $wpdb->purchases WHERE id = %d",
				$p_i_id
			));
		} else if ($source == "収入") {
			$db_type = "wp1_incomes";
			$target_data = $wpdb->get_row( $wpdb->prepare(
				"SELECT * FROM $wpdb->incomes WHERE id = %d",
				$p_i_id
			));
		}

		if($change_data_type == "price") {
			$data_type = "%d";
		} else {
			$data_type = "%s";
		}

		$wpdb->update($db_type,
		array(
			$change_data_type => $value,
			'modified' => current_time('mysql') //編集年月日
		),
		array(
			'id' => $p_i_id
		),
		array(
			$data_type,
			'%s'
		));
		if($change_data_type == 'price') {
			$price = $value - $target_data->price;
			update_wallet_asset($source, $target_data->source, $price);
		}
	}
	//service-list
	else if (!empty($_POST['submitWallet']))
	{
		$name = $_POST['name'];
		$wallet_name = $_POST['wallet_name'];
		$wallet_price = $_POST['wallet_price'];
		$asset_type = $_POST['asset_type'];
		$asset_name = $_POST['asset_name'];

		if($name == 'newCash') {
			if (empty($wallet_name)) {
				$cash_message = '名前は必ず入力してください。';
			} else if (empty($wallet_price)) {
				$cash_message = '残高は必ず入力してください。';
			} else if  (iconv_strlen($wallet_name) > 20 ) {
				$cash_message = '名前は20文字以内で入力してください。';
			} else if  (iconv_strlen($wallet_price) > 12 ) {
				$cash_message = '残高は12桁以内で入力してください。';
			}
			// 同じ財布の名前がないか確認する
			$check_data = $wpdb->get_row( $wpdb->prepare(
				"SELECT * FROM $wpdb->wallet WHERE possession = %s",
				$wallet_name
			));
			if(!empty($check_data))
			{
				$cash_message = '既に登録されている財布の名前です。';
			}
		} else if($name == 'newAsset') {
			if (empty($asset_name)) {
	      $asset_message = '金融機関名は必ず入力してください。';
	    } else if  (iconv_strlen($asset_name) > 20 ) {
	      $asset_message = '金融機関名は20文字以内で入力してください。';
	    }
			// 同じ財布の名前がないか確認する
			$check_data = $wpdb->get_row( $wpdb->prepare(
				"SELECT * FROM $wpdb->wallet WHERE possession = %s",
				$asset_name
			));
			if(!empty($check_data))
			{
				$asset_message = '既に登録されている金融機関名です。';
			}
		}
		if(empty($cash_message) && empty($asset_message)) {
			if($name == "newCash") {
				// 登録の処理
				// $wpdb->insert('wp1_wallet',
				//   array(
				//     'name' => $wallet_name,
				//     'price' => $wallet_price,
				//     'type' => "財布",
				//     'type_sub' => "現金",
				//     'possession' => $wallet_name,
				//     'created' => current_time('mysql')
				//     ),
				//   array(
				//     '%s',
				//     '%d',
				//     '%s',
				//     '%s',
				//     '%s',
				// 		'%s'
				//   ));
				//資産の作成
				// $wpdb->insert('wp1_asset',
				//   array(
				//     'wallet_name' => $wallet_name,
				//     'price' => $wallet_price,
				//     'created' => current_time('mysql')
				//     ),
				//   array(
				//     '%s',
				//     '%d',
				// 		'%s'
				//   ));
				$id = $wpdb->get_var( $wpdb->prepare(
					"SELECT id FROM $wpdb->wallet WHERE possession = %s",
					$wallet_name
				));
			} else if ($name == "newAsset") {
				$type_sub = $wpdb->get_var( $wpdb->prepare(
					"SELECT category_sub FROM $wpdb->asset_category
					 WHERE category_main = %s",
					$asset_type
				));
				// 登録の処理(資産データの作成は資産の追加で実装)
				// $wpdb->insert('wp1_wallet',
				// 	array(
				// 		'possession' => $asset_name,
				// 		'price' => 0,
				// 		'type' => $asset_type,
				// 		'type_sub' => $type_sub
				// 		),
				// 	array(
				// 		'%s',
				// 		'%d',
				// 		'%s'
				// 	));
				$id = $wpdb->get_var( $wpdb->prepare(
					"SELECT id FROM $wpdb->wallet WHERE possession = %s",
					$asset_name
				));
			}
		}
		$result = [$id, $cash_message, $asset_message, $type_sub];
		echo json_encode($result);
		wp_die();
	}
	//edit-manual
	else if (!empty($_POST['changeWallet']))
	{
		$wallet_name = $_POST['wallet_name'];
		$wallet_info = $_POST['wallet_info'];
		$wallet_id = $_POST['wallet_id'];
		$possession = $_POST['possession'];
		$old_wallet_name = $_POST['old_wallet_name'];

		if (empty($wallet_name)) {
			$message = '名前は必ず入力してください。';
		} else if  (iconv_strlen($wallet_name) > 20 ) {
			$message = '名前は20文字以内で入力してください。';
		} else if  (iconv_strlen($wallet_info) > 20 ) {
			$message = 'メモは20文字以内で入力してください。';
		}
		// 同じ財布の名前がないか確認する
		$check_data = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $wpdb->wallet WHERE name = %s",
			$wallet_name
		));
		$befor_data = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $wpdb->wallet WHERE id = %d",
			$wallet_id
		));
		if(!empty($check_data) && ($wallet_name != $befor_data->name))
		{
			$message = '既に登録されている財布の名前です。';
		}
		if(empty($message)) {
			// 更新の処理(現金とポイントで処理が違う)
      $wpdb->update('wp1_wallet',
      array(
        'name' => $wallet_name,
        'possession' => $possession,
        'info' => $wallet_info
        ),
      array(
        'id' => $wallet_id
        ),
      array(
        '%s',
        // '%s',
				'%s'
      ));
			// 資産の財布名更新
			$wpdb->update('wp1_asset',
			array(
			  'wallet_name' => $wallet_name
			  ),
			array(
			  'wallet_name' => $old_wallet_name
			  ),
			array(
			  '%s'
			));
      $message = '登録処理が完了しました。';
			$flag = true;

			// $id = $wpdb->get_var( $wpdb->prepare(
			// 	"SELECT id FROM $wpdb->wallet WHERE possession = %s",
			// 	$possession
			// ));
		}
		$result = [$message, $flag];
		echo json_encode($result);
		wp_die();
	}
}

add_action( 'wp_ajax_ajax_db', 'ajax_db' );
add_action( 'wp_ajax_nopriv_ajax_db', 'ajax_db' );

function ajax_get_total_asset() {
	date_default_timezone_set('Asia/Tokyo');
	global $wpdb;
	$days = $_POST['days'];

	// $today = new DateTime();
	// $today_d = $today->format('d');

	// $start_time = date('Y-m-01');
	$end_time = date('Y-m-d', strtotime("-" .$day. "day"));

	//グラフ範囲外の最新priceを取得
	$befor_data = $wpdb->get_var( $wpdb->prepare(
	  "SELECT sum(wp1_asset.price) FROM
	  (SELECT wallet_name, max(created) AS created
	  FROM $wpdb->asset WHERE created <= %s
	  GROUP BY wallet_name) AS wp2_asset
	  LEFT JOIN
	  (SELECT * FROM $wpdb->asset) AS wp1_asset
	  ON wp2_asset.wallet_name = wp1_asset.wallet_name AND wp2_asset.created = wp1_asset.created",
	  $end_time
	));
	if(empty($befor_data)) {
	  $befor_data = 0;
	}
	//残高推移グラフ用配列の作成
	$real_asset = [];
	for($i=0; $i<$days; $i++) {
	  $end_time = date('Y-m-d', strtotime("-" .$i. "day"));
	  $data = $wpdb->get_var( $wpdb->prepare(
	    "SELECT sum(wp1_asset.price) FROM
	    (SELECT wallet_name, max(created) AS created
	    FROM $wpdb->asset WHERE created <= %s
	    GROUP BY wallet_name) AS wp2_asset
	    LEFT JOIN
	    (SELECT * FROM $wpdb->asset) AS wp1_asset
	    ON wp2_asset.wallet_name = wp1_asset.wallet_name AND wp2_asset.created = wp1_asset.created",
	    $end_time
	  ));
	  if(empty($data)) {
	    $data = $befor_data;
	  }

	  array_push($real_asset, intval($data));
	}
	//ポイント配列（仮）作成
	$point_asset = [];
	$data = $wpdb->get_var( $wpdb->prepare(
		"SELECT sum(price) FROM $wpdb->wallet WHERE type = 'ポイント'"
	));
	for($i=0; $i<$days; $i++) {
		// $data = $wpdb->get_var( $wpdb->prepare(
		// 	"SELECT sum(price) FROM $wpdb->wallet WHERE type = 'ポイント'"
		// ));
		// if(empty($data)) {
		// 	$data = $befor_data;
		// }
		array_push($point_asset, intval($data));
	}
	$datas = [$real_asset, $point_asset];
	echo json_encode($datas);
	wp_die();
}
add_action( 'wp_ajax_ajax_get_total_asset', 'ajax_get_total_asset' );
add_action( 'wp_ajax_nopriv_ajax_get_total_asset', 'ajax_get_total_asset' );

function ajax_day_change(){
	if($_POST['data_type'] == "total-cell-list")
	{
		date_default_timezone_set('Asia/Tokyo');
		global $wpdb;
		$days = $_POST['days'];
		// $today = new DateTime();
		$end_time = date('Y-m-d', strtotime("-". $days ."day"));

		// if (!empty($_POST['wallet_name'])) {
			//財布名があるとき
			$wallet_name = $_POST['wallet_name'];
			$asset_datas = $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM $wpdb->asset WHERE wallet_name = %s limit %d",
				[$wallet_name, $days]
			));

			//グラフ範囲外の最新priceを初期値に代入
			$befor_datas = $wpdb->get_row( $wpdb->prepare(
				"SELECT * FROM $wpdb->asset
				where created <= %s and wallet_name = %s order by created desc limit 1" ,
				[$end_time, $wallet_name]
			));
			$befor_price = intval($befor_datas->price);

		// } else {
		// 	//財布名がないとき（総資産）
		// 	$wallet_name = "総資産";
		// 	$asset_datas = $wpdb->get_results( $wpdb->prepare(
		// 		"SELECT * FROM $wpdb->asset_total WHERE wallet_name = %s limit %d",
		// 		[$wallet_name, $days]
		// 	));
		//
		// 	//グラフ範囲外の最新priceを初期値に代入
		// 	$befor_datas = $wpdb->get_row( $wpdb->prepare(
		// 		"SELECT * FROM $wpdb->asset_total
		// 		 where created <= %s and wallet_name = %s order by created desc limit 1" ,
		// 		[$end_time, $wallet_name]
		// 	));
		// 	$befor_price = intval($befor_datas->price);
		//
		// }
			//要素数を指定して配列を宣言（やらないと辞書型になる）
			$month_c_asset = [];
			$month_c_asset = array_pad($month_c_asset, $days, 0);
			//残高推移グラフ用配列の作成
			for($i=$days-1; $i>=0; $i--) {
				$today_result = date('Y-m-d', strtotime("-" .$i ." day"));
				foreach($asset_datas as $asset_data){
					$asset_day = $asset_data->created;
					if ($today_result == $asset_day) {
						$befor_price = intval($asset_data->price);
						$month_c_asset[$i] = $befor_price;
						continue;
					}
				}
				//値が無い日付は前日のデータを使用する
				$month_c_asset[$i] = $befor_price;
			}
			echo json_encode($month_c_asset);
			wp_die();
	}
}
add_action( 'wp_ajax_ajax_day_change', 'ajax_day_change' );
add_action( 'wp_ajax_nopriv_ajax_day_change', 'ajax_day_change' );

function ajax_summary_change(){
	$this_month = $_POST['this_month'];
	$start_time = date('Y-m-01', strtotime($this_month. "month"));
	$end_time = date('Y-m-t', strtotime($this_month. "month"));

	$month_money = get_monthly_total($this_month);

	global $wpdb;
	$category_main_all = $wpdb->get_col( $wpdb->prepare(
		"SELECT DISTINCT category_main
		 FROM $wpdb->purchases_category"
	) );

	$row_datas = $wpdb->get_col( $wpdb->prepare(
		"SELECT DISTINCT category_main FROM $wpdb->purchases
		WHERE created BETWEEN %s AND %s",
		[$start_time, $end_time]
	) );

	//存在するカテゴリーを並びかえて配列を作成
	$category_main_datas = [];
	foreach($category_main_all as $category_main) {
		if(in_array($category_main, $row_datas)) {
			array_push($category_main_datas, $category_main);
		}
	}

	$data_count = count($category_main_datas);

	$category_main_sums = [];

	foreach ($category_main_datas as $category_main_data) {
		$category_sum = $wpdb->get_var( $wpdb->prepare(
			"SELECT sum(price) FROM $wpdb->purchases
			WHERE category_main = %s AND created BETWEEN %s AND %s",
			[$category_main_data, $start_time, $end_time]
		));
		array_push($category_main_sums, intval($category_sum));
	}

	$month_sum = $wpdb->get_var( $wpdb->prepare(
		"SELECT sum(price) FROM $wpdb->purchases
		WHERE created BETWEEN %s AND %s",
		[$start_time, $end_time]
	));

	$result = [$month_money, $category_main_datas, $category_main_sums, intval($month_sum)];

	echo json_encode($result);
	wp_die();
}
add_action( 'wp_ajax_ajax_summary_change', 'ajax_summary_change' );
add_action( 'wp_ajax_nopriv_ajax_summary_change', 'ajax_summary_change' );
