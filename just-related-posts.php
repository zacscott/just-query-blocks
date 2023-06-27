<?php
/**
 * Plugin Name: Just Related Posts
 * Description: Simple related posts block.
 * Version:     1.0.0
 * Author:      Zac Scott
 * Author URI:  https://zacscott.net
 * Text Domain: just-query-block
 */

require dirname( __FILE__ ) . '/vendor/autoload.php';

define( 'JUST_RELATED_POSTS_PLUGIN_ABSPATH', dirname( __FILE__ ) );
define( 'JUST_RELATED_POSTS_PLUGIN_ABSURL', plugin_dir_url( __FILE__ )  );

// Boot each of the plugin logic controllers.
new \JustRelatedPosts\Controller\RelatedPostsBlockController();
