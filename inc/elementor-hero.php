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
