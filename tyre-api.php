<?php 
/**
 * Plugin Name: Tyre API
 * Plugin URI: http://tyreapi.com
 * Author: Henry Oladeji
 * Description: Plugin That Consumes API
 * Version: 1.0.1
 * License: GPL2
 * License URL: http://www.gnu.org/licenses/gpl
 * text-domain: tyre-api.php
 */

defined('ABSPATH') or die;

$username = 'firecask-02';
$password = 'xe%37VWV@9-7?WAZ5Hzyhs$f';

add_action('admin_menu', 'henry_add_menu_page');

function henry_add_menu_page() {
    add_menu_page(
        'My Tyre',
        'My Tyre',
        'manage_options',
        'tyre-api.php',
        'run_all_the_code_functions',
        'dashicons-marker',
        16,
    );
}

function run_all_the_code_functions() {
    //Get Post Stored in database
    if(false === get_option('tyre_wp_info')) {
        //Get all the API
        $info_tyre = get_tyre_api();
        //Save API
        add_option('tyre_wp_info', $info_tyre);
        return;
    }
    //Custom Tables
    create_database_table();
    //Get info stored in db.
    save_database_table_info();
}

function get_tyre_api(){

    $url = 'https://api.tyrepedia.com/api/v1/brands';

    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjgwN2Y2YWMyYTYxYTdhMzc4YjY2ZGQzMWU3MzEyZWI3M2Q5Y2FiNGI2ZmJkNzEzMDkwZTMwYzFmYjVmYjBhODM0MjM3MWQ1ZTg1NmMxYTMxIn0.eyJhdWQiOiI2IiwianRpIjoiODA3ZjZhYzJhNjFhN2EzNzhiNjZkZDMxZTczMTJlYjczZDljYWI0YjZmYmQ3MTMwOTBlMzBjMWZiNWZiMGE4MzQyMzcxZDVlODU2YzFhMzEiLCJpYXQiOjE2MjY0Mzg1NjAsIm5iZiI6MTYyNjQzODU2MCwiZXhwIjoxNjU3OTc0NTYwLCJzdWIiOiIxNSIsInNjb3BlcyI6W119.UprH9hR_iJpF8LzkhtyeQQWDlPwW2_3-VIgXJkkRxB4wzYBYhZeLexr_GFK7xk9KiAwD7aTIjmyCAUsLN1DSHOvFZC6ye03OL8kAb1ufGQxtZlXOdBO9UJKG4ipmxvgsuMAJ-dzEEZpUGZh-MpK9BdNPd4tjZbRBml7eFJoehXLByfDCBsGTKnbpGb0IBsI9vrjAI7Mwsu2w5M0H3mYsW9Qj99k35XPXHoN8CaprJi6l29Hsrnh-OuuxXDjow4ElD6vMHN5bOSuA765Uc-JGViJMTFEWrOF42HHX7QivIyOHnJTyPShXpPT42TqysSBtsKmLMvr8x0MeBo2Yc_sZ7NN5c91xstgrfGFnm396XnsCiloXWTCCOQbkr5jp8APJIHxLq-x-_wPgFqo_lIwuAxJDSo-PAjE_fTZ7JB39X-xBkKeeGP9NbEMINDL3ZW_1NyFyOCIlhmsasmTSqnCdRpFt-9d7dvByPwrzHJJdldbis8ISbvjHdIOoRxh1igqMpiLyjOBBXvfk03B1hvOoNOvTvzCrxuh-P33Xnzvk3OxkTnITQznnTA03nH6O_qiZBqFnPmf0Qol-4DJEK0cx5igi9omYdrd4-O7gTc4YWYTgOhMFTVpdyKmycIb3qYxXmc1kHek_I84vhS8jUHW8fS0u9dKKfdKGLJVjiL8LbaQ',
            'Content-Type' => 'application/json',
        ),
        'body' => array(
            'username' => 'firecask-02',
            'password' => 'xe%37VWV@9-7?WAZ5Hzyhs$f'
        ),
    
    );
    $response = wp_remote_get($url, $args);

    echo '<pre>';
    var_dump($response);
    echo '</pre>';

    $response_code = wp_remote_retrieve_response_code ($response);

    $body = wp_remote_retrieve_body($response);

    if (401 === $response_code) {
        return "Unauthorized access";
    }

    if (200 !== $response_code){
        return "Error in getting API";
    }

    if (200 === $response_code){
        return $body;
    }
}

function create_database_table(){
    global $tyre;
    global $wpdb;
    $tyre = '1.0';

    $table_name = $wpdb->prefix . 'tyre';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        tyre_id varchar(10),
        brand_id varchar(10) ,
        brand_name varchar (55) ,
        brand_show int (9) ,
        brand_slug varchar (55) ,
        brand_logo varchar (55) ,
        brand_description text(116) ,
        brand_country varchar (55) ,
        PRIMARY KEY (id)
    )$charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    //Save API
    add_option('tyre', $tyre);
}
 
function save_database_table_info() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'tyre';
    // $results = get_option('tyre_wp_info');
    // $results = gettype(get_option('tyre_wp_info'));
    $results = json_decode( get_option('tyre_wp_info'), true );
    //print_r($results["brands"][0]);
    // var_dump($results);

    foreach( $results["brands"] as $result ) {
        // print_r($result['brand_id']);
        // die();
        $wpdb->insert( 
            $table_name, 
            array( 
                'time'              => current_time( 'mysql' ),
                'tyre_id'           => $result['id'],
                'brand_id'          => $result['brand_id'],
                'brand_name'        => $result['brand_name'],
                'brand_show'        => $result['show'],
                'brand_slug'        => $result['slug'],
                'brand_logo'        => $result['brand_logo'],
                'brand_description' => $result['brand_description'],
                'brand_country'     => $result['brand_country'], 
            )
        );
    }
}