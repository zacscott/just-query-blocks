<?php

namespace JustRelatedPosts\Controller;

/**
 * Responsible for registering and rendering the related posts block.
 * 
 * @package JustRelatedPosts\Controller
 */
class RelatedPostsBlockController {

    const RELATED_POSTS_BLOCK_NAME = 'just-related-posts/related-posts';
    const RELATED_POSTS_TEMPLATE_BLOCK_NAME = 'just-related-posts/related-posts-template';

    /**
     * The current query being rendered.
     * 
     * @var WP_Query|null
     */
    protected $current_query = null;

    public function __construct() {

        add_action( 'init', [ $this, 'register_blocks' ] );

    }

    public function register_blocks() {

        register_block_type(
            JUST_RELATED_POSTS_PLUGIN_ABSPATH . '/react/build/related-posts',
            [
                'render_callback' => [ $this, 'render_related_posts_block' ],
            ]
        );
    
        register_block_type(
            JUST_RELATED_POSTS_PLUGIN_ABSPATH . '/react/build/related-posts-template',
            [
                'render_callback' => [ $this, 'render_related_posts_template_block' ],
            ]
        );

    }

    public function render_related_posts_block( $attributes, $content, $block ) {

        $this->current_query = $this->build_related_posts_query( $attributes, $content, $block );

        ob_start();
        
        if ( $this->current_query->have_posts() ) {
    
            $inner_blocks = $block->inner_blocks;
            if ( $inner_blocks ) {
    
                while ( $inner_blocks->valid() ) {
    
                    $current = $inner_blocks->current();
    
                    echo $current->render();
    
                    $inner_blocks->next();
    
                }

                $inner_blocks->rewind();
    
            }
    
        }
    
        wp_reset_postdata();
        $this->current_query = null;
    
        return ob_get_clean();

    }

    public function render_related_posts_template_block( $attributes, $content, $block ) {

        ob_start();
	
        if ( $this->current_query && $this->current_query->have_posts() ) {
            $this->current_query->the_post();

            $inner_blocks = $block->inner_blocks;
            if ( $inner_blocks ) {

                while ( $inner_blocks->valid() ) {

                    $current = $inner_blocks->current();

                    echo $current->render();

                    $inner_blocks->next();

                }

                $inner_blocks->rewind();

            }

        }

        return ob_get_clean();

    }

    protected function build_related_posts_query( $attributes, $content, $block ) {

        $related_by          = $query_args['relatedBy'] ?? 'category';
        $order_by            = $query_args['orderBy'] ?? 'post_date';
        $order               = $query_args['order'] ?? 'DESC';
        $ignore_sticky_posts = $query_args['ignoreStickyPosts'] ?? false;

        // Build the query args based on the block attributes.

        $query_args = [
            'post_status'         => 'publish',
            'exclude'             => get_the_ID(),
            'post_type'           => get_post_type( get_the_ID() ),
            'posts_per_page'      => $this->count_post_templates( $block ),
            'orderby'             => $order_by,
            'order'               => $order,
            'ignore_sticky_posts' => $ignore_sticky_posts,
        ];

        // TODO category, tag, author.

        // TODO fallback query.

        /**
         * Filter the query arguments for the related posts block.
         * 
         * @param array $query_args The query arguments.
         */
        $query_args = apply_filters( 'just_related_posts_block_query_args', $query_args );

        $query = new \WP_Query( $query_args );

        return $query;

    }

    /**
     * Count the number of post templates in the given blocks InnerBlocks.
     * 
     * @param WP_Block $block The block to count the post templates in.
     * @return int The number of post templates in the block.
     */
    protected function count_post_templates( $block ) {

        $count = 0;

        $inner_blocks = $block->inner_blocks;
        if ( $inner_blocks ) {

            while ( $inner_blocks->valid() ) {

                $current = $inner_blocks->current();

                // If the current post is a post template, increment the count.
                if ( $current->name === self::RELATED_POSTS_TEMPLATE_BLOCK_NAME ) {
                    $count++;
                }

                // Recursively count the post templates in the current block.
                $count += $this->count_post_templates( $current );

                $inner_blocks->next();

            }

            $inner_blocks->rewind();

        }

        return $count;

    }

}
