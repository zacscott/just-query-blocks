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

$current_query = null;

function just_query_block_render_callback( $attributes, $content, $block ) {

	global $current_query;

	// TODO wp_query stuff here.
	$current_query = new WP_Query(
		[
		'post_type' => 'page',
		'posts_per_page' => 3,
		]
	);

	ob_start();
	
	if ( $current_query->have_posts() ) :

		$inner_blocks = $block->inner_blocks;
		if ( $inner_blocks ) :

			while ( $inner_blocks->valid() ) :

				$current = $inner_blocks->current();

				echo $current->render();

				$inner_blocks->next();

			endwhile;

			$inner_blocks->rewind();

		endif;

	endif;

	wp_reset_postdata();
	$current_query = null;

	return ob_get_clean();

}

function just_query_block_template_render_callback( $attributes, $content, $block ) {

	global $current_query;

	ob_start();
	
	if ( $current_query && $current_query->have_posts() ) :
		$current_query->the_post();

		global $post;
		$the_post = $post;

		$inner_blocks = $block->inner_blocks;
		if ( $inner_blocks ) :

			while ( $inner_blocks->valid() ) :

				$current = $inner_blocks->current();

				echo $current->render();

				$inner_blocks->next();

			endwhile;

			$inner_blocks->rewind();

		endif;

	endif;

	return ob_get_clean();

}

function create_block_just_query_block_block_init() {
	
	register_block_type(
		__DIR__ . '/react/build/related-posts',
		[
			'render_callback' => 'just_query_block_render_callback',
		]
	);

	register_block_type(
		__DIR__ . '/react/build/related-posts-template',
		[
			'render_callback' => 'just_query_block_template_render_callback',
		]
	);

}
add_action( 'init', 'create_block_just_query_block_block_init' );
