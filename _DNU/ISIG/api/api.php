<?php 

header('Access-Control-Allow-Origin: *');  

//  we need our clean up class
require_once('class.abbs-cleandata.php');

//  clean up data and create params array
$_GET = ABBsCleanData::Output($_GET);
$app = $_GET['a'];
$class = $_GET['c'];
$method = $_GET['m'];
$params = array_key_exists('p',$_GET) ? explode( '/', $_GET['p']) : array();

//  check API class exists - return message if not
$aAPIfilePath = $app . DIRECTORY_SEPARATOR . 'class.'.$class.'.php';
if( !file_exists( $aAPIfilePath ) ) {
    die( '404: Page Not Found <br/>'.$aAPIfilePath );
}

//  include required API classes
require_once('class.mysqli-db.php');
$oDB = new DB();
require_once( 'class.api.php' );
require_once( $aAPIfilePath );

//  create new object
$oAPI = new $class();

//  run function passing any parameters
$oAPI->$method( $params, $_POST );

//stop
die();

?>