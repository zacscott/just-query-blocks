<?php
/**
 * Plugin Name: Just Query Blocks
 * Description: Simple yet powerful post querying blocks designed for editorial teams to manage their publication homepage composition..
 * Version:     1.0.0
 * Author:      Zac Scott
 * Author URI:  https://zacscott.net
 * Text Domain: just-query-blocks
 */

require dirname( __FILE__ ) . '/vendor/autoload.php';

define( 'JUST_QUERY_BLOCKS_PLUGIN_ABSPATH', dirname( __FILE__ ) );
define( 'JUST_QUERY_BLOCKS_PLUGIN_ABSURL', plugin_dir_url( __FILE__ )  );

// Boot each of the plugin logic controllers.
new \JustQueryBlocks\Controller\QueryBlocksController();
new \JustQueryBlocks\Controller\PatternController();
