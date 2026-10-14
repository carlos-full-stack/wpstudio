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
        // Use the file's mtime instead of the theme version so cache busts on every edit.
        filemtime(get_stylesheet_directory() . '/style.css')
    );
}

/**
 * Register the additional block styles introduced for this project.
 *
 * @since 1.0.0
 */
function wpstudio_register_block_styles()
{

    // Los iconos del botón (is-style-icon-arrow, is-style-icon-whatsapp) NO se
    // registran aquí: Gutenberg solo permite un "Style" activo a la vez, así que
    // registrarlos como block style impediría combinarlos con Relleno/Contorno.
    // Se añaden a mano en "Clases CSS adicionales" (ver style.css).
    $block_styles = array(
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
 * Print the custom favicon links in <head>, pointing to the theme's own
 * files instead of relying on WordPress's Site Icon feature.
 *
 * @since 1.0.0
 */
add_action('wp_head', 'wpstudio_favicon_links');
function wpstudio_favicon_links()
{

    $favicon_uri = get_stylesheet_directory_uri() . '/assets/images/favicon';
?>
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url($favicon_uri . '/apple-touch-icon.png'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url($favicon_uri . '/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url($favicon_uri . '/favicon-16x16.png'); ?>">
    <link rel="manifest" href="<?php echo esc_url($favicon_uri . '/site.webmanifest'); ?>">
<?php
}

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
 * Enqueue the vanilla JS that animates elements with the "animate" class
 * when they enter the viewport (own lightweight replacement for AOS).
 *
 * @since 1.0.0
 */
add_action('wp_enqueue_scripts', 'wpstudio_enqueue_scroll_animations_script');
function wpstudio_enqueue_scroll_animations_script()
{

    wp_enqueue_script(
        'wpstudio-scroll-animations',
        get_stylesheet_directory_uri() . '/assets/js/scroll-animations.js',
        array(),
        filemtime(get_stylesheet_directory() . '/assets/js/scroll-animations.js'),
        true
    );
}

/**
 * [calendly_embed]
 * Shortcode para incrustar un calendario de Calendly (inline embed oficial).
 * El script de Calendly solo se carga en las páginas donde se usa este
 * shortcode, nunca de forma global.
 *
 * Atributos: url (obligatorio), height (px, por defecto 700),
 * hide_details (oculta foto/nombre/duración, por defecto true),
 * hide_cookie_banner (oculta el banner de cookies, por defecto true),
 * resize (ajusta la altura al contenido real de Calendly, por defecto true;
 * "height" se usa solo como altura inicial mientras se calcula el ajuste).
 *
 * @since 1.0.0
 */
add_shortcode('calendly_embed', 'wpstudio_render_calendly_embed');
function wpstudio_render_calendly_embed($atts)
{

    $atts = shortcode_atts(array(
        'url'                => '',
        'height'             => '700',
        'hide_details'       => 'false',
        'hide_cookie_banner' => 'true',
        'resize'             => 'false',
    ), $atts);

    if (! $atts['url']) {
        return '';
    }

    $query_args = array();
    if (filter_var($atts['hide_details'], FILTER_VALIDATE_BOOLEAN)) {
        $query_args['hide_event_type_details'] = 1;
    }
    if (filter_var($atts['hide_cookie_banner'], FILTER_VALIDATE_BOOLEAN)) {
        $query_args['hide_gdpr_banner'] = 1;
    }

    $url    = $query_args ? add_query_arg($query_args, $atts['url']) : $atts['url'];
    $height = absint($atts['height']);
    $resize = filter_var($atts['resize'], FILTER_VALIDATE_BOOLEAN);

    ob_start();
?>
    <div class="calendly-inline-widget" data-url="<?php echo esc_url($url); ?>" <?php echo $resize ? 'data-resize="true"' : ''; ?> style="width:100%;min-width:320px;height:<?php echo $height; ?>px;"></div>
    <script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>
<?php
    // Quita los saltos de línea para que wpautop no los convierta en <br>.
    return preg_replace('/\s*\n\s*/', '', ob_get_clean());
}

// Activar shortcode ACF.
add_action('acf/init', function () {
    acf_update_setting('enable_shortcode', true);
});

// Permitir el shortcode en bloques de temas de bloques.
add_filter('acf/shortcode/allow_in_block_themes_outside_content', '__return_true');

// Shortcode para incluir svg en hero servicios

add_shortcode('svg_hero_services', function ($atts) {

    $atts = shortcode_atts([
        'field' => 'imagen_hero'
    ], $atts);

    return get_field($atts['field']) ?: '';
});

// Incluye functions-faq-servicio
require_once get_theme_file_path('inc/functions-faq-servicio.php');

add_action('init', function () {
    register_block_style(
        'core/paragraph',
        [
            'name'  => 'chip',
            'label' => __('Chip', 'mi-tema'),
        ]
    );
});


/**
 * [servicios_price_cards] 
 * Shortcode para mostrar cards con precios
 * (Se opto por shortcode para poder añadir en otras partes de la web)
 *
 * @since 1.0.0
 */
function wpstudio_register_servicios_price_cards_shortcode()
{
    add_shortcode('servicios_price_cards', 'wpstudio_render_servicios_price_cards');
}
add_action('init', 'wpstudio_register_servicios_price_cards_shortcode');

function wpstudio_render_servicios_price_cards($atts)
{

    // ACF must be active; fail silently otherwise (no fatal on sites without it).
    if (! function_exists('get_field')) {
        return '';
    }

    $atts = shortcode_atts(array(
        'ids'   => '',
        'slugs' => '',
    ), $atts);

    $servicios_ids = array();

    if ($atts['ids']) {
        // Respeta el orden de IDs indicado en el shortcode, no el orden de la consulta.
        $servicios_ids = array_map('intval', array_filter(array_map('trim', explode(',', $atts['ids']))));
    } elseif ($atts['slugs']) {
        // Respeta el orden de slugs indicado en el shortcode.
        foreach (array_filter(array_map('trim', explode(',', $atts['slugs']))) as $slug) {
            $post = get_page_by_path($slug, OBJECT, 'diseno-web');
            if ($post) {
                $servicios_ids[] = $post->ID;
            }
        }
    } else {
        $servicios     = get_posts(array(
            'post_type'      => 'diseno-web',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ));
        $servicios_ids = wp_list_pluck($servicios, 'ID');
    }

    if (! $servicios_ids) {
        return '';
    }

    ob_start();
?>
    <div class="servicios-grid">
        <?php foreach ($servicios_ids as $servicio_id) : ?>
            <?php echo wpstudio_render_price_card($servicio_id); ?>
        <?php endforeach; ?>
    </div>
<?php
    // Quita los saltos de línea (no solo entre tags) para que wpautop no los convierta en <br>.
    return preg_replace('/\s*\n\s*/', '', ob_get_clean());
}

/**
 * [servicio_price_card]
 * Shortcode para mostrar una unica price card (por slug o id), para usar
 * en la pagina individual de cada servicio.
 *
 * @since 1.0.0
 */
add_shortcode('servicio_price_card', 'wpstudio_render_servicio_price_card_shortcode');
function wpstudio_render_servicio_price_card_shortcode($atts)
{

    if (! function_exists('get_field')) {
        return '';
    }

    $atts = shortcode_atts(array(
        'slug' => '',
        'id'   => 0,
    ), $atts);

    $servicio_id = 0;

    if ($atts['slug']) {
        $post        = get_page_by_path($atts['slug'], OBJECT, 'diseno-web');
        $servicio_id = $post ? $post->ID : 0;
    } elseif ($atts['id']) {
        $servicio_id = (int) $atts['id'];
    } elseif (is_singular('diseno-web')) {
        // Sin slug/id: usa el servicio actual (plantilla single de diseno-web).
        $servicio_id = get_queried_object_id();
    }

    if (! $servicio_id) {
        return '';
    }

    return wpstudio_render_price_card($servicio_id);
}

/**
 * Renderiza el markup de una price card a partir del ID de un post "diseno-web".
 *
 * @since 1.0.0
 */
function wpstudio_render_price_card($id)
{

    $precio          = get_field('precio', $id);
    $icono           = get_field('icono', $id);
    $destacado       = get_field('destacado', $id);

    // Hasta 6 puntos: reutiliza los campos "caracteristica_X" ya existentes como título y añade "caracteristica_X_descripcion".
    $caracteristicas = array();
    for ($i = 1; $i <= 6; $i++) {
        $titulo = get_field("caracteristica_{$i}", $id);
        if (! $titulo) {
            continue;
        }
        $caracteristicas[] = array(
            'titulo'      => $titulo,
            'descripcion' => get_field("caracteristica_{$i}_descripcion", $id),
        );
    }

    ob_start();
?>
    <div class="wp-block-group is-style-price-card is-style-border-accent">

        <?php if ($icono) : ?>
            <div class="price-card-icon">
                <img src="<?php echo esc_url($icono); ?>" alt="" width="40" height="40" loading="lazy" />
            </div>
        <?php endif; ?>

        <div class="price-card-header">
            <h4 class="price-card-title"><?php echo esc_html(get_the_title($id)); ?></h3>
                <?php if ($destacado) : ?>
                    <span class="price-card-badge"><?php esc_html_e('Más Popular', 'wpstudio'); ?></span>
                <?php endif; ?>
        </div>

        <p class="price-card-price has-max-48-font-size"><?php echo esc_html($precio); ?>€ <span class="price-card-iva"><?php esc_html_e('+ IVA', 'wpstudio'); ?></span></p>

        <hr class="price-card-divider" />

        <?php if ($caracteristicas) : ?>
            <ul class="wp-block-list is-style-check-list">
                <?php foreach ($caracteristicas as $caracteristica) : ?>
                    <li>
                        <span class="price-card-feature-title"><?php echo esc_html($caracteristica['titulo']); ?></span>
                        <?php if ($caracteristica['descripcion']) : ?>
                            <span class="price-card-feature-description"><?php echo esc_html($caracteristica['descripcion']); ?></span>
                        <?php endif; ?>
                    </li>
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
<?php
    // Quita los saltos de línea (no solo entre tags) para que wpautop no los convierta en <br>.
    return preg_replace('/\s*\n\s*/', '', ob_get_clean());
}

/**
 * Botón flotante de WhatsApp.
 * Visible solo en la portada y en la página individual de cada servicio
 * (CPT "diseno-web"); usa el filtro "wpstudio_whatsapp_cta_display" para
 * cambiar fácilmente en qué páginas aparece en el futuro.
 *
 * @since 1.0.0
 */
function wpstudio_whatsapp_cta_should_display()
{

    return (bool) apply_filters('wpstudio_whatsapp_cta_display', is_front_page() || is_singular('diseno-web'));
}

add_action('wp_enqueue_scripts', 'wpstudio_enqueue_whatsapp_cta_script');
function wpstudio_enqueue_whatsapp_cta_script()
{

    if (! wpstudio_whatsapp_cta_should_display()) {
        return;
    }

    wp_enqueue_script(
        'wpstudio-whatsapp-cta',
        get_stylesheet_directory_uri() . '/assets/js/whatsapp-cta.js',
        array(),
        filemtime(get_stylesheet_directory() . '/assets/js/whatsapp-cta.js'),
        true
    );
}

add_action('wp_footer', 'wpstudio_render_whatsapp_cta');
function wpstudio_render_whatsapp_cta()
{

    if (! wpstudio_whatsapp_cta_should_display()) {
        return;
    }

    // Número de WhatsApp: cambia el filtro para usar otro en el futuro.
    $numero = apply_filters('wpstudio_whatsapp_cta_number', '34694908071');

    if (is_singular('diseno-web')) {
        // Mensaje sin género ("el servicio de X" en lugar de "un/una X").
        $mensaje = sprintf(
            /* translators: %s: nombre del servicio */
            __('Hola Carlos, tengo interés en el servicio de %s', 'wpstudio'),
            get_the_title()
        );
    } else {
        $mensaje = __('Hola Carlos, he visto tu web y me gustaría hablar de mi proyecto', 'wpstudio');
    }

    $mensaje = apply_filters('wpstudio_whatsapp_cta_message', $mensaje);
    $enlace  = 'https://wa.me/' . $numero . '?text=' . rawurlencode($mensaje);
?>
    <a href="<?php echo esc_url($enlace); ?>" class="wpstudio-whatsapp-cta" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Contactar por WhatsApp', 'wpstudio'); ?>">
        <svg width="22" height="22" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.248c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.337-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z" />
        </svg>
        <span><?php esc_html_e('WhatsApp', 'wpstudio'); ?></span>
    </a>
<?php
}
