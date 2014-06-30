<?php
/**
 * @package Yo_Wordpress
 * @version 1.0
 */
/*
Plugin Name: Yo Wordpress
Plugin URI: 
Description: This is a simple integration of the Yo service for Wordpress. It will track Yo subscribers and allow you to send a Yo when you make a new Post.
Author: Paul Molluzzo
Version: 1.0
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

// Add Yo API key to DB

function print_yo_field()
{
    $value = get_option('yo_api_key');
    echo '<input type="text" id="yo_api_key" name="yo_api_key" value="' . $value . '" />';
}

function register_yo_setting() {
    register_setting( 'general', 'yo_api_key');
    add_settings_field( 'yo_api_key', 'Yo API Key', 'print_yo_field', 'general');
}
add_action( 'admin_init', 'register_yo_setting' );

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

add_filter('query_vars', 'yo_callback' );

// Send Yo when creating a new post

function yo_all() {
    $api_key = get_option('yo_api_key');
    $url = 'http://api.justyo.co/yoall/';
    $data = array('api_token' => $api_key);

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

function send_yo_on_save( $post_id ) {

    if (wp_is_post_revision($post_id)){
        return;
    }
    yo_all();
}

add_action( 'save_post', 'send_yo_on_save' );

?>
