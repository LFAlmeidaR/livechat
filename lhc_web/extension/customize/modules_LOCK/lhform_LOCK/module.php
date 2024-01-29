<?php

$Module = array( "name" => "Override Forms module");

$ViewList = array();

/*
 * XLS file with all files
*
* */
$ViewList['downloadcollected'] = array(
    'script' => 'downloadcollected.php',
    'params' => array('form_id'),
    'functions' => array(  'manage_fm' )
);

/*
* zip file with XLS file and documents
*  
* */
$ViewList['downloaditem'] = array(
    'script' => 'downloaditem.php',
    'params' => array('collected_id'),
    'functions' => array('manage_fm')
);

?>