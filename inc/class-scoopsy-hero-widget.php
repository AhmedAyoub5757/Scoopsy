<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Scoopsy_Hero_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'scoopsy_hero';
    }

    public function get_title() {
        return esc_html__( 'Scoopsy Hero', 'scoopsy' );
    }

    public function get_icon() {
        return 'eicon-image-rollover';
    }

    public function get_categories() {
        return array( 'general' );
    }

    protected function register_controls() {
        $flavors = scoopsy_hero_flavors();
        $items   = new \Elementor\Repeater();

        $items->add_control(
            'name',
            array(
                'label'       => esc_html__( 'Title', 'scoopsy' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
                'label_block' => true,
            )
        );
        $items->add_control(
            'word',
            array(
                'label'       => esc_html__( 'Large background word', 'scoopsy' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
                'label_block' => true,
            )
        );
        $items->add_control(
            'tagline',
            array(
                'label'       => esc_html__( 'Description', 'scoopsy' ),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'default'     => '',
            )
        );
        $items->add_control(
            'price',
            array(
                'label'   => esc_html__( 'Price', 'scoopsy' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            )
        );
        foreach ( array( 'cone', 'prop_l', 'prop_m', 'prop_s' ) as $image_key ) {
            $items->add_control(
                $image_key,
                array(
                    'label'   => ucwords( str_replace( '_', ' ', $image_key ) ) . ' image',
                    'type'    => \Elementor\Controls_Manager::MEDIA,
                    'default' => array(),
                )
            );
        }

        $this->start_controls_section(
            'content_section',
            array( 'label' => esc_html__( 'Slides', 'scoopsy' ) )
        );
        $this->add_control(
            'slides',
            array(
                'label'       => esc_html__( 'Flavor slides', 'scoopsy' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $items->get_controls(),
                'default'     => array_map(
                    function ( $flavor ) {
                        return array(
                            'name'    => $flavor['name'],
                            'word'    => $flavor['word'],
                            'tagline' => $flavor['tagline'],
                            'price'   => $flavor['price'],
                        );
                    },
                    $flavors
                ),
                'title_field' => '{{{ name }}}',
            )
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'extras_section',
            array( 'label' => esc_html__( 'Decorative images', 'scoopsy' ) )
        );
        foreach ( array( 'splash', 'wafer', 'ice' ) as $image_key ) {
            $this->add_control(
                $image_key,
                array(
                    'label'   => ucwords( $image_key ) . ' image',
                    'type'    => \Elementor\Controls_Manager::MEDIA,
                    'default' => array(),
                )
            );
        }
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $defaults = scoopsy_hero_flavors();
        $slides   = array();

        foreach ( (array) $settings['slides'] as $index => $slide ) {
            if ( empty( $slide['name'] ) && empty( $slide['tagline'] ) ) {
                continue;
            }
            $default = isset( $defaults[ $index ] ) ? $defaults[ $index ] : $defaults[0];
            $slides[] = array(
                'key'     => $default['key'],
                'name'    => sanitize_text_field( $slide['name'] ),
                'word'    => sanitize_text_field( $slide['word'] ),
                'tagline' => sanitize_textarea_field( $slide['tagline'] ),
                'price'   => sanitize_text_field( $slide['price'] ),
                'bg1'     => $default['bg1'],
                'bg2'     => $default['bg2'],
                'accent'  => $default['accent'],
                'props'   => array(
                    'L' => scoopsy_hero_elementor_image_url( $slide['prop_l'], '' ),
                    'M' => scoopsy_hero_elementor_image_url( $slide['prop_m'], '' ),
                    'S' => scoopsy_hero_elementor_image_url( $slide['prop_s'], '' ),
                ),
                'cone_image' => scoopsy_hero_elementor_image_url( $slide['cone'], '' ),
            );
        }

        if ( ! $slides ) {
            $slides = $defaults;
        }

        foreach ( $slides as &$slide ) {
            if ( ! empty( $slide['cone_image'] ) ) {
                $slide['cone_url'] = $slide['cone_image'];
            }
        }
        unset( $slide );

        $images = array();
        foreach ( array( 'splash', 'wafer', 'ice' ) as $image_key ) {
            $fallback = trailingslashit( get_template_directory_uri() ) . 'assets/hero/extra-' . $image_key . '.png';
            $images[ $image_key ] = scoopsy_hero_elementor_image_url( $settings[ $image_key ], $fallback );
        }

        echo scoopsy_hero_render( $slides, $images );
    }
}
