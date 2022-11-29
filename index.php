<?php
/*
Plugin Name: Dashboard Widget
Plugin URI:
Description: Dashboard widget code
Version: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * It adds a new widget to the dashboard
 */
add_action( 'wp_dashboard_setup', 'dashboard_add_widgets' );
function dashboard_add_widgets() {
	wp_add_dashboard_widget( 'dashboard_widget_graph', __( 'Graph Widget', 'dw' ), 'dashboard_widget_graph_handler' );
}

function dashboard_widget_graph_handler() {
	echo '<div id="graphwidget">
				<h2>Loading...</h2>
			</div>';
}

/**
 * It enqueues the CSS and JS files that we just built
 */
add_action( 'admin_enqueue_scripts', 'admin_enqueue_scripts' );

/**
 * Enqueue scripts and styles.
 *
 * @return void
 */
function admin_enqueue_scripts() {
    wp_enqueue_style( 'dashboard-style', plugin_dir_url( __FILE__ ) . 'build/index.css' );
    wp_enqueue_script( 'dashboard-script', plugin_dir_url( __FILE__ ) . 'build/index.js', array( 'wp-element' ), '1.0.0', true );
}


global $chartdata_db_version;
$chartdata_db_version = '1.1'; // version changed from 1.0 to 1.1
$table_name = $wpdb->prefix . 'chartdata';

/**
    * register_activation_hook implementation
    *
    * will be called when user activates plugin first time
    * must create needed database tables
    */
function chartdata_install()
{
    global $wpdb;
    global $chartdata_db_version;

    $table_name = $wpdb->prefix . 'chartdata'; // do not forget about tables prefix

    // sql to create your table
    // NOTICE that:
    // 1. each field MUST be in separate line
    // 2. There must be two spaces between PRIMARY KEY and its name
    //    Like this: PRIMARY KEY[space][space](id)
    // otherwise dbDelta will not work
    $sql = "CREATE TABLE " . $table_name . " (
        id int(11) NOT NULL AUTO_INCREMENT,
		    name VARCHAR(255) NOT NULL,
        uv int(11) NOT NULL,
        pv int(11) NOT NULL,
		    amt int(11) NOT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id)
    );";

    // we do not execute sql directly
    // we are calling dbDelta which cant migrate database
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // save current database version for later use (on upgrade)
    add_option('chartdata_db_version', $chartdata_db_version);

    /**
        * [OPTIONAL] Example of updating to 1.1 version
        *
        * If you develop new version of plugin
        * just increment $chartdata_db_version variable
        * and add following block of code
        *
        * must be repeated for each new version
        * in version 1.1 we change isp field
        * to contain 100 chars rather 200 in version 1.0
        * and again we are not executing sql
        * we are using dbDelta to migrate table changes
        */
    $installed_ver = get_option('chartdata_db_version');
    if ($installed_ver != $chartdata_db_version) {
        $sql = "ALTER TABLE" . $table_name . " (
			    ADD name VARCHAR(255) NOT NULL AFTER `id`,
        );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // notice that we are updating option, rather than adding it
        update_option('chartdata_db_version', $chartdata_db_version);
    }
}

register_activation_hook(__FILE__, 'chartdata_install');


/**
 * It registers a new REST API endpoint that accepts GET requests and returns a list of all the posts
 * 
 * @param WP_REST_Request request The WP_REST_Request object.
 * 
 * @return The function handle_get_all() is being returned.
 */

add_action( 'rest_api_init', function () {
	register_rest_route( 'chardata/v1', '/data', array(
	  'methods' => 'GET',
	  'callback' => 'handle_get_all',
	) );
  } );
  
  function handle_get_all( WP_REST_Request $request ) {
	  global $wpdb;
	  $filter = $request->get_param( 'filter' );
	  $date = '';
	  if($filter == 7)
	  {
	  	$date =  date('Y-m-d', strtotime('-7 days'));
	  }
	  if($filter == 15)
	  {
	  	$date =  date('Y-m-d', strtotime('-15 days'));
	  }
	  if($filter == 1)
	  {
	  	$date =  date('Y-m-d', strtotime('-1 month'));
	  }
	  $table_name = $wpdb->prefix . 'chartdata';
	  if($date)
	  {
		$query = "SELECT `name`, uv, pv,amt,created_at FROM ".$table_name." WHERE date_format(created_at,'%Y%m%d')>date_format('".$date."','%Y%m%d')";
	  }
	  else{
		$query = "SELECT `name`, uv, pv,amt,created_at FROM ".$table_name;
	  }
	  $list = $wpdb->get_results($query);
	  return $list;
  }
