<!-- Envia o aviso para os operadores -->
<?php if (isset($started_chat)) : ?>
<?php foreach($started_chat as $chats): ?>
<script>
lhinst.startChat(<?php echo $chats->id?>,$('#tabs'),'<?php echo erLhcoreClassDesign::shrt($chats->nick,10,'...',30,ENT_QUOTES)?>');
</script>
<?php endforeach; ?>
<?php endif; ?>
<script type="text/javascript">
    alert("Aviso Enviado com Sucesso!");
    setTimeout(function(){window.location.href = "<?php echo erLhcoreClassDesign::baseurl('system/configuration#!#alerta_geral') ?>";}, 1); 
</script>