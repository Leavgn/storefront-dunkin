<?php
/**
 * E-mail client « Ta commande est prête ! »
 * Envoyé quand une commande passe à l'état « Prête à retirer »
 */
defined( 'ABSPATH' ) || exit;

class Dunkin_Email_Prete extends WC_Email {

	public function __construct() {
		$this->id             = 'dunkin_commande_prete';
		$this->customer_email = true;
		$this->title          = 'Commande prête à retirer';
		$this->description    = 'Envoyé au client quand sa commande passe à l\'état « Prête à retirer ».';
		$this->template_html  = 'emails/customer-commande-prete.php';
		$this->template_plain = 'emails/plain/customer-commande-prete.php';
		$this->placeholders   = array(
			'{order_date}'   => '',
			'{order_number}' => '',
		);

		add_action( 'woocommerce_order_status_prete_notification', array( $this, 'trigger' ), 10, 2 );

		parent::__construct();
	}

	public function get_default_subject() {
		return 'Ta commande Dunkin\' n°{order_number} est prête !';
	}

	public function get_default_heading() {
		return 'Ta commande t\'attend 🍩';
	}

	public function get_default_additional_content() {
		return 'À tout de suite au comptoir !';
	}

	public function trigger( $order_id, $order = false ) {
		$this->setup_locale();

		if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order_id );
		}

		if ( is_a( $order, 'WC_Order' ) ) {
			$this->object                         = $order;
			$this->recipient                      = $order->get_billing_email();
			$this->placeholders['{order_date}']   = wc_format_datetime( $order->get_date_created() );
			$this->placeholders['{order_number}'] = $order->get_order_number();
		}

		if ( $this->is_enabled() && $this->get_recipient() ) {
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		$this->restore_locale();
	}

	public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'order'              => $this->object,
			'email_heading'      => $this->get_heading(),
			'additional_content' => $this->get_additional_content(),
			'sent_to_admin'      => false,
			'plain_text'         => false,
			'email'              => $this,
		) );
	}

	public function get_content_plain() {
		return wc_get_template_html( $this->template_plain, array(
			'order'              => $this->object,
			'email_heading'      => $this->get_heading(),
			'additional_content' => $this->get_additional_content(),
			'sent_to_admin'      => false,
			'plain_text'         => true,
			'email'              => $this,
		) );
	}
}