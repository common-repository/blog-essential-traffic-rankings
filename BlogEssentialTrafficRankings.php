<?php 
/*
    Plugin Name: Blog Essential Traffic and Rankings from Google
    Plugin URI: https://www.bloggerplot.com/wp-plugin/
    Description: This plugin gives you instant access to your blog’s essential traffic and rankings data from Google Analytics 4 and Search Console, inside WordPress.
    Tags: google analytics 4, google search console, ga, ga4, gsc, blogging, analytics, website traffic, website rankings, performance tracking, content marketing
    Author: Bloggerplot
    Author URI: https://www.bloggerplot.com/
    Version: 1.0.0
    Requires at least: 6.2
    Stable tag: 1.0.0
    Requires PHP: 7.3
    License: GPLv2 or later
    License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if (!session_id()) {
    session_start();
}

require_once plugin_dir_path(__FILE__) .'includes/timeframe_update.php';
require_once plugin_dir_path(__FILE__) .'includes/setting_check.php';

if (!class_exists('BPETR_Admin')) {
    class BPETR_Admin {
        static $bpetrApiUrl = 'https://wp.bloggerplot.com';
        static $bpetrServerState = 1;

        const BPETR_DEFAULT_TIMEFRAME = '30days';
        const BPETR_DEFAULT_GRAPH_METRIC = 'screenPageViews';

        function __construct() {
            $this->bpetr_activation();

            require_once plugin_dir_path(__FILE__) . 'includes/csv_export.php';
            require_once plugin_dir_path(__FILE__) . 'includes/website_init.php';
            require_once plugin_dir_path(__FILE__) . 'includes/property-set-auth.php';
            require_once plugin_dir_path(__FILE__) . 'includes/api_token.php';

            if (BPETR_Admin::$bpetrServerState == 0) {
                return;
            }

            require_once plugin_dir_path(__FILE__) . 'includes/website_pack.php';
            require_once plugin_dir_path(__FILE__) . 'includes/paypal-payment.php';
            require_once plugin_dir_path(__FILE__) . 'includes/paypal_plan_id.php';

            add_action('admin_menu', array($this, 'bpetr_add_menu'));
            add_action('admin_init', [$this, 'bpetr_ga4_settings_init']);
            add_action( 'admin_enqueue_scripts', [$this, 'bpetr_loadJsScripts'] );
        }

        function bpetr_activation()
        {
            require_once plugin_dir_path(__FILE__) .'includes/activation.php';
            register_activation_hook( __FILE__, 'bpetr_activationHook');
        }

        function bpetr_add_menu()
        {
            $pageTitle = esc_html__('Blog Performance');
            $menuTitle = esc_html__('Blog Performance');

            $hookName = add_menu_page(
                $pageTitle,
                $menuTitle,
                'manage_options',
                'blog-essential-traffic-rankings',
                [$this, 'bpetr_display_settings'],
                plugin_dir_url(__FILE__) . 'images/bp_wp_logo.png',
                20
            );

            $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

            $trafficRankTitle = 'Traffic & Rankings';
            $trafficChangeTitle = 'Traffic Change';
            $trafficSource = 'Traffic sources';
            $geoBreakdown = 'Geo Breakdown';
            $lockIcon = ' <i class="fa fa-lock" aria-hidden="true"></i>';
            if ($websiteIsPremium != 1) {
                $trafficRankTitle .= $lockIcon;
                $trafficChangeTitle .= $lockIcon;
                $trafficSource .= $lockIcon;
                $geoBreakdown .= $lockIcon;
            }

            add_submenu_page(
                'blog-essential-traffic-rankings',
                'Traffic & Rankings',
                $trafficRankTitle,
                'manage_options',
                'traffic-and-rankings',
                [$this, 'bpetr_trafficAndRankings'],
            );

            add_submenu_page(
                'blog-essential-traffic-rankings',
                'Traffic Change',
                $trafficChangeTitle,
                'manage_options',
                'traffic-change-analysis',
                [$this, 'bpetr_trafficChangeAnalysis'],
            );

            add_submenu_page(
                null,
                'Post Traffic Analysis',
                'Post Traffic Analysis',
                'manage_options',
                'post-traffic-analysis',
                [$this, 'bpetr_postTrafficAnalysis'],
            );

            add_submenu_page(
                'blog-essential-traffic-rankings',
                'Traffic sources',
                $trafficSource,
                'manage_options',
                'traffic-sources',
                [$this, 'bpetr_trafficSources'],
            );

            add_submenu_page(
                'blog-essential-traffic-rankings',
                'Top Search Queries',
                'Top Search Queries',
                'manage_options',
                'search-queries',
                [$this, 'bpetr_searchQueries'],
            );

            add_submenu_page(
                'blog-essential-traffic-rankings',
                'Top Queries by Post',
                'Top Queries by Post',
                'manage_options',
                'top-queries-by-post',
                [$this, 'bpetr_topQueriesByPost'],
            );

            add_submenu_page(
                'blog-essential-traffic-rankings',
                'Geo Breakdown',
                $geoBreakdown,
                'manage_options',
                'traffic-by-country',
                [$this, 'bpetr_trafficByCountry'],
            );

            add_submenu_page(
                'blog-essential-traffic-rankings',
                'My Subscription',
                'My Subscription',
                'manage_options',
                'my-subscription',
                [$this, 'bpetr_mySubscription'],
            );

            add_submenu_page(
                'blog-essential-traffic-rankings',
                'Settings',
                'Settings',
                'manage_options',
                'setting',
                [$this, 'bpetr_setting'],
            );

            add_action( 'load-' . $hookName, [$this, 'bpetr_saveSettting'] );
        }

        function bpetr_display_settings()
        {
            require_once plugin_dir_path(__FILE__) .'includes/main.php';
        }

        function bpetr_trafficAndRankings()
        {
            require_once plugin_dir_path(__FILE__) .'includes/pages/traffic_and_rankings.php';
        }

        function bpetr_trafficChangeAnalysis()
        {
            require_once plugin_dir_path(__FILE__) .'includes/pages/traffic_change_analysis.php';
        }

        function bpetr_postTrafficAnalysis()
        {
            require_once plugin_dir_path(__FILE__) .'includes/pages/post_traffic_analysis.php';
        }

        function bpetr_trafficSources()
        {
            require_once plugin_dir_path(__FILE__) .'includes/pages/traffic_sources.php';
        }

        function bpetr_searchQueries()
        {
            require_once plugin_dir_path(__FILE__) .'includes/pages/search_queries.php';
        }

        function bpetr_topQueriesByPost()
        {
            require_once plugin_dir_path(__FILE__) .'includes/pages/top_queries_by_post.php';
        }

        function bpetr_trafficByCountry()
        {
            require_once plugin_dir_path(__FILE__) .'includes/pages/traffic_by_country.php';
        }

        function bpetr_mySubscription()
        {
            require_once plugin_dir_path(__FILE__) .'includes/pages/my_subscription.php';
        }

        function bpetr_setting()
        {
            require_once plugin_dir_path(__FILE__) .'includes/pages/setting.php';
        }

        function bpetr_ga4_settings_init() {
            register_setting('bpetr-config', 'blog traffic and rankings config settings');

            add_settings_section(
                'bpetr_settings_section',
                '', 'bpetr_settings_section_callback',
                'bpetr-config'
            );

            add_settings_field(
                'bpetr_settings_field',
                'GA4 Property Id', 'bpetr_ga4_property_id',
                'bpetr-config',
                'bpetr_settings_section'
            );
        }

        function bpetr_saveSettting()
        {
            if ('POST' == $_SERVER['REQUEST_METHOD']) {
                $serverName = sanitize_text_field($_SERVER['SERVER_NAME']);

                check_admin_referer(
                    sprintf('bpetr-property-id_%s_%s', $serverName, date('YmdHi')),
                    'bpetr_check'
                );

                global $wpdb;
                $tableName = $wpdb->prefix . 'bpetr_config';

                $propertyId = sanitize_text_field($_POST['ga4_property_id_fld']);

                $checkBpConfig = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT * FROM %i WHERE id = 1", $tableName
                    )
                );
                $sql = isset($checkBpConfig->id)
                    ? $wpdb->prepare("UPDATE %i SET property_id = %s WHERE id = 1", array($tableName, $propertyId))
                    : $wpdb->prepare("INSERT INTO %i (id, property_id) VALUES (1, %s)", array($tableName, $propertyId))
                ;

                $wpdb->query($sql);

                update_option( 'bpetr_property_id', $propertyId );

                add_option( 'bpetr_setting_saved_flash', true );
            }
        }

        function bpetr_loadJsScripts()
        {
            wp_enqueue_script(
                'common-script',
                plugins_url( '/js/common.js', __FILE__ )
            );

            wp_enqueue_style(
                'bootstrap-css',
                plugins_url( '/css/bootstrap-5.3.2.min.css', __FILE__ )
            );

            wp_enqueue_script(
                'bootstrap-js',
                plugins_url( '/js/bootstrap-5.3.2.min.js', __FILE__ )
            );

            wp_enqueue_style(
                'font-awesome-css',
                plugins_url( '/css/font-awesome.min.css', __FILE__ )
            );

            wp_enqueue_style(
                'blog-traffic-and-rankings-css',
                plugins_url( '/css/blog-traffic-and-rankings.css', __FILE__ )
            );

            wp_enqueue_style(
                'datatables-css',
                plugins_url( '/css/jquery.dataTables.min.css', __FILE__ )
            );

            wp_enqueue_script(
                'datatables-js',
                plugins_url( '/js/jquery.dataTables.min.js', __FILE__ )
            );

            if (in_array($_GET['page'], ['blog-essential-traffic-rankings', 'post-traffic-analysis'])) {
                wp_enqueue_script(
                    'chartjs',
                    plugins_url( '/js/chart.js', __FILE__ )
                );
            }

            $paypalPages = ['blog-essential-traffic-rankings', 'my-subscription', 'setting'];
            if (in_array($_GET['page'], $paypalPages)) {
                require_once plugin_dir_path(__FILE__) .'includes/paypal_script.php';
            }

            switch ($_GET['page']) {
                case 'traffic-and-rankings':
                    $pageScript = '../../js/pages/traffic_and_rankings.js';
                    break;

                case 'traffic-change-analysis':
                    $pageScript = '../../js/pages/traffic_change_analysis.js';
                    break;

                case 'traffic-sources':
                    $pageScript = '../../js/pages/traffic_sources.js';
                    break;

                case 'search-queries':
                    $pageScript = '../../js/pages/search_queries.js';
                    break;

                case 'top-queries-by-post':
                    $pageScript = '../../js/pages/top_queries_by_post.js';
                    break;

                case 'traffic-by-country':
                    $pageScript = '../../js/pages/traffic_by_country.js';
                    break;

                case 'my-subscription':
                    $pageScript = '../../js/pages/my_subscription.js';
                    break;

                case 'post-traffic-analysis':
                    $pageScript = '../../js/pages/post_traffic_analysis.js';
                    break;

                default:
                    $pageScript = null;
                    break;
            }

            if ($_GET['page'] == 'blog-essential-traffic-rankings') {
                require_once plugin_dir_path(__FILE__) .'includes/snapshot_scripts.php';
            } elseif ($pageScript != null) {
                require_once plugin_dir_path(__FILE__) .'includes/pages/pages_scripts.php';
            }
        }
    }

    $GLOBALS['BPETR_Admin'] = new BPETR_Admin();
}
