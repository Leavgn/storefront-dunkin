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