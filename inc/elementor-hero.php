<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function scoopsy_hero_elementor_image_url( $image, $fallback ) {
    if ( is_array( $image ) && ! empty( $image['url'] ) ) {
        return esc_url_raw( $image['url'] );
    }

    if ( is_numeric( $image ) ) {
        $url = wp_get_attachment_image_url( (int) $image, 'full' );
        if ( $url ) {
            return esc_url_raw( $url );
        }
    }

    return $fallback;
}

function scoopsy_register_hero_widget( $widgets_manager ) {
    require_once get_template_directory() . '/inc/class-scoopsy-hero-widget.php';
    $widgets_manager->register( new \Scoopsy_Hero_Widget() );
}
add_action( 'elementor/widgets/register', 'scoopsy_register_hero_widget' );

function scoopsy_register_offer_widget( $widgets_manager ) {
    require_once get_template_directory() . '/inc/class-scoopsy-offer-widget.php';
    $widgets_manager->register( new \Scoopsy_Offer_Widget() );
}
add_action( 'elementor/widgets/register', 'scoopsy_register_offer_widget' );

function scoopsy_register_offer_assets() {
    wp_register_style(
        'scoopsy-offer',
        get_template_directory_uri() . '/assets/css/offer.css',
        array( 'scoopsy-style' ),
        SCOOPSY_VERSION
    );
    wp_register_script(
        'scoopsy-offer',
        get_template_directory_uri() . '/assets/js/offer.js',
        array( 'elementor-frontend' ),
        SCOOPSY_VERSION,
        array(
            'in_footer' => true,
            'strategy'   => 'defer',
        )
    );
}
add_action( 'wp_enqueue_scripts', 'scoopsy_register_offer_assets' );
