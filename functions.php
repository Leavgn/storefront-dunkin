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

/**
 * PAGE D'ACCUEIL
 * Storefront affiche plusieurs sections sur la page d'accueil.
 * On retire celles qui ne sont pas dans ma maquette.
 */
add_action( 'init', function () {
	remove_action( 'homepage', 'storefront_homepage_content', 10 );                    // titre « Accueil »
	remove_action( 'homepage', 'storefront_recent_products', 30 );                     // Nouveautés
	remove_action( 'homepage', 'storefront_featured_products', 40 );                   // Produits mis en avant
	remove_action( 'homepage', 'storefront_popular_products', 50 );                    // Produits populaires
	remove_action( 'homepage', 'storefront_on_sale_products', 60 );                    // Promotions
	remove_action( 'homepage', 'storefront_best_selling_products', 70 );               // Meilleures ventes
	remove_action( 'homepage', 'storefront_woocommerce_brands_homepage_section', 80 ); // Marques
} );

// Section catégories : titre de la maquette, 3 catégories sur 3 colonnes
add_filter( 'storefront_product_categories_args', function ( $args ) {
	$args['title']   = 'Nos catégories';
	$args['limit']   = 3;
	$args['columns'] = 3;
	return $args;
} );

// Hero : affiché en premier (priorité 5)
add_action( 'homepage', function () {
	$boutique = wc_get_page_permalink( 'shop' );
	?>
	<section class="dunkin-hero">
		<div class="dunkin-hero__texte">
			<h1>Commande en ligne, récupère en boutique sans attendre</h1>
			<p>Tes donuts, cafés et sandwichs préférés, prêts à l'heure que tu choisis.</p>
			<div class="dunkin-hero__boutons">
				<a class="button" href="<?php echo esc_url( $boutique ); ?>">Commander</a>
				<a class="button dunkin-button--secondaire" href="<?php echo esc_url( $boutique ); ?>">Voir la carte</a>
			</div>
		</div>
		<div class="dunkin-hero__image">
			<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/images/hero.png' ); ?>" alt="Boissons glacées Dunkin'">
		</div>
	</section>
	<?php
}, 5 );

// « Comment ça marche ? » : après le hero, avant les catégories (priorité 15)
add_action( 'homepage', function () {
	?>
	<section class="dunkin-etapes">
		<h2>Comment ça marche ?</h2>
		<ol class="dunkin-etapes__liste">
			<li>
				<span class="dunkin-etapes__num">1</span>
				<h3>Je choisis</h3>
				<p>Sélectionne tes donuts et boissons préférés.</p>
			</li>
			<li>
				<span class="dunkin-etapes__num">2</span>
				<h3>Je commande</h3>
				<p>Valide ton panier et ton heure de retrait.</p>
			</li>
			<li>
				<span class="dunkin-etapes__num">3</span>
				<h3>Je récupère</h3>
				<p>Passe en boutique, ta commande t'attend.</p>
			</li>
		</ol>
	</section>
	<?php
}, 15 );

add_action( 'init', function () {
	remove_action( 'storefront_header', 'storefront_product_search', 40 );
	remove_action( 'storefront_header', 'storefront_header_container_close', 41 );
	remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper', 42 );
	remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper_close', 68 );
	add_action( 'storefront_header', function () {
		echo '</div>'; // ferme le bloc unique
	}, 68 );
} );

/**
 * BOUTIQUE : pastilles de filtre par catégorie, au-dessus de la grille.
 * La pastille de la catégorie affichée est « active ».
 */
add_action( 'woocommerce_before_shop_loop', function () {
	$categories = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'exclude'    => get_option( 'default_product_cat' ), // sans « Non classé »
	) );
	$actuelle = is_product_category() ? get_queried_object_id() : 0;

	echo '<nav class="dunkin-filtres" aria-label="Catégories de produits">';
	printf(
		'<a class="dunkin-pastille%s" href="%s">Tout</a>',
		$actuelle ? '' : ' is-active',
		esc_url( wc_get_page_permalink( 'shop' ) )
	);
	foreach ( $categories as $categorie ) {
		printf(
			'<a class="dunkin-pastille%s" href="%s">%s</a>',
			$actuelle === $categorie->term_id ? ' is-active' : '',
			esc_url( get_term_link( $categorie ) ),
			esc_html( $categorie->name )
		);
	}
	echo '</nav>';
}, 5 );



// BOUTIQUE : on retire le tri et le nombre de résultats en bas (déjà présents en haut)
add_action( 'init', function () {
	remove_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10 );
	remove_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20 );
} );

/**
 * « En stock » sous la description courte, comme sur la maquette.
 * WooCommerce ne l'affiche que si le stock est géré en quantité :
 * on l'ajoute pour les autres produits disponibles.
 */
