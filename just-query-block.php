<?php
/**
 * Plugin Name:       Just Query Block
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       just-query-block
 *
 * @package           create-block
 */

function just_query_block_render_callback( $attributes, $content, $block ) {

	// global $wp_query;

	// TODO wp_query stuff here.
	$query = new WP_Query( [
		'post_type' => 'page',
		'posts_per_page' => 3,
	] );

	ob_start();
	
	while ( $query->have_posts() ) :
		$query->the_post();

		$inner_blocks = $block->inner_blocks;
		if ( $inner_blocks ) :

			while ( $inner_blocks->valid() ) :

				$current = $inner_blocks->current();

				?>
				<div class="just-query-block">
					<p><?php echo $current->render(); ?></p>
				</div>
				<?php

				$inner_blocks->next();

			endwhile;

			$inner_blocks->rewind();

		endif;

	endwhile;

	wp_reset_postdata();

	return ob_get_clean();

}

function create_block_just_query_block_block_init() {
	register_block_type(
		__DIR__ . '/build',
		[
			'render_callback' => 'just_query_block_render_callback',
		]
	);
}
add_action( 'init', 'create_block_just_query_block_block_init' );
