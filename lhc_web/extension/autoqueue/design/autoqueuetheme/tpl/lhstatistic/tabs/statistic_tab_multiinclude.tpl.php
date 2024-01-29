<li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'fila') : ?> active<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/fila" >Fila de Atendimento</a></li>
<li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'fila_hist') : ?> active<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/fila_hist" >Relatório Fila</a></li>
<li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'empty_subject') : ?> active<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/empty_subject" >Chats Sem Assunto</a></li>
<li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'folha_ponto') : ?> active<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/folha_ponto" >Folha de Ponto</a></li>
<li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'chats_por_hora') : ?> active<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/chats_por_hora" >Chats Por Horário</a></li>
<?php $currentUser = erLhcoreClassUser::instance(); ?>
<?php if ($currentUser->hasAccessTo('lhtotaladmin','use')) : ?>
    <li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'chat_operador') : ?> active<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/chat_operador" >Chats de Operadores</a></li>
<?php endif; ?>
<?php
/**
 * Override and append custom tabs
 */
?>