<?php if ($canEditChat == true) :  ?>
	<div class="form-group" id="action-block-row-<?php echo $chat->id?>">
		<div class="send-row<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?> hide<?php endif;?>">
		
		<div class="btn-group btn-group-sm d-flex">
			
			<?php include(erLhcoreClassDesign::designtpl('lhchat/part/send_message_button.tpl.php')); ?>
			
			<?php include(erLhcoreClassDesign::designtpl('lhchat/part/speech_action.tpl.php')); ?>
			
			<?php include(erLhcoreClassDesign::designtpl('lhchat/part/translation_action.tpl.php')); ?>
              
            <?php include(erLhcoreClassDesign::designtpl('lhchat/part/send_delayed_canned_action.tpl.php')); ?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/part/hold_action.tpl.php')); ?>

			<!-- Para habilitar BB Code novamente, basta descomentar esta parte -->
            <!-- <?php //include(erLhcoreClassDesign::designtpl('lhchat/part/bbcode_action.tpl.php')); ?> -->

		</div>
		
		</div>
		<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?><input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Reopen chat')?>" class="btn btn-secondary" data-id="<?php echo $chat->id?>" onclick="lhinst.reopenchat($(this))" /><?php endif;?>
	</div>
	
	<?php include(erLhcoreClassDesign::designtpl('lhchat/part/canned_messages_action.tpl.php')); ?>
<?php endif; ?>
