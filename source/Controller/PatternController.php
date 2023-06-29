<?php

namespace JustQueryBlocks\Controller;

/**
 * Responsible for registering and rendering the plugins block patterns.
 * 
 * @package JustQueryBlocks\Controller
 */
class PatternController {

    public function __construct() {
        
        add_action( 'init', [ $this, 'register_patterns' ] );

    }

    public function register_patterns() {

        $pattern_category = 'just-query-blocks';

        register_block_pattern_category(
            $pattern_category,
            [
                'label' => __( 'Just Query Blocks', 'just-query-blocks' ),
            ]
        );

        register_block_pattern(
            'just-query-blocks/query-posts-row',
            [
                'title'       => __( 'Posts Row', 'just-query-blocks' ),
                'description' => __( 'Post query in a single row of post tiles.', 'just-query-blocks' ),
                'categories'  => [ 'featured', $pattern_category ],
                'content'     => $this->render_pattern( 'query-posts-row' ),
            ]
        );

        register_block_pattern(
            'just-query-blocks/query-posts-grid',
            [
                'title'       => __( 'Posts Grid', 'just-query-blocks' ),
                'description' => __( 'Post query in a multiline grid of post tiles.', 'just-query-blocks' ),
                'categories'  => [ 'featured', $pattern_category ],
                'content'     => $this->render_pattern( 'query-posts-grid' ),
            ]
        );

        register_block_pattern(
            'just-query-blocks/query-posts-list',
            [
                'title'       => __( 'Posts List', 'just-query-blocks' ),
                'description' => __( 'Post query in a vertical list.', 'just-query-blocks' ),
                'categories'  => [ 'featured', $pattern_category ],
                'content'     => $this->render_pattern( 'query-posts-list' ),
            ]
        );

        register_block_pattern(
            'just-query-blocks/related-posts-row',
            [
                'title'       => __( 'Related Posts Row', 'just-query-blocks' ),
                'description' => __( 'Related posts with a single row of post tiles.', 'just-query-blocks' ),
                'categories'  => [ 'featured', $pattern_category ],
                'content'     => $this->render_pattern( 'related-posts-row' ),
            ]
        );

        register_block_pattern(
            'just-query-blocks/related-posts-list',
            [
                'title'       => __( 'Related Posts List', 'just-query-blocks' ),
                'description' => __( 'Related posts in a vertical list.', 'just-query-blocks' ),
                'categories'  => [ 'featured', $pattern_category ],
                'content'     => $this->render_pattern( 'related-posts-list' ),
            ]
        );

    }

    protected function render_pattern( string $pattern ) {

        ob_start();

        include JUST_QUERY_BLOCKS_PLUGIN_ABSPATH . '/templates/patterns/' . $pattern . '.html';

        return ob_get_clean();
    }

}
