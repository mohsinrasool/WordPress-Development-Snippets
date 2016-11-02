<?php
/*
Plugin Name: Woocommerce Product for Divi
Description: This plugin enables Divi for products post type and adds [woocommerce_product_details] shortcode to be used in Divi template to display product's woocommerce details.
Author: Mohsin Rasool
Version: 1.0
*/

// Enable Divi template for product cpt
function my_et_builder_post_types( $post_types ) {
    $post_types[] = 'product';

    return $post_types;
}
add_filter( 'et_builder_post_types', 'my_et_builder_post_types' );


// add shortcode for woocommerce product details
function woocommerce_product_details() {

	if(!function_exists('woocommerce_get_product_schema'))
		return '';

    ob_start();

    do_action( 'woocommerce_before_single_product' );

     if ( post_password_required() ) {
        echo get_the_password_form();
        return;
     }
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

    <?php
        /**
         * woocommerce_before_single_product_summary hook.
         *
         * @hooked woocommerce_show_product_sale_flash - 10
         * @hooked woocommerce_show_product_images - 20
         */
        do_action( 'woocommerce_before_single_product_summary' );
    ?>

    <div class="summary entry-summary">

        <?php
            /**
             * woocommerce_single_product_summary hook.
             *
             * @hooked woocommerce_template_single_title - 5
             * @hooked woocommerce_template_single_rating - 10
             * @hooked woocommerce_template_single_price - 10
             * @hooked woocommerce_template_single_excerpt - 20
             * @hooked woocommerce_template_single_add_to_cart - 30
             * @hooked woocommerce_template_single_meta - 40
             * @hooked woocommerce_template_single_sharing - 50
             */
            do_action( 'woocommerce_single_product_summary' );
        ?>

    </div><!-- .summary -->

    <?php
        /**
         * woocommerce_after_single_product_summary hook.
         *
         * @hooked woocommerce_output_product_data_tabs - 10
         * @hooked woocommerce_upsell_display - 15
         * @hooked woocommerce_output_related_products - 20
         */
        do_action( 'woocommerce_after_single_product_summary' );
    ?>

    <meta itemprop="url" content="<?php the_permalink(); ?>" />

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' );

    return ob_get_clean();

}
add_shortcode( 'woocommerce_product_details','woocommerce_product_details' );
