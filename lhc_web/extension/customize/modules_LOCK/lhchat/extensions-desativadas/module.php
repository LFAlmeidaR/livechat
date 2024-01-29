<?php
$Module = array( "name" => "lhchat Overrides");
$ViewList = array();
   
$ViewList['syncadmin'] = array( 
    'script' => 'syncadmin.php',
    'params' => array(),
    'functions' => array( 'use' )
);
$ViewList['checkchatstatus'] = array(
    'script' => 'checkchatstatus.php',
    'params' => array('chat_id','hash'),
    'uparams' => array('mode','theme','dot')
);
$ViewList['startchat'] = array (
    'script' => 'startchat.php',
    'params' => array(),
    'uparams' => array('ua','switchform','operator','theme','er','vid','hash_resume','sound','hash','offline','leaveamessage','department','priority','chatprefill','survey','prod','phash','pvhash','ajaxmode'),
	'multiple_arguments' => array ( 'department', 'ua', 'prod' )
);
$ViewList['chatwidget'] = array (
    'script' => 'chatwidget.php',
    'params' => array(),
    'uparams' => array('ua','switchform','operator','theme','vid','sound','hash','hash_resume','mode','offline','leaveamessage','department','priority','chatprefill','survey','sdemo','prod','phash','pvhash','fullheight','ajaxmode'),
	'multiple_arguments' => array ( 'department', 'ua', 'prod' )
);
$ViewList['userclosechat'] = array(
    'script' => 'userclosechat.php',
    'params' => array('chat_id','hash'),
    'uparams' => array('eclose'),
);
$ViewList['syncadmininterface'] = array(
    'script' => 'syncadmininterface.php',
    'params' => array(),
    'uparams' => array('clcs','limitb','botd','odpgroups','ddgroups','udgroups','mdgroups', 'cdgroups', 'pdgroups','adgroups','pugroups','augroups','onop', 'acs', 'mcd', 'limitmc', 'mcdprod','activeu','pendingu','topen','departmentd','operatord','actived','pendingd','closedd','unreadd','limita','limitp','limitc','limitu','limito','limitd','activedprod','unreaddprod','pendingdprod','closeddprod','psort'),
    'ajax' => true,
    'functions' => array( 'use' ),
    'multiple_arguments' => array ('odpgroups','ddgroups','udgroups','mdgroups', 'cdgroups', 'pdgroups', 'adgroups', 'pugroups','augroups','mcd','operatord','mcdprod', 'activeu', 'pendingu', 'actived', 'closedd' , 'pendingd', 'unreadd','departmentd','activedprod','unreaddprod','pendingdprod','closeddprod')
);
$ViewList['sendmail'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'sendmail' )
);
?>