<?php
ini_set( 'memory_limit', '32M' );
$content_type = 'text/css';
header("Content-type: $content_type");

require dirname(__FILE__) . '/lessc.inc.php';
$less = new lessc;

$files = array( 'hc.less' );
reset( $files );
foreach( $files as $f ){
	echo $less->compileFile($f);
	}