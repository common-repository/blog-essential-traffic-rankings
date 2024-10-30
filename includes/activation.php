<?php

    if (!defined('ABSPATH')) exit;

    function bpetr_activationHook()
    {      
        global $wpdb; 
        $db_table_name = $wpdb->prefix . 'bpetr_config';
        $charset_collate = $wpdb->get_charset_collate();

        $check_table_name = $wpdb->get_var($wpdb->prepare("show tables like %i", $db_table_name));

        if ($check_table_name != $db_table_name ) {
            $sql = "CREATE TABLE $db_table_name (
                id smallint(6) NOT NULL auto_increment,
                property_id varchar(16) NULL,
                username varchar(200) NULL,
                is_auth tinyint NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }
    }
