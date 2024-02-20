<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );


/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

?>
<header class="woocommerce-products-header">
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="woocommerce-products-header__title page-title"><?php //woocommerce_page_title(); ?></h1>
	<?php endif; 

  	
  	// Get the current category
    // $term = get_queried_object();

    // Display the category title
    // echo '<h1>' . $term->name . '</h1>';

    // Include the filter dropdown
    include_once('filter-dropdown.php');
	// echo '<div class="spinner" style="display: none;">Loading...</div>';
  
  	?>

	<div class="loading">
		<div class="overlay"></div>
		<div class="spinner">
			<!-- Replace the content below with your SVG code -->
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; display: block; shape-rendering: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
				<rect x="19" y="19" width="20" height="20" fill="#ff4c00">
				<animate attributeName="fill" values="#f8b26a;#ff4c00;#ff4c00" keyTimes="0;0.125;1" dur="1s" repeatCount="indefinite" begin="0s" calcMode="discrete"></animate>
				</rect><rect x="40" y="19" width="20" height="20" fill="#ff4c00">
				<animate attributeName="fill" values="#f8b26a;#ff4c00;#ff4c00" keyTimes="0;0.125;1" dur="1s" repeatCount="indefinite" begin="0.125s" calcMode="discrete"></animate>
				</rect><rect x="61" y="19" width="20" height="20" fill="#ff4c00">
				<animate attributeName="fill" values="#f8b26a;#ff4c00;#ff4c00" keyTimes="0;0.125;1" dur="1s" repeatCount="indefinite" begin="0.25s" calcMode="discrete"></animate>
				</rect><rect x="19" y="40" width="20" height="20" fill="#ff4c00">
				<animate attributeName="fill" values="#f8b26a;#ff4c00;#ff4c00" keyTimes="0;0.125;1" dur="1s" repeatCount="indefinite" begin="0.875s" calcMode="discrete"></animate>
				</rect><rect x="61" y="40" width="20" height="20" fill="#ff4c00">
				<animate attributeName="fill" values="#f8b26a;#ff4c00;#ff4c00" keyTimes="0;0.125;1" dur="1s" repeatCount="indefinite" begin="0.375s" calcMode="discrete"></animate>
				</rect><rect x="19" y="61" width="20" height="20" fill="#ff4c00">
				<animate attributeName="fill" values="#f8b26a;#ff4c00;#ff4c00" keyTimes="0;0.125;1" dur="1s" repeatCount="indefinite" begin="0.75s" calcMode="discrete"></animate>
				</rect><rect x="40" y="61" width="20" height="20" fill="#ff4c00">
				<animate attributeName="fill" values="#f8b26a;#ff4c00;#ff4c00" keyTimes="0;0.125;1" dur="1s" repeatCount="indefinite" begin="0.625s" calcMode="discrete"></animate>
				</rect><rect x="61" y="61" width="20" height="20" fill="#ff4c00">
				<animate attributeName="fill" values="#f8b26a;#ff4c00;#ff4c00" keyTimes="0;0.125;1" dur="1s" repeatCount="indefinite" begin="0.5s" calcMode="discrete"></animate>
				</rect>
			</svg>
			<p>Loading...</p>
		</div>
	</div>

	<?php
	/**
	 * Hook: woocommerce_archive_description.
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description - 10
	 */
	do_action( 'woocommerce_archive_description' );
	?>
</header>
<?php
if ( woocommerce_product_loop() ) {

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	do_action( 'woocommerce_before_shop_loop' );

	woocommerce_product_loop_start();

	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();

			/**
			 * Hook: woocommerce_shop_loop.
			 */
			do_action( 'woocommerce_shop_loop' );

			wc_get_template_part( 'content', 'product' );
		}
	}

	woocommerce_product_loop_end();

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );
} else {
	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action( 'woocommerce_no_products_found' );
}

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
