<?php

namespace JustRelatedPosts\Controller;

/**
 * Responsible for registering and rendering the related posts block.
 * 
 * @package JustRelatedPosts\Controller
 */
class RelatedPostsBlockController {

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

        $this->current_query = $this->get_related_posts_query( $attributes );

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

        $this->current_query = $this->get_related_posts_query( $attributes );

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

    protected function get_related_posts_query( $attributes ) {

        $related_by = $attributes['related_by'] ?? '';

        // Build the query args based on the block attributes.

        $query_args = [
            'post_type'      => 'post',      // TODO based on current post type.
            'posts_per_page' => 3,           // TODO based on something.
            'post_status'    => 'publish',
            'orderby'        => 'post_date',
        ];

        // TODO category, tag, author.

        /**
         * Filter the query arguments for the related posts block.
         * 
         * @param array $query_args The query arguments.
         */
        $query_args = apply_filters( 'just_related_posts_block_query_args', $query_args );

        // TODO fallback query.

        $query = new \WP_Query( $query_args );

        return $query;

    }

}
