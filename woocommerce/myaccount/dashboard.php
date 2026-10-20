<?php
/**
 * Tableau de bord « Mon compte »
 * Surcharge du gabarit WooCommerce myaccount/dashboard.php
 */

defined( 'ABSPATH' ) || exit;

$utilisateur = wp_get_current_user();
$prenom      = $utilisateur->first_name ? $utilisateur->first_name : $utilisateur->display_name;
$commandes   = wc_get_orders( array( 'customer_id' => get_current_user_id(), 'limit' => 1 ) );
?>

<h2 class="dunkin-compte__titre">Bonjour <?php echo esc_html( $prenom ); ?> !</h2>
<p class="dunkin-compte__intro">Depuis ton compte, tu peux suivre tes commandes, gérer tes adresses et modifier tes informations.</p>

<div class="dunkin-raccourcis">
	<a class="dunkin-raccourci" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
		<h3>Mes commandes</h3>
		<p>Suis tes commandes en cours et retrouve tes factures.</p>
		<span>Voir →</span>
	</a>
	<a class="dunkin-raccourci" href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>">
		<h3>Mes adresses</h3>
		<p>Pour la facturation et la livraison.</p>
		<span>Gérer →</span>
	</a>
	<a class="dunkin-raccourci" href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>">
		<h3>Mes infos</h3>
		<p>Nom, e-mail et mot de passe.</p>
		<span>Modifier →</span>
	</a>
</div>

<?php if ( $commandes ) : ?>
	<?php $commande = $commandes[0]; ?>
	<div class="dunkin-derniere">
		<div>
			<h3>Dernière commande · n°<?php echo esc_html( $commande->get_order_number() ); ?></h3>
			<p>
				<?php echo esc_html( wc_format_datetime( $commande->get_date_created() ) ); ?>
				· <?php echo wp_kses_post( $commande->get_formatted_order_total() ); ?>
				· <?php echo esc_html( $commande->get_shipping_method() ); ?>
			</p>
		</div>
		<a class="button dunkin-button--contour" href="<?php echo esc_url( $commande->get_view_order_url() ); ?>">Voir la commande</a>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_account_dashboard' ); ?>