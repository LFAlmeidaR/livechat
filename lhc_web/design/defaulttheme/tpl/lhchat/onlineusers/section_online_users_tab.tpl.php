<?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_online_users_tab_pre.tpl.php')); ?>
<?php if ($chat_onlineusers_section_online_users_tab_enabled == true && $currentUser->hasAccessTo('lhchat', 'use_onlineusers') == true) : ?>
<li role="presentation" class="nav-item"><a class="nav-link" href="#onlineusers" aria-controls="onlineusers" role="tab" data-bs-toggle="tab" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Online visitors list');?>"><i class="material-icons me-0">face</i></a></li>
<?php endif;?>