add_filter( 'woocommerce_get_availability_text', function ( $text, $product ) {
	if ( '' === $text && $product->is_in_stock() ) {
		$text = 'En stock';
	}
	return $text;
}, 10, 2 );

// Itération 4 : allergènes et calories (champs SCF), affichés avant le prix
add_action( 'woocommerce_single_product_summary', function () {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}
	$allergenes = get_field( 'allergenes' );
	$calories   = get_field( 'calories' );
	if ( ! $allergenes && ! $calories ) {
		return;
	}
	echo '<div class="dunkin-infos">';
	if ( $allergenes ) {
		echo '<p class="dunkin-infos__titre">Allergènes</p>';
		echo '<ul class="dunkin-infos__allergenes">';
		foreach ( $allergenes as $allergene ) {
			echo '<li>' . esc_html( $allergene ) . '</li>';
		}
		echo '</ul>';
	}
	if ( $calories ) {
		echo '<p class="dunkin-infos__calories">' . esc_html( $calories ) . ' kcal</p>';
	}
	echo '</div>';
}, 7 );

// Itération 5 : panier
// « Expédition » devient « Réception » (vocabulaire du click & collect)
add_filter( 'woocommerce_shipping_package_name', function () {
	return 'Réception';
} );

// Nom du produit seul, les options s'affichent en dessous
add_filter( 'woocommerce_product_variation_get_name', function ( $name, $variation ) {
	$parent = wc_get_product( $variation->get_parent_id() );
	return $parent ? $parent->get_name() : $name;
}, 10, 2 );

// Itération 6 : instructions pour le retrait sur la page Commande
// (seulement si le client a choisi « Retrait en boutique »)
add_action( 'woocommerce_review_order_after_shipping', function () {
	$modes = WC()->session->get( 'chosen_shipping_methods' );
	if ( empty( $modes ) || strpos( $modes[0], 'local_pickup' ) !== 0 ) {
		return;
	}
		$instructions = function_exists( 'get_field' ) ? get_field( 'instructions_pour_le_retrait', 'option' ) : '';
	echo '<tr class="dunkin-retrait"><td colspan="2">';
	echo '<strong>Instructions pour le retrait</strong>';
	echo wp_kses_post( wpautop( $instructions ) );
	echo '</td></tr>';
} );

// Pied de page : ligne de copyright, sans le lien « Built with WooCommerce »
add_filter( 'storefront_credit_link', '__return_false' );

add_filter( 'storefront_copyright_text', function () {
	return '© ' . date( 'Y' ) . " Dunkin' Click & Collect · Projet étudiant, site non officiel";
} );

// Itération 7 : phrase d'accroche dans le formulaire d'inscription
add_action( 'woocommerce_register_form', function () {
	echo '<p class="dunkin-inscription__texte">Avec un compte, tu retrouves tes commandes et tes factures, et tu recommandes en deux clics.</p>';
}, 15 );

// IT-8 : ajoute emails.css aux styles des e-mails WooCommerce
add_filter( 'woocommerce_email_styles', function ( $css ) {
	return $css . file_get_contents( get_stylesheet_directory() . '/emails.css' );
} );

// IT-8 : instructions de retrait dans les e-mails client (retrait en boutique uniquement)
add_action( 'woocommerce_email_after_order_table', function ( $order, $sent_to_admin, $plain_text ) {
	if ( $sent_to_admin || ! $order->has_shipping_method( 'local_pickup' ) ) {
		return;
	}
	$instructions = function_exists( 'get_field' ) ? get_field( 'instructions_pour_le_retrait', 'option' ) : '';
	if ( ! $instructions ) {
		return;
	}
	if ( $plain_text ) {
		echo "\nINSTRUCTIONS POUR LE RETRAIT\n" . wp_strip_all_tags( $instructions ) . "\n\n";
		return;
	}
	echo '<div class="dunkin-retrait-email"><h2>Instructions pour le retrait</h2>';
	echo wp_kses_post( wpautop( $instructions ) );
	echo '</div>';
}, 10, 3 );

// IT-9 : nouvel état de commande « Prête à retirer »
add_action( 'init', function () {
	register_post_status( 'wc-prete', array(
		'label'                     => 'Prête à retirer',
		'public'                    => true,
		'show_in_admin_status_list' => true,
		'show_in_admin_all_list'    => true,
		'exclude_from_search'       => false,
		'label_count'               => _n_noop( 'Prête à retirer <span class="count">(%s)</span>', 'Prêtes à retirer <span class="count">(%s)</span>' ),
	) );
} );

