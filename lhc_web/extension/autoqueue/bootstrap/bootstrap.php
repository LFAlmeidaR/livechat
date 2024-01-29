<?php

class erLhcoreClassExtensionAutoqueue {

    public function __construct() {

    }

    //Cadastrar os event listeners aqui
    public function run() {

        $this->registerAutoload();

        $dispatcher = erLhcoreClassChatEventDispatcher::getInstance();

        $dispatcher->listen('statistic.valid_tabs', array($this, 'statisticValidTabs')); //abas válidas da estatística
        $dispatcher->listen('statistic.process_tab', array($this, 'statisticProcessTab')); //processar filtros de estatística
        $dispatcher->listen('chat.accept', array($this, 'chatAccept')); //ao aceitar um chat
        //$dispatcher->listen('user.login_after_success_authenticate', array($this, 'userLoginAfterAuth')); //assim que o usuário é autenticado, após o login
        $dispatcher->listen('chat.chat_transfered', array($this, 'chatTransfered')); //chat acaba de ser transferido
        $dispatcher->listen('chat.chat_transfer_accepted', array($this, 'chatTransferAccepted')); //após aceitar a transferencia
        //$dispatcher->listen('chat.syncadmininterface', array($this, 'syncAdminInterface')); //após sincronização (padrão - 6s)
        $dispatcher->listen('chat.workflow.autoassign', array($this, 'chatWorkflowAutoassign')); //atribuir um usuário antes do fluxo normal da fila
        //erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.dashboardwidgets',array('supported_widgets' => & $supportedWidgets)); novos widgets
        //$dispatcher->listen('chat.close', array($this, 'sendEmailToUser')); // Manda email para o usuário ao fechar o chat
        $dispatcher->listen('chat.close', array($this, 'chatClose')); //ao encerrar o chat envia o usuario para a pesquisa de satisfa��o
        $dispatcher->listen('chat.redirected_to_survey_by_autoresponder', array($this, 'redirectedToSurveyByAutoResponder')); // Assim que o AutoResponder redireciona o usuário para o questionário de avaliação
        //$dispatcher->listen('chat.explicitly_closed', array($this, 'chatExplicitlyClosed')); //Quando o usuário clica no "X" para fechar o chat
        //$dispatcher->listen('chat.workflow.canned_message_filter', array($this, 'cannedMessageFilter')); //Busca das mensagens predefinidas pelo título e não pelo conteúdo da mensagem
    }

    public function cannedMessageFilter(& $params){ //Altera o filtro de busca das mensagens predefinidas para buscar pelo título
        $q = $params['q'];
        $filter = &$params['filter'];
        $paramsFilter = $params['params_filter'];
        $department_id = $params['department_id'];
        $user_id = $params['user_id'];
        $items = &$params['items'];
        $session = &$params['session'];
        $filter[] = $q->expr->lOr($q->expr->eq('department_id', $q->bindValue($department_id)), $q->expr->lAnd($q->expr->eq('department_id', $q->bindValue(0)), $q->expr->eq('user_id', $q->bindValue(0))), $q->expr->eq('user_id', $q->bindValue($user_id)));
	
	    if (isset($paramsFilter['q']) && $paramsFilter['q'] != '') {
            $filter[] = $q->expr->lOr($q->expr->like('title', $q->bindValue('%' . $paramsFilter['q'] . '%')), $q->expr->like('msg', $q->bindValue('%' . $paramsFilter['q'] . '%'))); // Alteração do filtro
	    }

	    if (isset($paramsFilter['id']) && !empty($paramsFilter['id'])) {
	        $filter[] = $q->expr->in('id', $paramsFilter['id']);
	    }
	        
	    $q->where($filter);
	       
	    $q->limit(5000, 0);
	    $q->orderBy('position ASC, title ASC');
        $items = $session->find($q);
        return array('status' => erLhcoreClassChatEventDispatcher::STOP_WORKFLOW); // Necessário para não executar o filtro padrão
    }

