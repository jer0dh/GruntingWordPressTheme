<?php
remove_action('genesis_loop','genesis_do_loop');
add_action( 'genesis_loop', 'source__knowledgebase_loop' );


// Add class knowledgebase to body tag
add_filter( 'body_class', 'source__knowledgebase_body_class' );
function source__knowledgebase_body_class( $classes ) {
	$classes[] = 'knowledgebase';
	return $classes;
}


// The loop for this page

function source__knowledgebase_loop() {

	$paged = (get_query_var('paged'))? get_query_var('paged'): 1;
	if ( $paged == 1 ) {
		$catId = get_cat_ID( 'Featured' );
		$args  = array( 'cat' => $catId, 'posts_per_page' => 8, 'post_type' => 'post', 'paged' => 1 );

		global $wp_query;
		$wp_query = new WP_Query( $args );
		if ( $wp_query->have_posts() ):
			echo '<div class="banner banner--featured">Featured Articles</div>';
			while ( $wp_query->have_posts() ): $wp_query->the_post();
				global $post;
				$classes = 'article one-half entry';
				if ( $wp_query->current_post % 2 == 0 ) {
					$classes .= ' first';
				}
				source__the_article($classes);
			endwhile;
			echo '<div style="clear: both;"></div>';
		endif;
		wp_reset_query();
	}  /* If first page then show Features */

	/* now list out recent knowledgebase articles */

	$catId =get_cat_ID( 'Knowledgebase' );
	$args  = array( 'cat' => $catId, 'posts_per_page' => 8, 'post_type' => 'post', 'paged' => $paged );

	global $wp_query;
	$wp_query = new WP_Query( $args );
	if ( $wp_query->have_posts() ):
		echo '<div class="banner banner--knowledgebase">Knowledgebase Articles</div>';
		while ( $wp_query->have_posts() ): $wp_query->the_post();
			global $post;
			$classes = 'one-half entry';
			if ( $wp_query->current_post % 2 == 0 ) {
				$classes .= ' first';
			}

			source__the_article($classes);

		endwhile;
		genesis_posts_nav();
	endif;
	wp_reset_query();


}
/*
genesis_posts_nav();
wp_reset_query();
/*add_action('genesis_entry_content', 'source__knowledgebase_entry');
function source__knowledgebase_entry(){

?>
	<div class="article">
		<?php
		if(has_post_thumbnail()) {
			the_post_thumbnail();
		}
		else {
			echo source__add_default_image("Thumb");
		}
		?>
	</div>
	<div class="articleExcerpt">
		<div class="titleArea"><a href="<?php the_permalink(); ?>"><span class="title"><h1><?php the_title(); ?></h1></span></a></div>
		<div class="date"><time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date();?></time></div>
		<span class="excerpt"><a href="<?php the_permalink(); ?>"><?php the_excerpt(); ?></a></span>
	</div>
	<?php
}
*/

function source__the_article( $classes ) {

	?>
	<article class="<?php echo $classes; ?>">
		<div class="article__imgArea">
			<?php
			if(has_post_thumbnail()) {
				the_post_thumbnail("full");
			}
			else {
				echo source__add_default_image();
			}
			?>
			<div class="titleArea"><span class="title"><h1><a href="<?php the_permalink(); ?>"><?php echo source__limit_length(get_the_title(),79); ?></a> </h1></span></div>
		</div>
		<div class="article__excerpt">
			<span class="article__excerpt__span"><a href="<?php the_permalink(); ?>"><?php the_excerpt(); ?></a></span>
		</div>
	</article>
	<?php
}

function source__limit_length($string, $len) {
	if(strlen($string) > $len) {
		return substr($string, 0, $len-3) . '...';
	} else {
		return $string;
	}
}

genesis();
?>