<?php
/**
 * E-mail « Ta commande est prête ! » (version HTML)
 */
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p>Bonjour <?php echo esc_html( $order->get_billing_first_name() ); ?>,</p>
<p>Bonne nouvelle : ta commande n°<?php echo esc_html( $order->get_order_number() ); ?> est prête ! Tu peux venir la chercher dès maintenant au comptoir click &amp; collect.</p>

<?php
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );