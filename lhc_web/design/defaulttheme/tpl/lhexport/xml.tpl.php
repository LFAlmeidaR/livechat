<?php // Do not edit this file but create an override ?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>';?>
<chat>
<id><![CDATA[ <?php echo $chat->id?> ]]></id>
<url><![CDATA[<?php echo erLhcoreClassSystem::getHost()?><?php echo erLhcoreClassDesign::baseurl('chat/single')?>/<?php echo $chat->id?>]]></url>
<requested_by><![CDATA[ <?php echo htmlspecialchars($chat->email)?> ]]></requested_by>
<created_at type="datetime"><?php echo date('c',$chat->time)?></created_at>
<page_url><![CDATA[ <?php echo htmlspecialchars($chat->referrer)?> ]]></page_url>
<page_referrer><![CDATA[ <?php echo htmlspecialchars($chat->session_referrer)?> ]]></page_referrer>
<chat_initiator><![CDATA[ <?php echo $chat->chat_initiator?> ]]></chat_initiator>
<nick><![CDATA[ <?php echo htmlspecialchars($chat->nick)?>]]></nick>
<ip_address><?php echo htmlspecialchars($chat->ip)?></ip_address>
<unanswered><?php echo $chat->na_cb_executed?></unanswered>
<priority><?php echo $chat->priority?></priority>
<country_code><?php echo $chat->country_code?></country_code>
<country><![CDATA[ <?php echo htmlspecialchars($chat->country_name)?> ]]></country>
<city><![CDATA[ <?php echo htmlspecialchars($chat->city)?> ]]></city>
<remarks><![CDATA[ <?php echo htmlspecialchars($chat->remarks)?> ]]></remarks>
<latitude><?php echo $chat->lat?></latitude>
<longitude><?php echo $chat->lon?></longitude>
<additional_data><![CDATA[<?php echo htmlspecialchars($chat->additional_data)?>]]></additional_data>
<operator_variables type="array">
<?php if (!empty($chat->chat_variables)) : $chatVariables = json_decode($chat->chat_variables); ?>

<?php if (isset($chatVariables['variables'])) : ?>
	<?php foreach ($chatVariables['variables'] as $key => $variable) : ?>

	<?php if (strpos($key, '_ignore') === false && !isset($chatVariables['variables'][$key.'_ignore'])) : ?>
		<operator_variable>
				<name><![CDATA[<?php echo $key?>]]></name>
				<value><![CDATA[<?php echo htmlspecialchars($variable)?>]]></value>
		</operator_variable>
	<?php endif;?>

	<?php endforeach;?>
<?php endif;?>

<?php endif;?>
</operator_variables>

<operator_variables_filled type="array">

<?php if (isset($chatVariables['variables_filled'])) : ?>
	<?php foreach ($chatVariables['variables_filled'] as $key => $variables) : ?>
		<operator_variable>
				<name><![CDATA[<?php echo $key?>]]></name>
				<?php foreach ($variables as $variable => $value) : ?>
					<value_<?php echo $variable?>><![CDATA[<?php echo htmlspecialchars($value)?>]]></value_<?php echo $variable?>>
				<?php endforeach;?>
		</operator_variable>
	<?php endforeach;?>
<?php endif;?>

</operator_variables_filled>

<transcripts type="array">
<?php foreach (erLhcoreClassModelmsg::getList(array('limit' => 5000, 'filter' => array('chat_id' => $chat->id))) as $msg) : ?>
<transcript>
	<id><?php echo $msg->user_id?></id>
	<date type="datetime"><?php echo date('r',$msg->time)?></date>
	<message><![CDATA[<?php echo htmlspecialchars($msg->msg)?>]]></message>
	<user_id><?php echo $msg->user_id?></user_id>
	<alias>
	<?php if ($msg->user_id == -1) : ?>
	system
	<?php elseif ($msg->user_id == 0) : ?>
	visitor
	<?php elseif ($msg->user_id > 0) : ?>
	<?php echo htmlspecialchars($msg->name_support)?>
	<?php endif;?>
	</alias>
</transcript>
<?php endforeach;?>
</transcripts>
<chat_waittime type="integer"><?php echo $chat->wait_time?></chat_waittime>
<chat_duration type="integer"><?php echo $chat->chat_duration?></chat_duration>
</chat>