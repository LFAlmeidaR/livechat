<?php
$currentUser = erLhcoreClassUser::instance();
$tpl = erLhcoreClassTemplate::getInstance('lhuser/alertageral.tpl.php');
$tpl->set('user',$user);
$msg = new erLhcoreClassModelmsg();
$k=0;

if ( isset($_POST['MandarAlertaGeral']) ) {
    $currentUserData = $currentUser->getUserData();
    $onlineOperators = array();
    foreach(erLhcoreClassModelUserDep::getOnlineOperators($currentUser, true, array(), 50, 600) as $operadores){
         $onlineOperators[$operadores->user_id] = erLhcoreClassModelUser::findOne(array(
                'filter' => array(
                        'id' => $operadores->user_id
                )
            )); 
    }
    $chat = array();
    $msgs = array();
    $receber = false;
    foreach($onlineOperators as $operadores){
        foreach($_POST['UserDepartment'] as $dep){
            if($dep != 0){
                if(strpos($operadores->departments_ids, (string)$dep) === false && $receber === false){ // Se o usuario não faz parte dos departamentos escolhidos
                    $receber=false;
                } else { // Se faz parte de pelo menos um dos departamentos escolhidos
                    $receber=true;
                }
            } else {
                $receber=true;
                break;
            }
        }        
        if((int)$operadores->id != (int)$currentUser->getUserID() && $receber && $_POST['Mensagem']!=''){
            $k++;       
            $msgs[$operadores->id] = new erLhcoreClassModelmsg();
            $msgs[$operadores->id]->msg = "AVISO!!!\n" . $_POST['Mensagem'];

            // Fluxo normal de criação de chats entre operadores
    	    if (!($chat[$operadores->id] instanceof erLhcoreClassModelChat))
    	    {
    	        $chat[$operadores->id] = new erLhcoreClassModelChat();
    	        $chat[$operadores->id]->time = time();
    	        $chat[$operadores->id]->status = erLhcoreClassModelChat::STATUS_OPERATORS_CHAT;
    	        $chat[$operadores->id]->setIP();
    	        $chat[$operadores->id]->hash = erLhcoreClassChat::generateHash();
    	        $chat[$operadores->id]->referrer = '';
                $chat[$operadores->id]->session_referrer = '';
                $chat[$operadores->id]->nick = $currentUserData->name.' '.$currentUserData->surname;
                $chat[$operadores->id]->user_id = $operadores->id; // Assign chat to receiver operator, this way he will get permission to open chat
                $chat[$operadores->id]->dep_id = 0;//erLhcoreClassUserDep::getDefaultUserDepartment(); // Set default department to chat creator, this way current user will get permission to open it
                $chat[$operadores->id]->sender_user_id = $currentUser->getUserID();
    	    
    	        $chat[$operadores->id]->saveThis();
    	    }
    	
            // Store User Message
            $msgs[$operadores->id]->chat_id = $chat[$operadores->id]->id;
            $msgs[$operadores->id]->user_id = $currentUser->getUserID();
            $msgs[$operadores->id]->time = time();
            $msgs[$operadores->id]->name_support = trim($currentUserData->name.' '.$currentUserData->surname);
            erLhcoreClassChat::getSession()->save($msgs[$operadores->id]);

            $transfer[$operadores->id] = new erLhcoreClassModelTransfer();
            $transfer[$operadores->id]->chat_id = $chat[$operadores->id]->id;

            $transfer[$operadores->id]->from_dep_id = $chat[$operadores->id]->dep_id;

            // User which is transfering
            $transfer[$operadores->id]->transfer_user_id = $currentUser->getUserID();

            // To what user
            $transfer[$operadores->id]->transfer_to_user_id = $operadores->id;

            erLhcoreClassTransfer::getSession()->save($transfer[$operadores->id]);
        }
    }
    // Se ninguém puder receber o alerta
    if($k===0){
        echo '<script type="text/javascript">';
        echo 'alert("Falha ao Enviar o Aviso!");';
        echo 'window.location.href = "' . erLhcoreClassDesign::baseurl('system/configuration#!#alerta_geral') . '";'; 
        echo '</script>;';
    }
    	// Started chat
    	$tpl->set('started_chat', $chat);
    } 

$tpl->set('msg',$msg);

echo $tpl->fetch();
exit;
?>