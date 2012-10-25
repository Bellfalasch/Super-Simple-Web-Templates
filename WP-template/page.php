<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<header>
			<h1><?php the_title(); ?></h1>
		</header>

		<section>

			<?php the_content(); ?>

		</section>

	<?php endwhile; ?>

	<?php else : ?>

		<h2>Not Found</h2>

	<?php endif; ?>

<?php get_footer(); ?>