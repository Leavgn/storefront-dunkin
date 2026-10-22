<?php
/**
 * Page « Notre équipe » : liste de tous les membres
 * (archive du type de publication « membre »)
 */
get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<header class="dunkin-equipe__entete">
			<h1 class="dunkin-equipe__titre">Notre équipe</h1>
			<p class="dunkin-equipe__intro">Derrière chaque commande click &amp; collect, il y a une équipe passionnée. Fais connaissance !</p>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="dunkin-equipe">
				<?php while ( have_posts() ) : the_post(); ?>
					<a class="dunkin-membre" href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( 'medium_large', array( 'class' => 'dunkin-membre__photo' ) ); ?>
						<h2 class="dunkin-membre__nom"><?php the_title(); ?></h2>
						<p class="dunkin-membre__poste"><?php echo esc_html( get_field( 'poste' ) ); ?></p>
						<span class="dunkin-membre__lien">Voir le profil</span>
					</a>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<p>L'équipe arrive bientôt !</p>
		<?php endif; ?>

		<p class="dunkin-equipe__mention">Membres fictifs · Photos d'illustration : Pexels</p>

	</main>
</div>

<?php
get_footer();