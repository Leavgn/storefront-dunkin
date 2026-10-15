<?php
/**
 * Thème enfant Storefront Dunkin'.
 * Storefront charge automatiquement le style.css du thème enfant.
 */

// Polices du Design System : Fredoka (titres) et Nunito (textes)
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'dunkin-fonts',
		'https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:wght@400;600;700;800&display=swap',
		array(),
		null
	);
} );