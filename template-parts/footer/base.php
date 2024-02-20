<?php
/**
 * Template part for displaying the footer info
 *
 * @package kadence
 */

namespace Kadence;

if ( kadence()->has_content() ) {
	kadence()->print_styles( 'kadence-content' );
}
kadence()->print_styles( 'kadence-footer' );


$term = get_queried_object();
$get_quote_php_new = get_field('get_quote_php_new', $term);
$display_rating_and_brand_slider = get_field('display_rating_and_brand_slider', $term);
$display_other_sections_php = get_field('display_other_sections_php', $term);
$get_quote_php = get_field('get_quote_php', $term);

// get_quote_php_new

if($get_quote_php_new){
	// $post_id = 877855 for new quote block;
	$post_content = get_post_field('post_content', $get_quote_php_new);
	echo apply_filters('the_content', $post_content);
}

if($display_rating_and_brand_slider){
	// $post_id = 877847 for ratign and slider draft;
	$post_content = get_post_field('post_content', $display_rating_and_brand_slider);
	echo apply_filters('the_content', $post_content);
}

if($display_other_sections_php){
	// 
	$post_content = get_post_field('post_content', $display_other_sections_php);
	echo apply_filters('the_content', $post_content);
}

if($get_quote_php){
	// $post_id = 877850 for get quote section post draft;
	$post_content = get_post_field('post_content', $get_quote_php);
	echo apply_filters('the_content', $post_content);
}

?>

<footer id="colophon" class="site-footer" role="contentinfo">
	<div class="site-footer-wrap">
		<?php
		/**
		 * Kadence Top footer
		 *
		 * Hooked Kadence\top_footer
		 */
		do_action( 'kadence_top_footer' );
		/**
		 * Kadence Middle footer
		 *
		 * Hooked Kadence\middle_footer
		 */
		do_action( 'kadence_middle_footer' );
		/**
		 * Kadence Bottom footer
		 *
		 * Hooked Kadence\bottom_footer
		 */
		do_action( 'kadence_bottom_footer' );
		?>
	</div>
</footer><!-- #colophon -->

