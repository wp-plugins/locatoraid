<?php
/**
 * @package Locatoraid
 * @author HitCode
 */
/*
Plugin Name: Locatoraid
Plugin URI: http://www.locatoraid.com/
Description: Store locator plugin
Author: HitCode
Version: 2.3.0
Author URI: http://www.hitcode.com/
*/
/* 
to create another instance, simply copy this file to locatoraid_2.php or locatoraid_another.php 
or anything else starting with locatoraid_
*/

include_once( dirname(__FILE__) . '/application/libraries/locatoraid_base.php' );

if( ! class_exists('Locatoraid') )
{
class Locatoraid extends Locatoraid_Base
{
	public function __construct( $wpi = '' )
	{
		parent::__construct( $wpi, __FILE__ );
	}
}
}

if( preg_match("/locatoraid_(.+)\.php/", basename(__FILE__), $ma) )
{
	$lctr = new Locatoraid( $ma[1] );
}
else
{
	$lctr = new Locatoraid();
}
?>