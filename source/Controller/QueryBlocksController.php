<?php

namespace JustQueryBlocks\Controller;

/**
 * Responsible for registering and rendering the post queries block.
 * 
 * @package JustQueryBlocks\Controller
 */
class QueryBlocksController {

    const QUERY_BLOCK_NAME = 'just-query-blocks/query-block';
    const POST_TEMPLATE_BLOCK_NAME = 'just-query-blocks/post-template';
    const RELATED_POSTS_BLOCK_NAME = 'just-query-blocks/related-posts';

    /**
     * The current query being rendered.
     * 
     * @var WP_Query|null
     */
    protected $current_query = null;

    /**
     * The IDs of all posts which have appeared in a query block to this point.
     * 
     * @var array
     */
    protected $all_queried_posts = [];

    public function __construct() {

        add_action( 'init', [ $this, 'register_blocks' ] );

    }

    public function register_blocks() {

        register_block_type(
            JUST_QUERY_BLOCKS_PLUGIN_ABSPATH . '/react/build/query-posts',
            [
                'render_callback' => [ $this, 'render_query_posts_block' ],
            ]
        );

        register_block_type(
            JUST_QUERY_BLOCKS_PLUGIN_ABSPATH . '/react/build/related-posts',
            [
                'render_callback' => [ $this, 'render_related_posts_block' ],
            ]
        );
    
        register_block_type(
            JUST_QUERY_BLOCKS_PLUGIN_ABSPATH . '/react/build/post-template',
            [
                'render_callback' => [ $this, 'render_post_template_block' ],
            ]
        );

    }

    public function render_query_posts_block( $attributes, $content, $block ) {

        $this->current_query = $this->build_query_posts_query( $attributes, $content, $block );

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

    public function build_query_posts_query( $attributes, $content, $block ) {

        $category            = $attributes['category'] ?? 0;
        $tag                 = $attributes['tag'] ?? 0;
        $author              = $attributes['author'] ?? 0;
        $order_by            = $attributes['orderBy'] ?? 'post_date';
        $order               = $attributes['order'] ?? 'DESC';
        $ignore_sticky_posts = $attributes['ignoreStickyPosts'] ?? false;
        $prevent_duplicates  = $attributes['preventDuplicates'] ?? false;

        // Build the query args based on the block attributes.

        $query_args = [
            'post_status'         => 'publish',
            'post__not_in'        => [ get_the_ID() ],
            'post_type'           => 'post',
            'posts_per_page'      => $this->count_post_templates( $block ),
            'orderby'             => $order_by,
            'order'               => $order,
        ];

        if ( $category ) {
            $query_args['cat'] = $category;
        }

        if ( $tag ) {
            $query_args['tag_id'] = $tag;
        }

        if ( $author ) {
            $query_args['author'] = $author;
        }

        if ( $prevent_duplicates && ! empty( $this->all_queried_posts ) ) {
            $query_args['post__not_in'] = array_merge( $query_args['post__not_in'], $this->all_queried_posts );
        }

        /**
         * Filter the query arguments for the related posts block.
         * 
         * @param array $query_args The query arguments.
         */
        $query_args = apply_filters( 'just_query_posts_block_query_args', $query_args );

        // Run the query and get the post IDs.

        $query_args['fields'] = 'ids';
        $queried_post_ids     = get_posts( $query_args );

        // If there isnt enough related posts, fallback to a wider query.

        if ( count( $queried_post_ids ) < $query_args['posts_per_page'] ) {

            $query_args['posts_per_page'] = $query_args['posts_per_page'] - count( $queried_post_ids );
            $query_args['post__not_in']   = array_merge( $query_args['post__not_in'], $queried_post_ids );

            $extra_post_ids = get_posts( $query_args );

            $queried_post_ids = array_merge( $queried_post_ids, $extra_post_ids );

        }

        // Keep track of the posts which have been queried so far.

        $this->all_queried_posts = array_merge( $this->all_queried_posts, $queried_post_ids );

        // Build the final query to pull in the full posts.

        $query = new \WP_Query(
            [
                'post_status'         => 'publish',
                'post__in'            => $queried_post_ids,
                'orderby'             => $order_by,
                'order'               => $order,
                'ignore_sticky_posts' => $ignore_sticky_posts,
            ]
        ); 

        return $query;

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

    protected function build_related_posts_query( $attributes, $content, $block ) {

        $related_by          = $attributes['relatedBy'] ?? 'category';
        $order_by            = $attributes['orderBy'] ?? 'post_date';
        $order               = $attributes['order'] ?? 'DESC';
        $ignore_sticky_posts = $attributes['ignoreStickyPosts'] ?? false;
        $prevent_duplicates  = $attributes['preventDuplicates'] ?? false;

        // Build the query args based on the block attributes.

        $query_args = [
            'post_status'         => 'publish',
            'post__not_in'        => [ get_the_ID() ],
            'post_type'           => get_post_type( get_the_ID() ),
            'posts_per_page'      => $this->count_post_templates( $block ),
            'orderby'             => $order_by,
            'order'               => $order,
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

        if ( $prevent_duplicates && ! empty( $this->all_queried_posts ) ) {
            $query_args['post__not_in'] = array_merge( $query_args['post__not_in'], $this->all_queried_posts );
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

        // Keep track of the posts which have been queried so far.

        $this->all_queried_posts = array_merge( $this->all_queried_posts, $related_post_ids );

        // Build the final query to pull in the full posts.

        $query = new \WP_Query(
            [
                'post_status'         => 'publish',
                'post_type'           => get_post_type( get_the_ID() ),
                'post__in'            => $related_post_ids,
                'orderby'             => $order_by,
                'order'               => $order,
                'ignore_sticky_posts' => $ignore_sticky_posts,
            ]
        ); 

        return $query;

    }

    public function render_post_template_block( $attributes, $content, $block ) {

        $block_content = '';

        if ( $this->current_query && $this->current_query->have_posts() ) {
            $this->current_query->the_post();

            $dynamic_block_parsed = $block->parsed_block;
            $dynamic_block_parsed['blockName'] = 'core/null';

            $dynamic_block = new \WP_Block(
                $dynamic_block_parsed,
                [
                    'postType' => get_post_type(),
                    'postId'   => get_the_ID(),
                ]
            );

            $block_content = $dynamic_block->render( [ 'dynamic' => true ] );

        }

        return $block_content;

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
                if ( $current->name === self::POST_TEMPLATE_BLOCK_NAME ) {
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
