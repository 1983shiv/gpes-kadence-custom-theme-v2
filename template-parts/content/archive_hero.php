<?php
/**
 * Template part for displaying a post's Hero header
 *
 * @package kadence
 */

namespace Kadence;

$slug = ( is_search() && ! is_post_type_archive( 'product' ) ? 'search' : get_post_type() );
if ( empty( $slug ) ) {
	$queried_object = get_queried_object();
	if ( is_object( $queried_object ) && property_exists( $queried_object, 'taxonomy' ) ) {
		$current_tax = get_taxonomy( $queried_object->taxonomy );
		if ( property_exists( $current_tax, 'object_type' ) ) {
			$post_types = $current_tax->object_type;
			$slug = $post_types[0];
		}
	}
}
$term = get_queried_object();
$category_title = get_field('category_heading', $term);
// var_dump($category_title);
$category_bg = get_field('category_bg', $term);
if (empty($category_bg)) {
    // Set a placeholder image URL
    $category_bg['url'] = 'https://v3.garmentprinting.es/wp-content/uploads/2023/10/Background-final-2.jpg';
}

$front_image = get_field('front_image', $term);
if (empty($front_image)) {
    // Set a placeholder image URL
    $front_image['url'] = 'https://v3.garmentprinting.es/wp-content/uploads/2023/10/g36-300x300.webp';
}

$category_heading = get_field('category_heading', $term);
$button_url = get_field('button_url', $term);
if (empty($button_url)) {
    $button_url = "https://www.garmentprinting.es/quick-quote-generator";
}
$button_label = get_field('button_label', $term);
if (empty($button_label)) {
    $button_label = "Pide un presupuesto rápido";
}


?>
<?php
if(!empty($category_heading)){ 
	?>
	<div class="category-hero" style="background-image: url(' <?php echo ($category_bg["url"]) ;?> ');" >
		<div class="hero-content">
			<div class="left-column">
				<?php if ($category_heading) : ?>
					<h1><?php echo esc_html($category_heading); ?></h1>
				<?php endif; ?>
				<?php
				if ( apply_filters( 'kadence_show_archive_description', ( is_tax() || is_category() || is_tag() || ( is_archive() && ! is_search() && ! is_post_type_archive( 'product' ) ) ) ) ) {
					the_archive_description( '<div class="archive-description">', '</div>' );
				}
				?>
				<a href="<?php echo esc_url($button_url); ?>" class="archive-btn-link"><?php echo ($button_label); ?></a>
			</div>
			<div class="right-column">
				<?php if ($front_image) : ?>
					<img src="<?php echo esc_url($front_image['url']); ?>" alt="<?php echo esc_attr($front_image['alt']); ?>">
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
} else {
	?>
	<section role="banner" class="<?php echo esc_attr( implode( ' ', get_archive_hero_classes() ) ); ?>">
		<div class="entry-hero-container-inner">
			<div class="hero-section-overlay"></div>
			<div class="hero-container site-container">
				<header class="<?php echo esc_attr( implode( ' ', get_archive_title_classes() ) ); ?>">
					<?php
					/**
					 * Kadence Entry Hero
					 *
					 * Hooked kadence_entry_archive_header 10
					 */
					
					do_action( 'kadence_entry_archive_hero', $slug . '_archive', 'above' );
					?>
				</header><!-- .entry-header -->
			</div>
			
		</div>
	</section><!-- .entry-hero -->
<?php ;} ?>

