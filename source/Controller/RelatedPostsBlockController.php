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

        $related_by          = $attributes['relatedBy'] ?? 'category';
        $order_by            = $attributes['orderBy'] ?? 'post_date';
        $order               = $attributes['order'] ?? 'DESC';
        $ignore_sticky_posts = $attributes['ignoreStickyPosts'] ?? false;

        // Build the query args based on the block attributes.

        $query_args = [
            'post_status'         => 'publish',
            'post__not_in'        => [ get_the_ID() ],
            'post_type'           => get_post_type( get_the_ID() ),
            'posts_per_page'      => $this->count_post_templates( $block ),
            'orderby'             => $order_by,
            'order'               => $order,
            'ignore_sticky_posts' => $ignore_sticky_posts,
        ];

        if ( 'category' === $related_by ) {

            $categories = get_the_category( get_the_ID() );
            if ( $categories ) {

                $category_ids = [];
                foreach ( $categories as $category ) {
                    $category_ids[] = $category->term_id;
                }

                $query_args['category__in'] = $category_ids;

            }

        } elseif ( 'tag' === $related_by ) {

            $tags = get_the_tags( get_the_ID() );
            if ( $tags ) {

                $tag_ids = [];
                foreach ( $tags as $tag ) {
                    $tag_ids[] = $tag->term_id;
                }

                $query_args['tag__in'] = $tag_ids;

            }

        } elseif ( 'author' === $related_by ) {

            $author_id = get_the_author_meta( 'ID' );
            if ( $author_id ) {
                $query_args['author'] = $author_id;
            }

        }

        /**
         * Filter the query arguments for the related posts block.
         * 
         * @param array $query_args The query arguments.
         */
        $query_args = apply_filters( 'just_related_posts_block_query_args', $query_args );

        // Run the query and get the post IDs.

        $query_args['fields'] = 'ids';
        $related_post_ids = get_posts( $query_args );

        // If there isnt enough related posts, fallback to a wider query.

        if ( count( $related_post_ids ) < $query_args['posts_per_page'] ) {

            unset( $query_args['category__in'] );
            unset( $query_args['tag__in'] );
            unset( $query_args['author'] );

            $query_args['posts_per_page'] = $query_args['posts_per_page'] - count( $related_post_ids );
            $query_args['post__not_in']   = array_merge( $query_args['post__not_in'], $related_post_ids );

            $extra_post_ids = get_posts( $query_args );

            $related_post_ids = array_merge( $related_post_ids, $extra_post_ids );

        }

        // Build the final query to pull in the full posts.

        $query = new \WP_Query(
            [
                'post_status' => 'publish',
                'post_type'   => get_post_type( get_the_ID() ),
                'post__in'    => $related_post_ids,
                'orderby'     => $order_by,
                'order'       => $order,
            ]
        ); 

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
