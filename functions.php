<?php

/**
 * This file adds functions to the WP Studio child theme (parent: Frost).
 *
 * Only NEW behaviour lives here; frost_setup(), frost_enqueue_stylesheet(),
 * frost_register_block_styles() and frost_register_block_pattern_categories()
 * are already provided by the parent's functions.php, which PHP also loads,
 * so redeclaring them here fatals with "Cannot redeclare function".
 *
 * @package WP_Studio
 */

// Enqueue the child stylesheet after the parent's "frost" handle, so it can override it.
add_action('wp_enqueue_scripts', 'wpstudio_enqueue_stylesheet', 20);
function wpstudio_enqueue_stylesheet()
{

    wp_enqueue_style(
        'wpstudio',
        get_stylesheet_directory_uri() . '/style.css',
        array('frost'),
        wp_get_theme()->get('Version')
    );
}

/**
 * Register the additional block styles introduced for this project.
 *
 * @since 1.0.0
 */
function wpstudio_register_block_styles()
{

    $block_styles = array(
        'core/button' => array(
            'icon-arrow' => __('Con flecha', 'wpstudio'),
        ),
        'core/group' => array(
            'shadow-accent' => __('Accent', 'wpstudio'),
            'price-card' => __('Price Card', 'wpstudio'),
        ),
        'core/list' => array(
            'check-list' => __('Check List', 'wpstudio'),
        ),
        'core/quote' => array(
            'shadow-accent' => __('Accent', 'wpstudio'),
        ),
    );

    foreach ($block_styles as $block => $styles) {
        foreach ($styles as $style_name => $style_label) {
            register_block_style(
                $block,
                array(
                    'name'  => $style_name,
                    'label' => $style_label,
                )
            );
        }
    }
}
add_action('init', 'wpstudio_register_block_styles');

/**
 * ACF-powered shortcodes for the "servicio" custom post type.
 *
 * Placed as literal [shortcode] text inside pattern/query loop markup, they are
 * resolved by do_shortcode() when WordPress renders each post of the loop, so
 * they always read the fields of the post currently being rendered.
 *
 * @since 1.0.0
 */
function wpstudio_register_acf_shortcodes()
{

    // ACF must be active; fail silently otherwise (no fatal on sites without it).
    if (! function_exists('get_field')) {
        return;
    }

    // [acf_field name="precio"] -> raw text value of any ACF field on the current post.
    add_shortcode('acf_field', function ($atts) {
        $atts  = shortcode_atts(array('name' => ''), $atts, 'acf_field');
        $value = $atts['name'] ? get_field($atts['name']) : '';
        return is_scalar($value) ? esc_html($value) : '';
    });

    // [servicio_icono] -> the uploaded SVG/image for the current "servicio", or nothing.
    add_shortcode('servicio_icono', function () {
        $url = get_field('icono');
        if (! $url) {
            return '';
        }
        return sprintf('<img src="%s" alt="" width="40" height="40" loading="lazy" />', esc_url($url));
    });

    // [servicio_badge] -> "Más Popular" pill, only rendered when "destacado" is checked.
    add_shortcode('servicio_badge', function () {
        if (! get_field('destacado')) {
            return '';
        }
        return sprintf('<span class="price-card-badge">%s</span>', esc_html__('Más Popular', 'wpstudio'));
    });

    // [servicio_link] -> permalink of the current "servicio" post.
    add_shortcode('servicio_link', function () {
        return esc_url(get_permalink());
    });
}
add_action('init', 'wpstudio_register_acf_shortcodes');
