<?php
/**
 * Displays the header content
 *
 * @package Theme Freesia
 * @subpackage Freesia Empire
 * @since Freesia Empire 1.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<?php
$freesiaempire_settings = freesiaempire_get_theme_options(); ?>
<head>
<!-- noindex -->
<meta name="robots" content="noindex">
<!-- レスポンシブ viewport -->
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<!-- chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
<!-- jQuery	-->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet"/>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!-- jQueryソート機能付きテーブル -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.1/js/jquery.tablesorter.min.js"></script>
<!-- jQueryカレンダー -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/redmond/jquery-ui.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>

<meta charset="<?php bloginfo( 'charset' ); ?>" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php
	if ( function_exists( 'wp_body_open' ) ) {

		wp_body_open();

	} ?>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content','freesia-empire');?></a>
<!-- Masthead ============================================= -->
<header id="masthead" class="site-header" role="banner">
	<?php
				if($header_image = $freesiaempire_settings['freesiaempire_display_header_image'] == 'top'){
					do_action('freesiaempire_header_image');
				}
				echo '<div class="top-header">
						<div class="container clearfix">';
						do_action('freesiaempire_site_branding');

						echo '<div class="menu-toggle">
								<div class="line-one"></div>
								<div class="line-two"></div>
								<div class="line-three"></div>
							</div>';

						echo '<div class="header-info clearfix">';
							if(has_nav_menu('social-link') && $freesiaempire_settings['freesiaempire_top_social_icons'] == 0):
								echo '<div class="header-social-block">';
									do_action('social_links');
								echo '</div>'.'<!-- end .header-social-block -->';
							endif;
							if( is_active_sidebar( 'freesiaempire_header_info' )) {
								dynamic_sidebar( 'freesiaempire_header_info' );
							}
						echo ' </div> <!-- end .header-info -->';
						$search_form = $freesiaempire_settings['freesiaempire_search_custom_header'];
						if (1 != $search_form) { ?>
							<button id="search-toggle" class="header-search" type="button"></button>
							<div id="search-box" class="clearfix">
								<?php get_search_form();?>
							</div>  <!-- end #search-box -->
						<?php }

					echo '</div> <!-- end .container -->
				</div> <!-- end .top-header -->';
			if($header_image = $freesiaempire_settings['freesiaempire_display_header_image'] == 'below'){
				do_action('freesiaempire_header_image');
			}
			?>
	<!-- Main Header============================================= -->
	<div id="sticky_header">
		<div class="container clearfix">
			<!-- Main Nav ============================================= -->
			<?php
				if (has_nav_menu('primary')) { ?>
			<?php $args = array(
				'theme_location' => 'primary',
				'container'      => '',
				'items_wrap'     => '<ul id="primary-menu" class="menu nav-menu">%3$s</ul>',
				); ?>
			<nav id="site-navigation" class="main-navigation clearfix" role="navigation" aria-label="<?php esc_attr_e('Main Menu','freesia-empire');?>">
				<button class="menu-toggle-2" aria-controls="primary-menu" aria-expanded="false"></button>
					  	<!-- end .menu-toggle -->
				<?php wp_nav_menu($args);//extract the content from apperance-> nav menu ?>
			</nav> <!-- end #site-navigation -->
			<?php } else {// extract the content from page menu only ?>
			<nav id="site-navigation" class="main-navigation clearfix" role="navigation" aria-label="<?php esc_attr_e('Main Menu','freesia-empire');?>">
				<button class="menu-toggle-2" aria-controls="primary-menu" aria-expanded="false"></button>
					  	<!-- end .menu-toggle -->
				<?php	wp_page_menu(array('menu_class' => 'menu', 'items_wrap'     => '<ul id="primary-menu" class="menu nav-menu">%3$s</ul>')); ?>
			</nav> <!-- end #site-navigation -->
			<?php } ?>
		</div> <!-- end .container -->
	</div> <!-- end #sticky_header -->
	<?php
		$enable_slider = $freesiaempire_settings['freesiaempire_enable_slider'];
		freesiaempire_slider_value();
		if ($enable_slider=='frontpage'|| $enable_slider=='enitresite'){
			if(is_front_page() && ($enable_slider=='frontpage') ) {
				if($freesiaempire_settings['freesiaempire_slider_type'] == 'default_slider') {
						freesiaempire_page_sliders();
				}else{
					if(class_exists('Freesia_Empire_Plus_Features')):
						freesiaempire_image_sliders();
					endif;
				}
			}
			if($enable_slider=='enitresite'){
				if($freesiaempire_settings['freesiaempire_slider_type'] == 'default_slider') {
						freesiaempire_page_sliders();
				}else{
					if(class_exists('Freesia_Empire_Plus_Features')):
						freesiaempire_image_sliders();
					endif;
				}
			}
		}
		if(!is_page_template('page-templates/freesiaempire-corporate.php') && !is_page_template('alter-front-page-template.php')) {
			if (('' != freesiaempire_header_title()) || function_exists('bcn_display_list')) {
				if(is_home()){
					if($freesiaempire_settings['freesiaempire_blog_header_display'] == 'show'){ ?>
						<div class="page-header clearfix">
							<div class="container">
									<h2 class="page-title"><?php echo freesiaempire_header_title();?></h2> <!-- .page-title -->
									<?php freesiaempire_breadcrumb(); ?>
							</div> <!-- .container -->
						</div> <!-- .page-header -->
					<?php }
				} else { ?>
						<div class="page-header clearfix">
							<div class="container">
									<?php if ( is_front_page()) : ?>
										<h2 class="page-title"><?php echo freesiaempire_header_title(); ?></h2>
										<!-- .page-title -->
									<?php else : ?>
										<h1 class="page-title"><?php echo freesiaempire_header_title(); ?></h1>
										<!-- .page-title -->
									<?php endif; ?>
									<?php freesiaempire_breadcrumb(); ?>
							</div> <!-- .container -->
						</div> <!-- .page-header -->
				<?php }
			}
		} ?>
</header> <!-- end #masthead -->
<!-- Main Page Start ============================================= -->
<div id="content">
<?php if (!is_page_template('page-templates/freesiaempire-corporate.php') ){
  if(is_page_template('three-column-blog-template.php') || is_page_template('our-team-template.php') || is_page_template('about-us-template.php') || is_page_template('portfolio-template.php') ){

	}else{?>
<div class="container clearfix">
<?php }
}
	//サブメニュー追加と現在のページ名に色を付ける
	if(is_page('accounts') || is_page('service-list') || is_page('edit-manual') || is_page('show-manual')) {
		wp_nav_menu( array('menu' => 'GAccountsMenu' ) );
		?>
		<script>
			$("#menu-item-20").addClass("page_item");
			$("#menu-item-29").addClass("page_item");
		</script>
		<?php
	} elseif (is_page('household') || is_page('summary') || is_page('monthly')) {
		wp_nav_menu( array('menu' => 'HouseMenu' ) );
		?>
		<script>
			$("#menu-item-22").addClass("page_item");
			$("#menu-item-59").addClass("page_item");
		</script>
		<?php
	} elseif (is_page('portfolio') || is_page('history') || is_page('liability') || is_page('balance-sheet')) {
		wp_nav_menu( array('menu' => 'PortfolioMenu' ) );
		?>
		<script>
			$("#menu-item-21").addClass("page_item");
			$("#menu-item-110").addClass("page_item");
		</script>
		<?php
	}

	?>
