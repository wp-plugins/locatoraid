<?php
/**
 * @package Locatoraid
 * @author Locatoraid
 * @version 2.1.3
 */
/*
Plugin Name: Locatoraid
Plugin URI: http://www.locatoraid.com/
Description: Store locator plugin
Author: Locatoraid
Version: 2.1.3
Author URI: http://www.locatoraid.com/
*/
/* 
to create another instance, simply copy this file to locatoraid2.php or locatoraid_another.php 
or anything else starting with locatoraid
*/
if( ! class_exists('Locatoraid') )
{
class Locatoraid
{
	var $load_by_js = FALSE;
	var $app = '';
	var $wpi = 0;

	public function __construct( $wpi = '' )
	{
		$this->wpi = $wpi;
		$this->app = 'locatoraid' . $this->wpi;

		$this->load_by_js = FALSE;
		$this->_init();
		add_action('wp', array($this, 'check_shortcode') );
		add_action('admin_menu', array($this, 'admin_menu') );
		add_shortcode( $this->app, array($this, 'front_view'));
	}

	function _init()
	{
		$GLOBALS['NTS_CONFIG'][$this->app] = array();
		$GLOBALS['NTS_CONFIG'][$this->app]['ASSETS_DIR'] = plugins_url( 'assets', __FILE__ );

	// database
		global $table_prefix;
		$GLOBALS['NTS_CONFIG'][$this->app]['DB_HOST'] = DB_HOST;
		$GLOBALS['NTS_CONFIG'][$this->app]['DB_USER'] = DB_USER;
		$GLOBALS['NTS_CONFIG'][$this->app]['DB_PASS'] = DB_PASSWORD;
		$GLOBALS['NTS_CONFIG'][$this->app]['DB_NAME'] = DB_NAME;
		$mypref = $table_prefix . 'lctr2_';
		if( $this->wpi )
		{
			$mypref .= $this->wpi . '_';
		}
		$GLOBALS['NTS_CONFIG'][$this->app]['DB_TABLES_PREFIX'] = $mypref;
		$GLOBALS['NTS_IS_PLUGIN'] = 'wordpress';
	}

	function admin_menu()
	{
		$title = 'Locatoraid';
		if( $this->wpi )
		{
			if( substr($this->wpi, 0, 1) == '_' )
				$title .= ' ' . substr($this->wpi, 1);
			else
				$title .= ' ' . $this->wpi;
		}
		$page = add_menu_page( $title, $title, 'read', $this->app, array($this, 'admin_view') );
		add_action( 'load-' . $page, array($this, 'admin_action') );
		add_action( 'admin_print_styles-' . $page, array($this, 'print_styles') );
		add_action( 'admin_print_scripts-' . $page, array($this, 'print_scripts') );
	}

	function admin_action()
	{
		global $LANG, $CFG, $UNI;

	// action
		$current_user = wp_get_current_user();
		$GLOBALS['NTS_CONFIG'][$this->app]['PREDEFINED_ADMIN'] = $current_user->user_email;
		$GLOBALS['NTS_CONFIG'][$this->app]['FORCE_LOGIN_ID'] = $current_user->ID;
		$GLOBALS['NTS_CONFIG'][$this->app]['FORCE_LOGIN_NAME'] = $current_user->user_email;

		$GLOBALS['NTS_CONFIG'][$this->app]['INDEX_PAGE'] = 'admin.php?page=' . $this->app . '&';
		$GLOBALS['NTS_CONFIG'][$this->app]['DEFAULT_CONTROLLER'] = 'admin/locations';

		$GLOBALS['NTS_CONFIG']['_app_'] = $this->app;
		$app_title = ucfirst($this->app);
		$app_title = 'Locatoraid';
		if( file_exists(dirname(__FILE__) . '/application/modules/pro') )
		{
			$app_title .= ' Pro';
		}
		else
		{
			$GLOBALS['NTS_CONFIG'][$this->app]['nts_app_promo'] = array(
				'http://www.locatoraid.com/order/',
				$app_title . ' Pro'
				);
		}
		$GLOBALS['NTS_CONFIG']['_app_title_'] = $app_title;

		require( dirname(__FILE__) . '/application/index_action.php' );
	}

	function admin_view()
	{
	// view
		$ci =& ci_get_instance();
		echo $ci->output->get_output();
	}

	function front_view()
	{
		if( $this->load_by_js )
		{
			$target = ci_site_url('load');
			$html  =<<<EOT
$url
<script type="text/javascript" src="$target"></script>
EOT;
			return $html;
		}
		else
		{
			$ci =& ci_get_instance();
			return $ci->output->get_output();
		}
	}

	function check_shortcode()
	{
		if( is_admin() )
			return;

		global $post;

		$is_me = FALSE;

/*
		$pattern = get_shortcode_regex();
		if( 
			preg_match_all('/'. $pattern .'/s', $post->post_content, $matches)
			&& array_key_exists(2, $matches)
			&& in_array($this->app, $matches[2])
			)
		{
			$is_me = TRUE;
		}
*/
		if( ! (isset($post) && $post) )
			return $return;

		$pattern = '\[' . $this->app . '\]';
		if(
			preg_match('/'. $pattern .'/s', $post->post_content, $matches)
			)
		{
			$is_me = TRUE;
		}

		if( $is_me )
		{
			wp_enqueue_script( 'jquery' );

			if( ! $this->load_by_js ){
				$this->print_styles();
				$this->print_scripts();
				wp_enqueue_script( 'lctrScript5', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true' );
				wp_enqueue_script( 'lctrScript6', 'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/src/infobox.js' );
				}

		// action
			$url = parse_url( get_permalink($post) );
			$base_url = $url['path'];
			$index_page = $url['query'] ? '?' . $url['query'] . '&' : '?/';

			$GLOBALS['NTS_CONFIG'][$this->app]['BASE_URL'] = $base_url;
			$GLOBALS['NTS_CONFIG'][$this->app]['INDEX_PAGE'] = $index_page;
//			echo "bu = $base_url<br>";
//			echo "ip = $index_page<br>";

			$GLOBALS['NTS_CONFIG'][$this->app]['DEFAULT_CONTROLLER'] = 'front';

			$GLOBALS['NTS_CONFIG']['_app_'] = $this->app;
			require( dirname(__FILE__) . '/application/index_action.php' );
		}
	}

	function print_styles()
	{
		wp_enqueue_style( 'lctrStylesheet1', plugins_url('assets/bootstrap/css/_bootstrap.css', __FILE__) );
		wp_enqueue_style( 'lctrStylesheet2', plugins_url('assets/css/hitcode.css', __FILE__) );
		wp_enqueue_style( 'lctrStylesheet3', plugins_url('assets/css/lpr.css', __FILE__) );
	}

	function print_scripts()
	{
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'lctrScript2', plugins_url('assets/bootstrap/js/bootstrap.min.js', __FILE__) );
		wp_enqueue_script( 'lctrScript3', plugins_url('assets/js/hc.js', __FILE__) );
		wp_enqueue_script( 'lctrScript4', plugins_url('assets/js/lpr.js', __FILE__) );
	}
}
}

if( preg_match("/locatoraid(.+)\.php/", basename(__FILE__), $ma) )
{
	$lctr = new Locatoraid( $ma[1] );
}
else
{
	$lctr = new Locatoraid();
}
?>