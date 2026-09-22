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
            'border-accent' => __('Border Accent', 'wpstudio'),
        ),
        'core/list' => array(
            'check-list' => __('Check List', 'wpstudio'),
        ),
        'core/quote' => array(
            'shadow-accent' => __('Accent', 'wpstudio'),
        ),
        'core/details' => array(
            'border-accent' => __('Border Accent', 'wpstudio'),
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
 * Enqueue the vanilla JS that keeps the FAQ accordion exclusive (only one
 * question open at a time). Native <details> has no such behaviour built in.
 *
 * @since 1.0.0
 */
add_action('wp_enqueue_scripts', 'wpstudio_enqueue_faq_accordion_script');
function wpstudio_enqueue_faq_accordion_script()
{

    wp_enqueue_script(
        'wpstudio-faq-accordion',
        get_stylesheet_directory_uri() . '/assets/js/faq-accordion.js',
        array(),
        wp_get_theme()->get('Version'),
        true
    );
}

/**
 * [servicios_price_cards] -> loops the "diseno-web" CPT and renders a price
 * card per post with its ACF fields (precio, caracteristica_1-4, icono, destacado).
 * Can be dropped anywhere (shortcode block, widget, template) regardless of layout.
 *
 * @since 1.0.0
 */
function wpstudio_register_servicios_price_cards_shortcode()
{
    add_shortcode('servicios_price_cards', 'wpstudio_render_servicios_price_cards');
}
add_action('init', 'wpstudio_register_servicios_price_cards_shortcode');

function wpstudio_render_servicios_price_cards()
{

    // ACF must be active; fail silently otherwise (no fatal on sites without it).
    if (! function_exists('get_field')) {
        return '';
    }

    $servicios = get_posts(array(
        'post_type'      => 'diseno-web',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ));

    if (! $servicios) {
        return '';
    }

    ob_start();
?>
    <div class="servicios-grid">
        <?php foreach ($servicios as $servicio) :
            $id              = $servicio->ID;
            $precio          = get_field('precio', $id);
            $icono           = get_field('icono', $id);
            $destacado       = get_field('destacado', $id);
            $caracteristicas = array_filter(array(
                get_field('caracteristica_1', $id),
                get_field('caracteristica_2', $id),
                get_field('caracteristica_3', $id),
                get_field('caracteristica_4', $id),
            ));
        ?>
            <div class="wp-block-group is-style-price-card is-style-border-accent">

                <?php if ($icono) : ?>
                    <div class="price-card-icon">
                        <img src="<?php echo esc_url($icono); ?>" alt="" width="40" height="40" loading="lazy" />
                    </div>
                <?php endif; ?>

                <div class="price-card-header">
                    <h3 class="price-card-title"><?php echo esc_html(get_the_title($id)); ?></h3>
                    <?php if ($destacado) : ?>
                        <span class="price-card-badge"><?php esc_html_e('Más Popular', 'wpstudio'); ?></span>
                    <?php endif; ?>
                </div>

                <p class="price-card-price has-max-48-font-size"><?php echo esc_html($precio); ?>€</p>

                <hr class="price-card-divider" />

                <?php if ($caracteristicas) : ?>
                    <ul class="wp-block-list is-style-check-list">
                        <?php foreach ($caracteristicas as $caracteristica) : ?>
                            <li><?php echo esc_html($caracteristica); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <div class="wp-block-buttons">
                    <div class="wp-block-button is-style-icon-arrow">
                        <a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(get_permalink($id)); ?>">
                            <?php esc_html_e('Me interesa', 'wpstudio'); ?>
                        </a>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
<?php
    return ob_get_clean();
}
