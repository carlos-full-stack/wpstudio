<?php

/**
 * Render: wpstudio/faq-servicio
 *
 * Se ejecuta en tiempo de renderizado del bloque (no en `init`), así que
 * aquí SÍ hay contexto real del post actual. Reutiliza las mismas clases
 * que el patrón "wpstudio/faq" para que el resultado sea visualmente idéntico.
 *
 * Lee los campos ya registrados en ACF (faq_1_pregunta, faq_1_respuesta...
 * faq_4_pregunta, faq_4_respuesta) en lugar de duplicar datos con post meta
 * propio: ACF ya es la fuente de verdad y ya tiene su propia UI de edición.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined('ABSPATH') || exit;

if (! function_exists('get_field')) {
    return; // ACF no está activo.
}

$post_id = $block->context['postId'] ?? get_the_ID();

// El grupo de campos ACF de las FAQs está vinculado al CPT "diseno-web" (no existe un CPT "servicio").
if (! $post_id || 'diseno-web' !== get_post_type($post_id)) {
    return;
}

$faqs = array();

for ($i = 1; $i <= 4; $i++) {
    $pregunta  = get_field("faq_{$i}_pregunta", $post_id);
    $respuesta = get_field("faq_{$i}_respuesta", $post_id);

    if ($pregunta && $respuesta) {
        $faqs[] = array(
            'pregunta'  => $pregunta,
            'respuesta' => $respuesta,
        );
    }
}

if (empty($faqs)) {
    return;
}

$wrapper_attributes = get_block_wrapper_attributes(array('class' => 'wp-faq-accordion'));
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php foreach ($faqs as $index => $faq) : ?>
        <details class="wp-block-details wp-faq-item is-style-border-accent rounded-lg" <?php echo 0 === $index ? ' open' : ''; ?>>
            <summary><?php echo esc_html($faq['pregunta']); ?></summary>
            <p><?php echo esc_html($faq['respuesta']); // Si cambias el campo a WYSIWYG en ACF, usa wp_kses_post() aquí en su lugar. 
                ?></p>
        </details>
    <?php endforeach; ?>
</div>
