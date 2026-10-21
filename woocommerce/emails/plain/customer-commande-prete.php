<?php
/**
 * E-mail « Ta commande est prête ! » (version texte brut)
 */
defined( 'ABSPATH' ) || exit;

echo '= ' . esc_html( wp_strip_all_tags( $email_heading ) ) . " =\n\n";
echo 'Bonjour ' . esc_html( $order->get_billing_first_name() ) . ",\n\n";
echo 'Bonne nouvelle : ta commande n°' . esc_html( $order->get_order_number() ) . " est prête ! Tu peux venir la chercher dès maintenant au comptoir click & collect.\n\n";

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

if ( $additional_content ) {
	echo "\n" . esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) ) . "\n";
}