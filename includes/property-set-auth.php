<?php

if (!defined('ABSPATH')) exit;

function bpetr_setGA4Auth($username)
{
    global $wpdb;
    $tableName = $wpdb->prefix . 'bpetr_config';

    $wpdb->query(
        $wpdb->prepare(
            "UPDATE %i SET is_auth = 1, username = %s WHERE id = 1",
            array(
                $tableName,
                $username
            )
        )
    );
    update_option( 'bpetr_is_auth', 1 );

    $redirectUrl = sanitize_url(
        sprintf('%s/wp-admin/admin.php?page=blog-essential-traffic-rankings', get_bloginfo('url'))
    );
    
    header('location: ' . esc_url($redirectUrl));
}

function bpetr_unsetGA4Auth()
{
    global $wpdb;
    $tableName = $wpdb->prefix . 'bpetr_config';

    $wpdb->query(
        $wpdb->prepare(
            "UPDATE %i SET is_auth = NULL WHERE id = 1",
            $tableName
        )
    );
    update_option( 'bpetr_is_auth', 0 );

    $redirectUrl = sanitize_url(
        sprintf('%s/wp-admin/admin.php?page=blog-essential-traffic-rankings', get_bloginfo('url'))
    );

    header('location: ' . esc_url($redirectUrl));
}

if (isset($_GET['setAuth']) && $_GET['setAuth'] == '1' && isset($_GET['un']) && $_GET['un'] != "") {
    bpetr_setGA4Auth(sanitize_email($_GET['un']));
} elseif (isset($_GET['unAuth']) && $_GET['unAuth'] == '1') {
    bpetr_unsetGA4Auth();
}
