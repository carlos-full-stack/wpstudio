<?php

/**
 * Price Card Block Registration
 *
 * @package WP_Studio
 */

/**
 * Registra el bloque estático "wpstudio/price-card".
 *
 * @since 1.0.0
 */
function wpstudio_register_price_card_block()
{
	register_block_type( get_theme_file_path( 'blocks/price-card' ) );
}

add_action( 'init', 'wpstudio_register_price_card_block' );

