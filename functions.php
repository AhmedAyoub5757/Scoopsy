<?php
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SCOOPSY_VERSION', '1.0.2' );

function scoopsy_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', array(
        'height'      => 80,
        'width'       => 240,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script',
    ) );

    // Required for Header Footer & Blocks for Elementor
    add_theme_support( 'header-footer-elementor' );

    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'scoopsy' ),
    ) );
}
add_action( 'after_setup_theme', 'scoopsy_setup' );

function scoopsy_assets() {
    wp_enqueue_style( 'scoopsy-style', get_stylesheet_uri(), array(), SCOOPSY_VERSION );
}
add_action( 'wp_enqueue_scripts', 'scoopsy_assets' );

require_once get_template_directory() . '/inc/hero.php';
require_once get_template_directory() . '/inc/elementor-hero.php';

function scoopsy_hero_assets() {
    wp_enqueue_style(
        'scoopsy-fonts',
        'https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Poppins:wght@400;500;600&display=swap',
        array(),
        null
    );
    wp_enqueue_style(
        'scoopsy-hero',
        get_template_directory_uri() . '/assets/css/hero.css',
        array( 'scoopsy-style' ),
        SCOOPSY_VERSION
    );
        wp_enqueue_script(
        'scoopsy-hero',
        get_template_directory_uri() . '/assets/js/hero.js',
        array(),
        SCOOPSY_VERSION,
        array( 'in_footer' => true, 'strategy' => 'defer' )
    );
}
add_action( 'wp_enqueue_scripts', 'scoopsy_hero_assets' );

add_action( 'wp_head', function () {
    echo "<script>document.documentElement.classList.add('sc-js');</script>\n";
}, 1 );

function scoopsy_reveal_assets() {
    wp_enqueue_script(
        'scoopsy-reveal',
        get_template_directory_uri() . '/assets/js/reveal.js',
        array(),
        SCOOPSY_VERSION,
        array( 'in_footer' => true, 'strategy' => 'defer' )
    );
}
add_action( 'wp_enqueue_scripts', 'scoopsy_reveal_assets' );