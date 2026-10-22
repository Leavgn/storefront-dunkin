<?php
/**
 * Page d'un membre de l'équipe
 */
get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>
			<?php
			$poste      = get_field( 'poste' );
			$annee      = get_field( 'annee_arrivee' );
			$citation   = get_field( 'citation' );
			$produit_id = get_field( 'produit_prefere' );
			$produit    = $produit_id ? wc_get_product( $produit_id ) : false;
			?>

			<article class="dunkin-fiche">
				<?php the_post_thumbnail( 'medium_large', array( 'class' => 'dunkin-fiche__photo' ) ); ?>

				<div class="dunkin-fiche__contenu">
					<?php if ( $poste ) : ?>
						<p class="dunkin-fiche__poste"><?php echo esc_html( $poste ); ?></p>
					<?php endif; ?>

					<h1 class="dunkin-fiche__nom"><?php the_title(); ?></h1>

					<?php if ( $annee ) : ?>
						<p class="dunkin-fiche__depuis">Chez Dunkin' depuis <?php echo esc_html( $annee ); ?></p>
					<?php endif; ?>

					<?php if ( $citation ) : ?>
						<blockquote class="dunkin-fiche__citation"><?php echo esc_html( $citation ); ?></blockquote>
					<?php endif; ?>

					<div class="dunkin-fiche__texte"><?php the_content(); ?></div>

					<?php if ( $produit ) : ?>
						<a class="dunkin-prefere" href="<?php echo esc_url( $produit->get_permalink() ); ?>">
							<?php echo wp_kses_post( $produit->get_image( 'woocommerce_thumbnail' ) ); ?>
							<span>
								<span class="dunkin-prefere__etiquette">Son produit préféré</span>
								<span class="dunkin-prefere__nom"><?php echo esc_html( $produit->get_name() ); ?></span>
								<span class="dunkin-prefere__lien">Voir le produit</span>
							</span>
						</a>
					<?php endif; ?>

					<a class="button dunkin-button--contour" href="<?php echo esc_url( get_post_type_archive_link( 'membre' ) ); ?>">← Toute l'équipe</a>
				</div>
			</article>

		<?php endwhile; ?>

	</main>
</div>

<?php
get_footer();