    public static function lastActivity($userid){ // Atualiza a última atividade do operador quando ele está offline (raio cortado)
        $db = ezcDbInstance::get();
        $stmt = $db->prepare("SELECT id FROM lh_users_online_session WHERE user_id = :user_id AND lactivity > :lactivity_back");
        $stmt->bindValue(':user_id',$userid,PDO::PARAM_INT);
        $stmt->bindValue(':lactivity_back',time()-40,PDO::PARAM_INT);
        $stmt->execute();
        $id = $stmt->fetch(PDO::FETCH_COLUMN);

        if (is_numeric($id)) {
            $stmt = $db->prepare('UPDATE lh_users_online_session SET lactivity = :lactivity, duration = :lactivity_two - time WHERE id = :id');
            $stmt->bindValue(':id',$id,PDO::PARAM_INT);
            $stmt->bindValue(':lactivity_two',time(),PDO::PARAM_INT);
            $stmt->bindValue(':lactivity',time(),PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $db->prepare('INSERT INTO lh_users_online_session SET time = :time, lactivity = :lactivity, duration = 0, user_id = :user_id');
            $stmt->bindValue(':lactivity',time(),PDO::PARAM_INT);
            $stmt->bindValue(':time',time(),PDO::PARAM_INT);
            $stmt->bindValue(':user_id',$userid,PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    //Fecha o chat
    public function chatExplicitlyClosed($params){
        $chat = $params;
        if(!($chat['chat']->status===erLhcoreClassModelChat::STATUS_CLOSED_CHAT)){

            $subject = erLhAbstractModelSubject::findOne(array('filter' => array('name' => '5 - Visitante Fechou o Chat')));
            $db = ezcDbInstance::get();
            $sql = 'INSERT INTO lh_abstract_subject_chat (subject_id, chat_id) VALUES (:subject_id, :chat_id)';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':subject_id', $subject->id, PDO::PARAM_INT);
            $stmt->bindValue(':chat_id', $chat['chat']->id, PDO::PARAM_INT);
            $stmt->execute();

            $msg = new erLhcoreClassModelmsg();
            $msg->msg = htmlspecialchars_decode("Visitante fechou o chat.",ENT_QUOTES);
            $msg->chat_id = $chat['chat']->id;
            $operador = erLhcoreClassModelUser::findOne(array('filter' => array('username' => 'Bot')));
            $msg->user_id = $operador->id;
            $msg->name_support = $operador->chat_nickname;
            $msg->time = time();

            erLhcoreClassChat::getSession()->save($msg);

            $chat['chat']->last_user_msg_time = $msg->time;

            if ($chat['chat']->last_msg_id < $msg->id) {
                $chat['chat']->last_msg_id = $msg->id;
            }
            erLhcoreClassChatHelper::closeChat($chat);
        }
    }

    //Fecha o chat
    public function redirectedToSurveyByAutoResponder($params){
        $chat = $params;
        erLhcoreClassChatHelper::closeChat($chat);
    }

    // Manda a transcrição do chat por email para o usuário ao fechar o chat
    public function sendEmailtoUser($params){
        $chat = $params['chat'];
        
        //Busca a template do email a ser enviado
        $tpl = erLhcoreClassTemplate::getInstance('lhchat/sendmail.tpl.php');
        $mailTemplate = erLhAbstractModelEmailTemplate::fetch(3); 
        erLhcoreClassChatMail::prepareSendMail($mailTemplate, $chat);
        //Usa o email do usuário como destinatário
        $mailTemplate->recipient = $chat->email;

        // Busca as mensagens do chat
        $messages = array_reverse(erLhcoreClassModelmsg::getList(array('customfilter' => array('user_id != -1'),'limit' => 500, 'sort' => 'id DESC','filter' => array('chat_id' => $chat->id))));
        $tpl = new erLhcoreClassTemplate( 'lhchat/messagelist/plain.tpl.php');
        $tpl->set('chat', $chat);
        $tpl->set('messages', $messages);

        //Adiciona as mensagens no template do email
        $mailTemplate->content = str_replace(array('{user_chat_nick}','{messages_content}','{chat_id}'), array($chat->nick, $tpl->fetch(), $chat->id), $mailTemplate->content);

        //Manda o email
        erLhcoreClassChatMail::sendMail($mailTemplate, $chat);
    }

    public function chatClose($params) {
        $chat = $params['chat'];

        if(!$params['user_data']){ // Verifica se existe um operador no chat
            $user = erLhcoreClassModelUser::findOne(array(
                'filter' => array(
                    'username' => 'Bot'  // Caso não possua um operador, atribui um usuário "Bot" como operador padrão
                )
            )); 
            $params['user_data'] = $user;
        } // end if

        foreach (erLhAbstractModelSurvey::getList() as $item) {
            erLhcoreClassChatHelper::redirectToSurvey(array('survey_id' => $item->id, 'chat' => $params['chat'], 'user' => $params['user_data']));
        }
    }

    /*Função que interfere no workflow normal de atribuição da fila, para atribuir automaticamente para o atendente anterior, caso
     *exista um atendimento para o mesmo CPF na última hora*/
    public function chatWorkflowAutoassign($params) {
        $chat = $params['chat'];
        $time1h = time() - 3600; //De uma hora atrás para cá

        if ($chat->tslasign != 0) {
            return false; 
        }

        //abaixo retornará false para o workflow se não achar um usuário, fazendo com que atribua pelo método do chat
        $user_id = self::getUserLastChat($chat->id, $time1h, $chat->additional_data, $params['is_online']); //verificar se o usuário foi atendido na última hora e por quem
        if ($user_id && $user_id > 0) {
            return array( 'status' => erLhcoreClassChatEventDispatcher::STOP_WORKFLOW, 'user_id' => $user_id);
        } else {
            return false;
        }
    }

    /*Função ao transferir um chat. Está sendo utilizada para não penalizar um atendente que transfere o chat para outro dar a continuidade.
     *Válido apenas para as últimas 6 horas. Após isto o atendente perde a vez se o transferir. */
    public function chatTransfered($params) {
        $chat = $params['chat'];
        $time1h = time() - (6 * 3600); //De 6 horas atrás para cá

        $transfer = erLhcoreClassTransfer::getTransferByChat($chat->id); //recuperar dados da transferencia

        $user_id = self::getUserLastChat($chat->id, $time1h, $chat->additional_data); //verificar se o usuário foi atendido nas últimas 6 horas e por quem

        if ($user_id && $transfer['transfer_to_user_id'] == $user_id) {
            erLhcoreClassUserDep::updateLastAcceptedByUser($transfer['transfer_user_id'], time() - (24 * 3600)); //coloca o atendente que transferiu para o início da fila
        }
    }

    /* Função para o momento de aceite de uma transferência. Sempre coloca o usuário que recebeu a transferência para o final da fila. */
    public function chatTransferAccepted($params) {
        $chat = $params['chat'];

        erLhcoreClassChat::updateActiveChats($chat->transfer_uid); //atualiza a quantidade de chats ativos de quem transferiu
        erLhcoreClassChat::updateActiveChats($chat->user_id); //atualiza a quantidade de chats ativos de quem recebeu
        if ($chat->status != erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) { //apenas se não for chat de operadores
            erLhcoreClassUserDep::updateLastAcceptedByUser($chat->user_id, time()); //O atendente que aceitou a transferência sempre vai para o final da fila
        }
    }

    //Verificar os chats transferidos acima do limite de tempo e retransferir. Pendente do método para retransferência
    /*public function syncAdminInterface($params) {

        $transferchatsUser = $params['lists']['transfer_chats']['list'];

        if (!empty($transferchatsUser)) {
            $time = time();
            foreach ($transferchatsUser as & $transf) {
                $dept = erLhcoreClassModelDepartament::fetch($transf['dep_id']);
                $diff = $time - $transf['time'];
                if ($time - $transf['time'] > $dept->max_timeout_seconds && $time - $transf['tslasign'] > $dept->max_timeout_seconds) {
                    //erLhcoreClassTransfer::handleTransferredChatOpen()
                    $chat = erLhcoreClassChat::getSession()->load('erLhcoreClassModelChat', $transf['id']);

                    $chat->user_id = $transf['user_id'];
                    $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
                    $chat->updateThis();

                    erLhcoreClassChat::updateActiveChats($transf['transfer_uid']); //atualiza a quantidade de chats ativos
                    //erLhcoreClassUserDep::updateLastAcceptedByUser($transf['user_id']);

                    erLhcoreClassChatWorkflow::autoAssign($chat, $dept);


                }
            }
        }

    }*/

    //função para adicionar a abas às abas válidas de estatística
    public function statisticValidTabs($params) {
        $params['valid_tabs'][] = 'fila';           //fila de atendimento
        $params['valid_tabs'][] = 'fila_hist';     //relatorio da fila
        $params['valid_tabs'][] = 'empty_subject';  //assuntos vazios    
        $params['valid_tabs'][] = 'folha_ponto';    //folha de ponto
        $params['valid_tabs'][] = 'chat_operador'; //chats de operadores
        $params['valid_tabs'][] = 'chats_por_hora'; //chats por horario
    }

    //função para tratar filtros de estatística nas páginas customizadas
    public function statisticProcessTab($params) { // tpl / params
        return;
    }

    //função para zerar o contador assim que o operador aceitar o chat, e não quando o sistema atribuir
    public function chatAccept($params) {
        $user_id = $params['user']->getUserID();
        erLhcoreClassChat::updateActiveChats($user_id); //atualiza a quantidade de chats ativos
        erLhcoreClassUserDep::updateLastAcceptedByUser($user_id, time()); //coloca o atendente para o final da fila
    }

    //função para zerar o contador assim que o operador loga no sistema pela p  rimeira vez no dia
    //só é valido se existe pausa entre os turnos da noite (antes das 00:00) e da manhã
    public function userLoginAfterAuth($params) {
        $userid = $params['current_user']->getUserID();

        try {

            $db = ezcDbInstance::get();

            $sql = "SELECT last_accepted FROM lh_userdep WHERE user_id = :user_id ORDER BY last_accepted DESC LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $userid, PDO::PARAM_INT);

            $stmt->execute();

            $last_accepted = $stmt->fetchColumn();

			$today = new DateTime();
			$today->setTime(0, 0);

			$today_timest = $today->getTimestamp(); //recupera a timestamp de 00:00 do dia atual

            if ($last_accepted < $today_timest) {
                erLhcoreClassUserDep::updateLastAcceptedByUser($userid, time()); //coloca o atendente para o final da fila
            }

            erLhcoreClassChat::updateActiveChats($userid); //atualiza a quantidade de chats ativos

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    /*Verifica o último chat para o CPF dentro do período de tempo, excluindo o chat atual */
    public static function getUserLastChat($chat_id, $user_msg_time, $additional_data, $isOnlineUser = null) {

        $cpf = explode("},{", $additional_data)[0] . "%"; //feito desta forma para facilitar o LIKE - performance

        try {

            $db = ezcDbInstance::get();

            if ($isOnlineUser === null) {
                $isOnlineUser = (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'];
            }

            $sql = "SELECT c.user_id FROM lh_chat c INNER JOIN lh_userdep u ON c.user_id = u.user_id WHERE c.last_user_msg_time > :time1h AND c.additional_data LIKE :cpf AND c.id != :chat_id AND u.ro = 0 AND u.hide_online = 0 AND u.last_activity > :last_activity ORDER BY last_user_msg_time DESC LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':cpf', $cpf, PDO::PARAM_STR);
            $stmt->bindValue(':time1h', $user_msg_time, PDO::PARAM_INT);
            $stmt->bindValue(':chat_id', $chat_id, PDO::PARAM_INT);
            $stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetchColumn();

        } catch (Exception $e) {
            //TODO exception logic
        }
    }

    public function registerAutoload()
    {
        spl_autoload_register(array($this, 'autoload'), true, false);
    }

    public function autoload($className)
    {
        $classes = array(
            'erLhcoreClassModelAutoqueueHistory' => 'extension/autoqueue/classes/lhautoqueuehistory.php'
        );

        if (key_exists($className, $classes)) {
            include_once $classes[$className];
        }
    }

}