// IT-9 : ajoute « Prête à retirer » juste après « En cours » dans la liste des états
add_filter( 'wc_order_statuses', function ( $etats ) {
	$nouveaux = array();
	foreach ( $etats as $cle => $nom ) {
		if ( 'wc-processing' === $cle ) {   // ← nouvelle ligne
			$nom = 'En préparation';          // ← nouvelle ligne
		}                                   // ← nouvelle ligne
		$nouveaux[ $cle ] = $nom;
		if ( 'wc-processing' === $cle ) {
			$nouveaux['wc-prete'] = 'Prête à retirer';
		}
	}
	return $nouveaux;
} );

// IT-9 : couleur de l'état « Prête à retirer » dans la liste des commandes (admin)
add_action( 'admin_head', function () {
	echo '<style>.order-status.status-prete { background: #E8F4EB; color: #1B7A36; }</style>';
} );

// IT-9 : enregistre l'e-mail « Ta commande est prête ! » auprès de WooCommerce
add_filter( 'woocommerce_email_classes', function ( $emails ) {
	require_once get_stylesheet_directory() . '/class-dunkin-email-prete.php';
	$emails['Dunkin_Email_Prete'] = new Dunkin_Email_Prete();
	return $emails;
} );

// IT-9 : déclenche les e-mails quand une commande passe à « Prête à retirer »
add_filter( 'woocommerce_email_actions', function ( $actions ) {
	$actions[] = 'woocommerce_order_status_prete';
	return $actions;
} );

// IT-9 : colonne « Réception » dans « Mes commandes », placée après la date
add_filter( 'woocommerce_account_orders_columns', function ( $colonnes )  {
	$nouvelles = array();
	foreach ( $colonnes as $cle => $nom ) {
		$nouvelles[ $cle ] = $nom;
		if ( 'order-date' === $cle ) {
			$nouvelles['dunkin-reception'] = 'Réception';
		}
	}
	return $nouvelles;
} );

add_action( 'woocommerce_my_account_my_orders_column_dunkin-reception', function ( $order ) {
	echo esc_html( $order->get_shipping_method() );
} );

// IT-9 : l'état de la commande affiché sous forme de pastille
add_action( 'woocommerce_my_account_my_orders_column_order-status', function ( $order ) {
	$etat = $order->get_status();
	echo '<span class="dunkin-etat dunkin-etat--' . esc_attr( $etat ) . '">' . esc_html( wc_get_order_status_name( $etat ) ) . '</span>';
} );

// IT-9 : suivi de la commande en étapes sur la page « Voir la commande » (retrait uniquement)
add_action( 'woocommerce_view_order', function ( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order || ! $order->has_shipping_method( 'local_pickup' ) ) {
		return;
	}

	// Position de chaque état dans le suivi : 0 = Reçue … 3 = Retirée
	$positions = array(
		'pending'    => 0,
		'on-hold'    => 0,
		'processing' => 1,
		'prete'      => 2,
		'completed'  => 3,
	);
	$etat = $order->get_status();
	if ( ! isset( $positions[ $etat ] ) ) {
		return; // commande annulée ou remboursée : pas de suivi
	}
	$actuelle = $positions[ $etat ];
	$etapes   = array( 'Reçue', 'En préparation', 'Prête à retirer', 'Retirée' );

	echo '<div class="dunkin-suivi">';
	echo '<h2 class="dunkin-suivi__titre">Suivi de ta commande</h2>';
	echo '<ol class="dunkin-suivi__etapes">';
	foreach ( $etapes as $numero => $nom ) {
		$attribut = '';
		if ( $numero < $actuelle || 'completed' === $etat ) {
			$classe   = 'faite';
			$pastille = '✓';
		} elseif ( $numero === $actuelle ) {
			$classe   = 'en-cours';
			$pastille = $numero + 1;
			$attribut = ' aria-current="step"';
		} else {
			$classe   = 'a-venir';
			$pastille = $numero + 1;
		}
		echo '<li class="dunkin-suivi__etape dunkin-suivi__etape--' . $classe . '"' . $attribut . '>';
		echo '<span class="dunkin-suivi__pastille">' . $pastille . '</span>';
		echo '<span class="dunkin-suivi__nom">' . esc_html( $nom ) . '</span>';
		echo '</li>';
	}
	echo '</ol>';
	echo '</div>';

	// Quand la commande est prête : rappel des instructions de retrait
	if ( 'prete' === $etat && function_exists( 'get_field' ) ) {
		$instructions = get_field( 'instructions_pour_le_retrait', 'option' );
		if ( $instructions ) {
			echo '<div class="dunkin-retrait-compte">';
			echo '<h3>Ta commande t\'attend !</h3>';
			echo wp_kses_post( wpautop( $instructions ) );
			echo '</div>';
		}
	}
}, 5 );

// IT-9 : le client peut annuler sa commande tant qu'elle n'est pas prête
add_filter( 'woocommerce_valid_order_statuses_for_cancel', function ( $etats ) {
	$etats[] = 'on-hold';
	$etats[] = 'processing';
	return $etats;
} );