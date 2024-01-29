<?php
$lastOperatorChanged = false;
$lastOperatorId = false;
$lastOperatorNick = '';

$messagesStats = array(
    'total_messages' => count($messages),
    'counter_messages' => 0,
);

foreach ($messages as $msg) :
    $messagesStats['counter_messages']++;

if ($lastOperatorId !== false && ($lastOperatorId != $msg['user_id'] || $msg['name_support'] != $lastOperatorNick)) {
    $lastOperatorChanged = true;
    $lastOperatorNick = $msg['name_support'];
} else {
    $lastOperatorChanged = false;
}

$lastOperatorId = $msg['user_id'];
$lastOperatorNick = $msg['name_support'];


?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/user_msg_row.tpl.php'));?>
<?php endforeach; ?>

<!-- Caso o chat estiver ativo, a caixa de digitação de mensagens do usuário é desbloqueada -->
<?php if ($chat->status === erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) : ?>
<script>
    lhinst.enableVisitorEditor();
</script>
<!-- Caso o chat estiver pendente, a caixa de digitação de mensagens do usuário é bloqueada -->
<?php elseif($chat->status === erLhcoreClassModelChat::STATUS_PENDING_CHAT) : ?>
<script>
    lhinst.disableVisitorEditor();
</script>
    <?php 
    // Caso o chat fique pendente e esteja atribuido ao Assistente Virtual, a atribuição é removida
    $operador_id = erLhcoreClassModelUser::findOne(array('filter' => array('username' => 'Bot')));
    if($chat->user_id===$operador_id->id){
        $db = ezcDbInstance::get();
        $db->beginTransaction();
        $chat->user_id = 0;
        $chat->updateThis();
        $db->commit();
    }
    ?>
    <!-- Caso o chat esteja no bot e sem dono do chat definido, ele é atribuido ao Assistente Virtual
<?php elseif($chat->status === erLhcoreClassModelChat::STATUS_BOT_CHAT && $chat->user_id === 0) :?>
    <?php
        $db = ezcDbInstance::get();
        $db->beginTransaction();
        $operador_id = erLhcoreClassModelUser::findOne(array('filter' => array('username' => 'Bot')));
        $chat->user_id = $operador_id->id;
        $chat->updateThis();
        $db->commit();
    ?>
<?php endif;?>