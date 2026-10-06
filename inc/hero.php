<?php
if (! defined('ABSPATH')) exit;

/**
 * Flavor data. Add or edit flavors here only.
 */
function scoopsy_hero_flavors()
{
    return array(
        array(
            'key'     => 'strawberry',
            'name'    => 'Strawberry Blush',
            'word'    => 'STRAWBERRY',
            'tagline' => 'Sun-ripened berries in velvety cream.',
            'price'   => '$4.99',
            'bg1'     => '#FFD1DC',
            'bg2'     => '#FF8FAB',
            'accent'  => '#FF4D79',
        ),
        array(
            'key'     => 'mango',
            'name'    => 'Mango Sunrise',
            'word'    => 'MANGO',
            'tagline' => 'Golden, juicy mango. Bright and tropical.',
            'price'   => '$5.49',
            'bg1'     => '#FFE3A3',
            'bg2'     => '#FFB347',
            'accent'  => '#F28C00',
        ),
        array(
            'key'     => 'pistachio',
            'name'    => 'Pistachio Dream',
            'word'    => 'PISTACHIO',
            'tagline' => 'Stone-ground pistachios, silky and nutty.',
            'price'   => '$5.99',
            'bg1'     => '#DDF2D1',
            'bg2'     => '#A8D5A2',
            'accent'  => '#5E9E57',
        ),
    );
}

function scoopsy_hero_default_images()
{
    $img = trailingslashit( get_template_directory_uri() ) . 'assets/hero/';

    return array(
        'splash' => $img . 'extra-splash.png',
        'wafer'  => $img . 'extra-wafer.png',
        'ice'    => $img . 'extra-ice.png',
    );
}

function scoopsy_hero_render($flavors = null, $images = null)
{
    $flavors = is_array($flavors) && $flavors ? $flavors : scoopsy_hero_flavors();
    $images  = wp_parse_args((array) $images, scoopsy_hero_default_images());
    $n       = count($flavors);
    $half    = (int) floor($n / 2);
    $img     = trailingslashit(get_template_directory_uri()) . 'assets/hero/';
    ob_start();
?>
    <div class="sc-hero" data-autoplay="5000" data-count="<?php echo (int) $n; ?>" style="--ap:5000ms">

        <!-- Background gradients (cross-fade) -->
        <div class="sc-hero__bgs" aria-hidden="true">
            <?php foreach ($flavors as $i => $f) : ?>
                <div class="sc-bg<?php echo 0 === $i ? ' is-active' : ''; ?>"
                    style="--bg1:<?php echo esc_attr($f['bg1']); ?>;--bg2:<?php echo esc_attr($f['bg2']); ?>"></div>
            <?php endforeach; ?>
        </div>

        <!-- Giant name behind the cone (font-size auto-fitted to word length) -->
        <div class="sc-hero__words" aria-hidden="true">
            <?php foreach ($flavors as $i => $f) :
                $fs = min(22, round(88 / (strlen($f['word']) * 0.58), 1)); ?>
                <span class="sc-word<?php echo 0 === $i ? ' is-active' : ''; ?>" style="--fs:<?php echo esc_attr($fs); ?>">
                    <?php echo esc_html($f['word']); ?>
                </span>
            <?php endforeach; ?>
        </div>

        <!-- Shared extras -->
        <span class="sc-layer sc-layer--splash" style="--d:14" aria-hidden="true">
            <img class="sc-float" src="<?php echo esc_url($images['splash']); ?>" alt="">
        </span>
        <span class="sc-layer sc-layer--wafer" style="--d:46" aria-hidden="true">
            <img class="sc-float" src="<?php echo esc_url($images['wafer']); ?>" alt="" loading="lazy">
        </span>
        <span class="sc-layer sc-layer--ice" style="--d:64" aria-hidden="true">
            <img class="sc-float" src="<?php echo esc_url($images['ice']); ?>" alt="" loading="lazy">
        </span>

        <!-- Cones on the arc -->
        <div class="sc-arc">
            <?php foreach ($flavors as $i => $f) :
                $o = ($i + $n + $half) % $n - $half; ?>
                <div class="sc-cone<?php echo 0 === $i ? ' is-active' : ''; ?>"
                    data-index="<?php echo (int) $i; ?>"
                    style="--o:<?php echo (int) $o; ?>;--a:<?php echo 0 === $i ? 1 : 0; ?>">
                    <div class="sc-cone__tilt">
                        <img class="sc-float"
                            src="<?php echo esc_url(! empty($f['cone_url']) ? $f['cone_url'] : $img . 'cone-' . $f['key'] . '.png'); ?>"
                            alt="<?php echo esc_attr($f['name'] . ' ice cream cone'); ?>"
                            <?php echo 0 === $i ? '' : 'loading="lazy"'; ?>>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Floating props (one set per flavor) -->
        <?php foreach ($flavors as $i => $f) : ?>
            <div class="sc-propset<?php echo 0 === $i ? ' is-active' : ''; ?>" data-index="<?php echo (int) $i; ?>" aria-hidden="true">
                <?php foreach (array('L' => 52, 'M' => 34, 'S' => 76) as $size => $depth) : ?>
                    <span class="sc-layer sc-layer--<?php echo esc_attr($size); ?>" style="--d:<?php echo (int) $depth; ?>">
                        <span class="sc-prop">
                            <img class="sc-float"
                                src="<?php echo esc_url(isset($f['props']) && ! empty($f['props'][$size]) ? $f['props'][$size] : $img . 'prop-' . $f['key'] . '-' . $size . '.png'); ?>"
                                alt="" loading="lazy">
                        </span>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <!-- Bottom-left: description (no buttons) -->
        <div class="sc-hero__content">
            <?php foreach ($flavors as $i => $f) :
                $tag = (0 === $i) ? 'h1' : 'h2'; ?>
                <div class="sc-text<?php echo 0 === $i ? ' is-active' : ''; ?>">
                    <<?php echo $tag; ?> class="sc-text__title"><?php echo esc_html($f['name']); ?></<?php echo $tag; ?>>
                    <p class="sc-text__tag"><?php echo esc_html($f['tagline']); ?></p>
                    <span class="sc-text__price"><?php echo esc_html($f['price']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Bottom-right: slider indicator -->
        <!-- Bottom-right: slider indicator (dots) -->
        <div class="sc-indicator" role="tablist" aria-label="Choose flavor">
            <?php foreach ($flavors as $i => $f) : ?>
                <button class="sc-ind<?php echo 0 === $i ? ' is-active' : ''; ?>" type="button" role="tab"
                    data-index="<?php echo (int) $i; ?>"
                    aria-label="<?php echo esc_attr($f['name']); ?>">
                    <span class="sc-ind__bar"></span>
                </button>
            <?php endforeach; ?>
        </div>

    </div>
<?php
    return ob_get_clean();
}

/**
 * [scoopsy_hero] shortcode
 */
function scoopsy_hero_shortcode()
{
    return scoopsy_hero_render();
}
add_shortcode('scoopsy_hero', 'scoopsy_hero_shortcode');
