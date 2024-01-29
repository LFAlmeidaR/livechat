<?php
$Module = array( "name" => "lhgenericbot Overrides");
$ViewList = array();

$ViewList['nodetriggeractions'] = array(
    'script' => 'nodetriggeractions.php',
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

?>