<?php

// create custom function to return nav menu

function custom_wp_menu() {
	$x = wp_get_nav_menu_items('main');
		$menu = array();
		$submenu = array();
		foreach($x as $y){
			$y-> submenu = array();
			if($y->menu_item_parent === '0')
				array_push($menu, $y);
			else
				array_push($submenu, $y);
		}for($i=0; $i < count($submenu); $i++) {
			$index = get_index($menu,$submenu[$i]->menu_item_parent);
			if($index > -1) {array_push($menu[$index]->submenu,$submenu[$i]);
			}
		}
	return $menu;
	}
	function get_index($menu,$parent_id){
		$index = -1;for($i = 0; $i < count($menu); $i++) {
			if((string)$menu[$i]->ID === $parent_id) {
				$index = $i;
				break;
			}
		}
		return $index;
	}
// create new endpoint route
add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', 'menu-items', array(
        'methods' => 'GET',
        'callback' => 'custom_wp_menu',
    ) );
} );


function wp_rest_allow_anonymous_comments_fx() {
    return true;
}
add_filter('rest_allow_anonymous_comments','wp_rest_allow_anonymous_comments_fx');

add_action( 'rest_api_init', function () {
    register_rest_field( 'comment', 'karma', array(
        'get_callback' => function( $comment_arr ) {
            $comment_obj = get_comment( $comment_arr['id'] );
            return (int) $comment_obj->comment_karma;
        },
        'update_callback' => function( $karma, $comment_obj ) {
            $ret = wp_update_comment( array(
                'comment_ID'    => $comment_obj->comment_ID,
                'comment_karma' => $karma
            ) );
            if ( false === $ret ) {
                return new WP_Error(
                  'rest_comment_karma_failed',
                  __( 'Failed to update comment karma.' ),
                  array( 'status' => 500 )
                );
            }
            return true;
        },
        'schema' => array(
            'description' => __( 'Comment karma.' ),
            'type'        => 'integer'
        ),
    ) );
} );

// Register a new rest route
add_action( 'rest_api_init', 'my_rest_routes' );

function my_rest_routes() {

    register_rest_route(
        'alexi/v1',
        '/output_css_uri/',
        [
            'methods'  => 'GET',
            'callback' => 'output_css_callback',
        ]
    );
}


// Callback function to output the css URI
function output_css_callback() {
    return get_stylesheet_uri();
}