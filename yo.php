<?php
/**
 * @package Yo
 * @version 1.3
 */
/*
Plugin Name: Yo
Plugin URI: 
Description: This is a simple integration of the Yo service for Wordpress. It will track Yo subscribers and allow you to send a Yo when you make a new Post.
Author: Paul Molluzzo
Version: 1.3
Author URI: http://paul.molluzzo.com/
License: MIT
*/

// Create the DB table for the Yo Subscribers
function install_yo () {
    global $wpdb;

    $table_name = $wpdb->prefix . "yoscribers"; 


    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        name tinytext NOT NULL,
        yo_count mediumint(9) NOT NULL,
        UNIQUE KEY id (id)
    );";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    add_option('yo_api_key', '');
}

register_activation_hook( __FILE__, 'install_yo' );

// Add page with Yo Subscribers

function register_yo_setting() {
    register_setting( 'general', 'yo_api_key');
}

add_action( 'admin_init', 'register_yo_setting' );

function yo_page_content() {
    global $wpdb;
    $value = get_option('yo_api_key');
    $table = $wpdb->prefix . "yoscribers";
    $all_yosers = $wpdb->get_results("SELECT * FROM $table");

    echo '<div class="wrap">';
    echo '<h2>Yo</h2>';
    echo '<p>If you want to send a Yo when you make a new post, grab a <a href="http://api.justyo.co">Yo API Key</a> and enter it here:';
    echo '<form method="post" action="options.php">';
    echo settings_fields( 'general' );
    echo '<label style="padding-right:5px">Enter Yo API Key:</label>';
    echo '<input type="text" id="yo_api_key" name="yo_api_key" value="' . $value . '" size="40" style="padding:5px" />';
    echo submit_button('Save Yo API Key');
    echo '</form>';
    echo '<h2>Yo</h2>';
    echo '<p>Below is a list of your Yo Subscribers and how often they\'ve Yo\'d you. These are the people who will receive a Yo when you make a new post.</p>';
    echo '<table class="wp-list-table widefat fixed users" cellspacing="0"><thead><tr><th scope="col" class="manage-column" style="">Yo Subscriber</th><th scope="col" class="manage-column" style="">Yo Count</th><th scope="col" class="manage-column" style="">Last Yo\'d</th></tr></thead><tbody id="the-list" data-wp-lists="list:user">';
    foreach($all_yosers as $yoser){
        echo '<tr>';
        echo '<td class="username column-username">'.$yoser->name.'</td>';
        echo '<td class="email column-email">'.$yoser->yo_count.'</td>';
        echo '<td class="posts">'.$yoser->time.'</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
}

function add_yo_page() {
    add_plugins_page( 'Yo', 'Yo', 'read', 'yo', 'yo_page_content');
}

add_action('admin_menu', 'add_yo_page');

// Add filter for Yo callback
function yo_callback() {
    $yoser = $_GET['username'];

    if (isset($_GET['username'])) {
        global $wpdb;
        $table = $wpdb->prefix . "yoscribers";
        $current_yoser = $wpdb->get_row("SELECT * FROM $table WHERE name = '$yoser'");
        $current_count = $current_yoser->yo_count;

        if ($current_count > 0) {
            $data = array( 
                'time' => date("Y-m-d H:i:s"),
                'yo_count' => ++$current_count
            );
            $current_yoser_id = array( 'ID' => $current_yoser->id);
            $wpdb->update( $table, $data, $current_yoser_id);
        } else {
            $data = array( 
                'time' => date("Y-m-d H:i:s"),
                'name' => $yoser,
                'yo_count' => $current_count+1
            );
            $wpdb->insert( $table, $data);
        }
    }
}

add_action('plugins_loaded', 'yo_callback' );

// Send Yo when creating a new post
function yo_all($link) {
    $api_key = get_option('yo_api_key');
    $url = 'http://api.justyo.co/yoall/';
    $data = array(
        'api_token' => $api_key,
        'link' => $link
        );

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
}

function send_yo_on_publish( $new_status, $old_status, $post ) {
    if ( $new_status == 'publish' && $new_status != $old_status ) {
        $link = get_permalink( $post->ID );
        yo_all($link);
    }
}

add_action( 'transition_post_status', 'send_yo_on_publish', 10, 3 );

?>