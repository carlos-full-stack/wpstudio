<?php

/**
 * FAQs del CPT "diseno-web" — usando los campos ACF ya existentes
 * (faq_1_pregunta, faq_1_respuesta ... faq_4_pregunta, faq_4_respuesta).
 *
 * Incluir desde functions.php:
 *   require get_theme_file_path( 'inc/functions-faq-servicio.php' );
 *
 * No se registra ningún post meta ni metabox propio: ACF ya gestiona
 * esos campos y su edición. Este archivo solo registra el bloque
 * dinámico que los pinta en el front.
 */

defined('ABSPATH') || exit;

function wpstudio_register_faq_servicio_block()
{
    // get_theme_file_path() resuelve siempre desde la raíz del tema activo,
    // sin importar desde qué archivo se llame — evita el típico lío de
    // rutas relativas cuando este código vive dentro de inc/.
    register_block_type(get_theme_file_path('blocks/faq-servicio'));
}
add_action('init', 'wpstudio_register_faq_servicio_block');
