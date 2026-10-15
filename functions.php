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