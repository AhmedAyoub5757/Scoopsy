<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Scoopsy_Offer_Widget extends \Elementor\Widget_Base {
	public function get_name() {
		return 'scoopsy_offer';
	}

	public function get_title() {
		return esc_html__( 'Scoopsy Offer Banner', 'scoopsy' );
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	public function get_categories() {
		return array( 'general' );
	}

	public function get_keywords() {
		return array( 'offer', 'banner', 'promo', 'scoopsy' );
	}

	public function get_style_depends() {
		return array( 'scoopsy-offer' );
	}

	public function get_script_depends() {
		return array( 'scoopsy-offer' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Offer content', 'scoopsy' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'label',
			array(
				'label'   => esc_html__( 'Label', 'scoopsy' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Limited time offer', 'scoopsy' ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'scoopsy' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Buy 2 cones, get the 3rd free', 'scoopsy' ),
			)
		);
		$this->add_control(
			'description',
			array(
				'label'   => esc_html__( 'Description', 'scoopsy' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Mix any flavors you like. Use the code below when you order and your third cone is on us.', 'scoopsy' ),
			)
		);
		$this->add_control(
			'coupon',
			array(
				'label'   => esc_html__( 'Coupon code', 'scoopsy' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'SCOOP3',
			)
		);
		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'Button text', 'scoopsy' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Claim the offer', 'scoopsy' ),
			)
		);
		$this->add_control(
			'button_link',
			array(
				'label'   => esc_html__( 'Button link', 'scoopsy' ),
				'type'    => \Elementor\Controls_Manager::URL,
				'default' => array( 'url' => '#order' ),
			)
		);

		$cone_defaults = array(
			'left'   => 'cone-strawberry.png',
			'center' => 'cone-mango.png',
			'right'  => 'cone-pistachio.png',
		);
		foreach ( $cone_defaults as $position => $filename ) {
			$this->add_control(
				'cone_' . $position,
				array(
					'label'   => sprintf(
						/* translators: %s is the cone position. */
						esc_html__( '%s cone', 'scoopsy' ),
						ucfirst( $position )
					),
					'type'    => \Elementor\Controls_Manager::MEDIA,
					'default' => array(
						'url' => trailingslashit( get_template_directory_uri() ) . 'assets/hero/' . $filename,
					),
				)
			);
		}
		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			array(
				'label' => esc_html__( 'Colors', 'scoopsy' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'gradient_start',
			array(
				'label'     => esc_html__( 'Gradient start', 'scoopsy' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#E8386D',
				'selectors' => array(
					'{{WRAPPER}} .sc-offer' => '--sc-offer-1: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'gradient_end',
			array(
				'label'     => esc_html__( 'Gradient end', 'scoopsy' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FF6F91',
				'selectors' => array(
					'{{WRAPPER}} .sc-offer' => '--sc-offer-2: {{VALUE}};',
				),
			)
		);
		$this->end_controls_section();
	}

	private function get_image_data( $image, $fallback, $alt ) {
		$url      = $fallback;
		$image_alt = '';

		if ( is_array( $image ) ) {
			if ( ! empty( $image['url'] ) ) {
				$url = $image['url'];
			}
			if ( ! empty( $image['alt'] ) ) {
				$image_alt = $image['alt'];
			}
		} elseif ( is_numeric( $image ) ) {
			$attachment_url = wp_get_attachment_image_url( (int) $image, 'full' );
			if ( $attachment_url ) {
				$url = $attachment_url;
			}
			$image_alt = get_post_meta( (int) $image, '_wp_attachment_image_alt', true );
		}

		return array(
			'url' => esc_url( $url ),
			'alt' => esc_attr( $image_alt ? $image_alt : $alt ),
		);
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$base_url = trailingslashit( get_template_directory_uri() ) . 'assets/hero/';
		$element_id = ! empty( $settings['_element_id'] ) ? sanitize_html_class( $settings['_element_id'] ) : 'offer';
		$element_id = $element_id ? $element_id : 'offer';
		$gradient_start = sanitize_hex_color( isset( $settings['gradient_start'] ) ? $settings['gradient_start'] : '' );
		$gradient_end   = sanitize_hex_color( isset( $settings['gradient_end'] ) ? $settings['gradient_end'] : '' );
		$gradient_start = $gradient_start ? $gradient_start : '#E8386D';
		$gradient_end   = $gradient_end ? $gradient_end : '#FF6F91';
		$link = ! empty( $settings['button_link']['url'] ) ? $settings['button_link']['url'] : '#order';
		$link_target = ! empty( $settings['button_link']['is_external'] ) ? ' target="_blank"' : '';
		$link_rel = ! empty( $settings['button_link']['nofollow'] ) ? ' rel="nofollow"' : '';
		$cones = array(
			'1' => $this->get_image_data( $settings['cone_left'], $base_url . 'cone-strawberry.png', esc_html__( 'Strawberry ice cream cone', 'scoopsy' ) ),
			'2' => $this->get_image_data( $settings['cone_center'], $base_url . 'cone-mango.png', esc_html__( 'Mango ice cream cone', 'scoopsy' ) ),
			'3' => $this->get_image_data( $settings['cone_right'], $base_url . 'cone-pistachio.png', esc_html__( 'Pistachio ice cream cone', 'scoopsy' ) ),
		);
		?>
		<section
			class="sc-offer"
			id="<?php echo esc_attr( $element_id ); ?>"
			style="--sc-offer-1:<?php echo esc_attr( $gradient_start ); ?>;--sc-offer-2:<?php echo esc_attr( $gradient_end ); ?>;"
		>
			<div class="sc-offer__content">
				<span class="sc-offer__label"><?php echo esc_html( $settings['label'] ); ?></span>
				<h2 class="sc-offer__title"><?php echo esc_html( $settings['title'] ); ?></h2>
				<p class="sc-offer__text"><?php echo wp_kses_post( $settings['description'] ); ?></p>
				<span class="sc-offer__code"><?php echo esc_html( $settings['coupon'] ); ?></span>
				<a class="sc-offer__btn" href="<?php echo esc_url( $link ); ?>"<?php echo $link_target . $link_rel; ?>>
					<?php echo esc_html( $settings['button_text'] ); ?>
				</a>
			</div>
			<div class="sc-offer__media" aria-hidden="true">
				<?php foreach ( $cones as $number => $cone ) : ?>
					<img
						class="sc-offer__cone sc-offer__cone--<?php echo esc_attr( $number ); ?>"
						src="<?php echo esc_url( $cone['url'] ); ?>"
						alt="<?php echo esc_attr( $cone['alt'] ); ?>"
						loading="lazy"
					>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
}
