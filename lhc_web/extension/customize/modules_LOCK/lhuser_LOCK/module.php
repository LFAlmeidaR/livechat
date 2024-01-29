<?php
$Module = array( "name" => "lhuser Overrides");
$ViewList = array();

$ViewList['alertageral'] = array (
    'script' => 'alertageral.php',
    'functions' => array( 'changeonlinestatus'),
    'params' => array()
);

?>