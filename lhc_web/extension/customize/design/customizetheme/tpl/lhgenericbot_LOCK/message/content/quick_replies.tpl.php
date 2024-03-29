<div class="meta-message meta-message-<?php echo $messageId?>">
    <ul class="quick-replies list-inline meta-auto-hide">
    <?php foreach ($metaMessage as $item) : $disabledButton = isset($item['content']['disabled']) && $item['content']['disabled'] == true;?>
            <?php if ($item['type'] == 'url') : ?>
            <li>
                <a <?php if (isset($item['content']['payload_message']) && $item['content']['payload_message'] != '') : ?>onclick='lhinst.buttonClicked(<?php echo json_encode($item['content']['payload_message'])?>,<?php echo $messageId?>,$(this))'<?php else : ?>onclick="lhinst.disableVisitorEditor()"<?php endif;?> class="btn btn-xs btn-info btn-bot" target="_blank" href="<?php echo htmlspecialchars($item['content']['payload'])?>"><i class="material-icons">open_in_new</i><?php echo htmlspecialchars($item['content']['name'])?></a>
            </li>
             <?php elseif ($item['type'] == 'trigger') : ?>
			<div style="margin-inline-end:0.5%; line-height: 1.8; float:left;"> 
            <li><button type="button" class="btn btn-xs btn-info btn-bot" <?php if ($disabledButton == true) : ?>disabled="disabled"<?php endif;?> <?php if ($disabledButton == false) : ?>onclick='lhinst.updateTriggerClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,$(this))'<?php endif;?>><?php echo htmlspecialchars($item['content']['name'])?></button></li>
			</div>
            <?php elseif ($item['type'] == 'updatechat') : ?>
                <?php if ($item['content']['payload'] == 'subscribeToNotifications') : $detectMobile = new Mobile_Detect; ?>
                    <?php if (!$detectMobile->is('IOS') && erLhcoreClassModelNotificationSubscriber::getCount(array('filter_custom' => array('`chat_id` = ' . (int)$chat->id . ($chat->online_user_id > 0 ? ' OR `online_user_id` = ' . (int)$chat->online_user_id : '')))) == 0) : ?>
                        <div style="margin-inline-end:0.5%; line-height: 1.8; float:left;">  
                        <li><button type="button" <?php if ($disabledButton == true) : ?>disabled="disabled"<?php endif;?> class="btn btn-xs btn-info btn-bot" <?php if ($disabledButton == false) : ?>onclick='$(this).attr("disabled","disabled").text("Subscribing...");notificationsLHC.sendNotification();'<?php endif;?>><?php echo htmlspecialchars($item['content']['name'])?></button></li>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                        <div style="margin-inline-end:0.5%; line-height: 1.8; float:left;"> 
                        <li><button type="button" <?php if ($disabledButton == true) : ?>disabled="disabled"<?php endif;?> class="btn btn-xs btn-info btn-bot" <?php if ($disabledButton == false) : ?>onclick='lhinst.updateChatClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,$(this))'<?php endif;?>><?php echo htmlspecialchars($item['content']['name'])?></button></li>
                        </div>
                <?php endif; ?>
            <?php else : ?>
                        <div style="margin-inline-end:0.5%; line-height: 1.8; float:left;"> 
                        <li><button type="button" <?php if ($disabledButton == true) : ?>disabled="disabled"<?php endif;?> class="btn btn-xs btn-info btn-bot" <?php if ($disabledButton == false) : ?>onclick='lhinst.buttonClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,$(this))'<?php endif;?>><?php echo htmlspecialchars($item['content']['name'])?></button></li>
                        </div>
            <?php endif?>
    <?php endforeach; ?>
    </ul>
</div>

