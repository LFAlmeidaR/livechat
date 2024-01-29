<?php

/**
 * Override and append custom tabs content
 */

if ($tab == 'fila') { //Ordem da Fila de Atendimento
    try {

        $isOnlineUser = (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'];

        $db = ezcDbInstance::get();

        $sql = "SELECT u.name, u.surname, last_accepted FROM lh_userdep d INNER JOIN lh_users u ON d.user_id = u.id WHERE ro = 0 AND d.hide_online = 0 AND last_activity > :last_activity GROUP BY user_id ORDER BY pending_chats + active_chats ASC, last_accepted ASC ;";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':last_activity', time() - $isOnlineUser, PDO::PARAM_INT);

        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "Fila 'Tempo Real' de Atendimento (leva em consideração se o atendente tem chats ativos ou pendentes)" . '<br>';
        foreach ($users as $user) {
            echo $user['name'] . ' ' . $user['surname'] . '<br>';
        }

        uasort($users, function($a, $b) {
            return $a['last_accepted'] - $b['last_accepted'];
        });

        echo '<br>' . "Fila 'Estática' de Atendimento (leva em consideração apenas o tempo desde o último chat)" . '<br>';
        foreach ($users as $user) {
            echo $user['name'] . ' ' . $user['surname'] . '<br>';
        }

    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
}

if ($tab == 'fila_hist') { //Histórico da Fila de Atendimento
    $history = new erLhcoreClassModelAutoqueueHistory();
    $resultados = $history->todayHistory();

    ?>
    <table class="table" cellpadding="0" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Informação do Chat</th>
                <th>Operador Anterior</th>
                <th>Novo Operador</th>
                <th>Motivo</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($resultados as $resultado) : ?>
                <tr>
                    <td>
                        <a class="material-icons" onclick="lhc.previewChat(<?php echo $resultado['id']?>)">info_outline</a>          
                        <a class="action-image material-icons" data-title="<?php echo htmlspecialchars($resultado['nick'],ENT_QUOTES);?>" onclick="lhinst.startChatNewWindow('<?php echo$resultado['id'];?>',$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Open in a new window');?>">open_in_new</a>
                        <a ng-click="lhc.startChat('<?php echo $resultado['chat_id']?>','<?php echo htmlspecialchars($resultado['nick'],ENT_QUOTES)?>')"><?php echo htmlspecialchars($resultado['nick']);?>, <small><i><?php echo date(erLhcoreClassModule::$dateDateHourFormat, $resultado['time']);?></i></small></a>
                    </td>
                    <td><?php echo htmlspecialchars($resultado['name'] . ' ' . $resultado['surname']); ?></td>
                    <td><?php echo htmlspecialchars($resultado['namepro'] . ' ' . $resultado['surnamepro']); ?></td>
                    <td><?php echo erLhcoreClassModelAutoqueueHistory::getMotivoString($resultado['motivo']); ?></td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>

<?php
}

if ($tab == 'empty_subject') { //Chats sem assunto
    ?>
    <form action="" method="post" autocomplete="off">
        <div class="row form-group">
            <div class="col-md-4">
            <div class="form-group">
                <label>Data Inicial</label>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" class="form-control form-control-sm" name="timefrom" id="id_timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($_POST['timefrom'])?>" />
                        </div>							
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range to');?></label>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" class="form-control form-control-sm" name="timeto" id="id_timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($_POST['timeto'])?>" />
                        </div>							
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User');?></label>
                    <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                        'input_name'     => 'user_ids[]',
                        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select user'),
                        'selected_id'    => $_POST['user_ids'],
                        'css_class'      => 'form-control',
                        'display_name'   => 'name_official',
                        'list_function'  => 'erLhcoreClassModelUser::getUserList'
                    )); ?>
                </div>
            </div>  
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-1">
                    <input type="submit" name="Pesquisar" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
                    </div>    		
                </div>		
            </div>
        </div>
    </form>
    <script>
	$(function() {
		$('#id_timefrom, #id_timeto').fdatepicker({
			format: 'yyyy-mm-dd'
		});
	});
    </script>
    <?php if(!isset($_POST['Pesquisar'])): ?>
        <div class="alert alert-info">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Please choose statistic parameters first!');?>
        </div>
    <?php elseif ((isset($_POST['Pesquisar']))): ?>
    <?php
        $db = ezcDbInstance::get();

        $sql = "SELECT c.id, u.name, u.surname, c.nick, c.time FROM lh_chat c INNER JOIN lh_users u ON c.user_id = u.id LEFT JOIN lh_abstract_subject_chat s ON s.chat_id = c.id WHERE c.dep_id != 0 AND c.status != :bot_dept AND s.subject_id IS NULL";
        $k = 0;
        if(isset($_POST['user_ids'])) {
            foreach($_POST['user_ids'] as $users){
                if($k==0){
                    $sql .= " AND c.user_id = ". $users;
                } else {
                    $sql .= " OR c.user_id = ". $users;
                } 
                $k++;
            }
        }
        
        if((isset($_POST['timefrom']) && $_POST['timefrom']!='')) {
            $data = new DateTime($_POST['timefrom']);
            $sql .= " AND c.time >= " . $data->getTimestamp();
        }
        if((isset($_POST['timeto']) && $_POST['timeto']!='')) {
            $data = new DateTime($_POST['timeto']);
            $sql .= " AND c.time <= " . $data->getTimestamp();
        }
        $sql .= " ORDER BY c.time DESC;";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':bot_dept', erLhcoreClassModelChat::STATUS_BOT_CHAT, PDO::PARAM_INT);

        $stmt->execute();

        $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include 'lib/core/lhform/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $globalStyle = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $style = array(
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            )
        );
        $objPHPExcel->getDefaultStyle()->applyFromArray($globalStyle);
        $x = 1;
        $y = 2;
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(35);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, "ID do Chat");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+1, $y, "Data");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+2, $y, "Operador");
        $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x+1).$y)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x+2).$y)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x+1).$y)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCFF33');
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x+2).$y)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFE4E1');
        $y++;
        ?>

        <a href="/chat/extension/autoqueue/design/autoqueuetheme/tpl/lhstatistic/tabs/ChatsSemAssunto.xls" class="btn btn-secondary">Baixar Relatório de Chats sem Assunto</a>
        <table class="table" cellpadding="0" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Chat sem assunto</th>
                    <th>Operador</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($chats as $chat) : ?>
                <tr>
                    <td>
                        <a class="material-icons" onclick="lhc.previewChat(<?php echo $chat['id']?>)">info_outline</a>          
                        <a class="action-image material-icons" data-title="<?php echo htmlspecialchars($chat['nick'],ENT_QUOTES);?>" onclick="lhinst.startChatNewWindow('<?php echo$chat['id'];?>',$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Open in a new window');?>">open_in_new</a>
                        <a ng-click="lhc.startChat('<?php echo $chat['id']?>','<?php echo htmlspecialchars($chat['nick'],ENT_QUOTES)?>')"><?php echo htmlspecialchars($chat['nick']);?>, <small><i><?php echo date(erLhcoreClassModule::$dateDateHourFormat, $chat['time']);?></i></small></a>
                    </td>
                    <td><?php echo htmlspecialchars($chat['name'] . ' ' . $chat['surname']);?></td>
                </tr>
                <?php 
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, $chat['id']);
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+1, $y, date(erLhcoreClassModule::$dateDateHourFormat, $chat['time']));
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+2, $y, $chat['name'] . ' ' . $chat['surname']);
                    $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->applyFromArray($style);
                    $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x+1).$y)->applyFromArray($style);
                    $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x+2).$y)->applyFromArray($style);
                    $y++;
                    endforeach;
                ?>
            </tbody>
        </table>
    <?php
        $objPHPExcel->getActiveSheet()->setTitle('ChatsSemAssunto');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        try{
            $objWriter->save('extension/autoqueue/design/autoqueuetheme/tpl/lhstatistic/tabs/ChatsSemAssunto.xls');
        } catch (Exception $e){
            echo $e->getMessage();
        }
        endif; 
     ?>
    <?php
}

if ($tab == 'folha_ponto') { //Relatório da Folha de Ponto
    ?>
    <form action="" method="get" autocomplete="off">
        <div class="row form-group">
            <div class="col-md-4">
            <div class="form-group">
                <label>Data Inicial</label>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" class="form-control form-control-sm" name="timefrom" id="id_timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($_GET['timefrom'])?>" />
                        </div>							
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range to');?></label>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" class="form-control form-control-sm" name="timeto" id="id_timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($_GET['timeto'])?>" />
                        </div>							
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User');?></label>
                    <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                        'input_name'     => 'user_ids[]',
                        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select user'),
                        'selected_id'    => $_GET['user_ids'],
                        'css_class'      => 'form-control',
                        'display_name'   => 'name_official',
                        'list_function'  => 'erLhcoreClassModelUser::getUserList'
                    )); ?>
                </div>
            </div>  
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-1">
                    <input type="submit" name="doSearch" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
                    </div>    		
                </div>		
            </div>
        </div>
    </form>
    <script>
	$(function() {
		$('#id_timefrom, #id_timeto').fdatepicker({
			format: 'yyyy-mm-dd'
		});
	});
    </script>
    <?php
    
    if(isset($_GET['doSearch'])){
        if(isset($_GET['timefrom']) && $_GET['timefrom']!=''){
            if(!isset($_GET['timeto']) || $_GET['timeto']==''){
                $dataini = new DateTime($_GET['timefrom']);
                $datafinal = new DateTime($_GET['timefrom']);                
            } else if(isset($_GET['timeto']) && $_GET['timeto']!=''){
                $dataini = new DateTime($_GET['timefrom']);
                $datafinal = new DateTime($_GET['timeto']);    
            }
            ?>
                <br>
                <a href="/livechat/lhc_web/extension/autoqueue/design/autoqueuetheme/tpl/lhstatistic/tabs/FolhaDePonto.xls" class="btn btn-secondary">Baixar Relatório da Folha de Ponto</a>
		<br>
		<br>
            <?php
            //CONSTRUINDO PLANILHA DO EXCEL
            include 'lib/core/lhform/PHPExcel.php';
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $globalStyle = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );
            $style = array(
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000'),
                    ),
                )
            );
            $objPHPExcel->getDefaultStyle()->applyFromArray($globalStyle);
            $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
            $x = 1;
            $y = 2;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y,"RELATORIO DE FOLHA DE PONTO DO CHAT TJDFT" );
            $objPHPExcel->getActiveSheet()->mergeCells('B'.$y.':D'.$y);
            $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->getFont()->setBold(true);
            $y++;
            ?>

            <!-- INÍCIO DA TABELA DE EXIBIÇÃO HTML-->
            <hr style="margin-top:-10px;"/>
            <br>
            <table class="table" cellpadding="0" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th colspan="3" width="100%" style='text-align: center;'><B>RELATORIO DE FOLHA DE PONTO DO CHAT TJDFT</B></th>
                    </tr>
                </thead>
            </table>

            <?php
            for($k=$dataini->getTimeStamp();$k<=$datafinal->getTimeStamp();$k+=86400){
                $x = 1;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, date('d/m/Y', $k));
                $objPHPExcel->getActiveSheet()->mergeCells('B'.$y.':D'.$y);
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->applyFromArray($style);
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x+1).$y)->applyFromArray($style);
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x+2).$y)->applyFromArray($style);
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->getFont()->setBold(true);
                $y++;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, 'Operador');
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+1, $y, 'Entrada');
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+2, $y, 'Saída');
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->applyFromArray($style);
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x+1).$y)->applyFromArray($style);
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x+2).$y)->applyFromArray($style);
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x+1).$y)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x+2).$y)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFE4E1');
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x+1).$y)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCFF33');
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x+2).$y)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FF3B1F');
                $y++;
                $filterParams['filter']['filtergte']['time'] = $k;
                $filterParams['filter']['filterlte']['time'] = $k + 86340;
                if(isset($_GET['user_ids']) && count($_GET['user_ids'])>0){
                    $filterParams['filter']['filterin']['user_id'] = $_GET['user_ids'];
                }
                $filterParams['is_search'] = true;
                $userlist2 = erLhcoreClassModelUser::getUserList();                
                $userlist = erLhcoreClassModelUserOnlineSession::getList(array_merge($filterParams['filter'],array( 'limit' => 3000,'sort' => 'id ASC')));
                $folha = array();
                if($userlist){
                    foreach($userlist as $usuarios){
                        if(isset($folha[$usuarios->user_id])){
                            $folha[$usuarios->user_id]['tempfinal'] = date('H:i:s',strtotime(($usuarios->lactivity_front)));
                        } else {
                            $folha[$usuarios->user_id] = array();
                            $folha[$usuarios->user_id]['id'] = $usuarios->user_name;
                            $folha[$usuarios->user_id]['nome']='';
                            $folha[$usuarios->user_id]['sobrenome']='';
                            $folha[$usuarios->user_id]['tempini'] = date('H:i:s',strtotime(($usuarios->time_front)));
                            $folha[$usuarios->user_id]['tempfinal'] = date('H:i:s',strtotime(($usuarios->lactivity_front)));
                            foreach($userlist2 as $u){
                                if($usuarios->user_id == $u->id){
                                        $folha[$usuarios->user_id]['nome'] =  $u->name;
                                        $folha[$usuarios->user_id]['sobrenome'] =  $u->surname;
                                }
                            }
                        }
                    }             
                            ?>
                    <!-- CONTINUAÇÃO DA TABELA DE EXIBIÇÃO -->
                    <table class="table" cellpadding="0" cellspacing="0" width="100%">
                         <thead>
                            <tr>
                                <th colspan="3" width="100%" style='text-align: center;'><B>Data: <?php echo date('d/m/Y', $k);?></B></th>
                            </tr>
                            <tr>
                                <th width="50%">Nome do Operador</th>
                                <th width="25%">Primeira Entrada</th>
                                <th width="25%">Última Saída</th>
                            </tr>
                        </thead>
 
                        <?php foreach($folha as $operadores): ?>
                            <tr>
                                <td width="50%"><?php echo $operadores['nome']." ".$operadores['sobrenome'];?></td>
                                <td width="25%"><?php echo $operadores['tempini'];?></td>
                                <td width="25%"><?php echo $operadores['tempfinal'];?></td>
                            </tr>
                        <?php 
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, $operadores['nome']." ".$operadores['sobrenome']);
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+1, $y, $operadores['tempini']);
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+2, $y, $operadores['tempfinal']);
                            $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->applyFromArray($style);
                            $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x+1).$y)->applyFromArray($style);
                            $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x+2).$y)->applyFromArray($style);
                            $y++;
                        endforeach; ?>
                    </table>
                    <hr style="margin-top:-10px;"/>
                    <br/>
                <?php
                $y++;
                }
            }
            $objPHPExcel->getActiveSheet()->setTitle('FolhaDePonto');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            try{
                $objWriter->save('livechat/lhc_web/extension/autoqueue/design/autoqueuetheme/tpl/lhstatistic/tabs/FolhaDePonto.xls');  
            } catch (Exception $e){
                echo $e->getMessage();
            }
        } else {
        ?>
        <div class="alert alert-info">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Please choose statistic parameters first!');?>
        </div>
        <?php
        }
    } else {
        ?>
        <div class="alert alert-info">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Please choose statistic parameters first!');?>
        </div>
        <?php
    }
}

if ($tab == 'chat_operador') {  //Chats de operadores
    $currentUser = erLhcoreClassUser::instance();
    if ($currentUser->hasAccessTo('lhtotaladmin','use')) {
        $db = ezcDbInstance::get();

        $sql = "SELECT c.id, u.name, u.surname, c.nick, c.time, c.status FROM lh_chat c INNER JOIN lh_users u ON c.user_id = u.id WHERE c.dep_id = 0 AND c.status != :bot_dept ORDER BY c.status DESC, c.time DESC ;";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':bot_dept', erLhcoreClassModelChat::STATUS_BOT_CHAT, PDO::PARAM_INT);

        $stmt->execute();

        $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ?>

        <table class="table" cellpadding="0" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Chats de Operadores</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($chats as $chat) : ?>
                <tr>
                    <td>
                        <a class="material-icons" onclick="lhc.previewChat(<?php echo $chat['id']?>)">info_outline</a>          
                        <a class="action-image material-icons" data-title="<?php echo htmlspecialchars($chat['nick'],ENT_QUOTES);?>" onclick="lhinst.startChatNewWindow('<?php echo$chat['id'];?>',$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Open in a new window');?>">open_in_new</a>
                        <a ng-click="lhc.startChat('<?php echo $chat['id']?>','<?php echo htmlspecialchars($chat['nick'],ENT_QUOTES)?>')"><?php echo htmlspecialchars($chat['nick']);?>, <small><i><?php echo date(erLhcoreClassModule::$dateDateHourFormat, $chat['time']);?></i></small></a>
                    </td>
                    <td><?php echo htmlspecialchars($chat['name'] . ' ' . $chat['surname']);?></td>
                    <td><?php 
                        if ($chat['status'] == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) {
                            $msg = 'ATIVO';
                        } else {
                            $msg = 'INATIVO';
                        }
                        echo htmlspecialchars($msg);
                    ?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <?php
    }
}

if($tab == 'chats_por_hora'){ 
    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    ?>
    <form action="" method="get" autocomplete="off">
        <div class="row form-group">
            <div class="col-md-3">
            <div class="form-group">
                <label>Mês Inicial</label>
                    <div class="row">
                        <div class="col-md-12">
                            <select class="form-control form-control-sm" name="mesI">
                                <option hidden disabled selected value="">Selecione</option>
                                <?php for($e=1;$e<=12;$e++) : ?>
                                <option value="<?php echo $e; ?>"> <?php $data = new DateTime('now'); $data->setDate(date('Y', $data->getTimestamp()), $e, 1); echo utf8_encode(ucwords(strftime('%B', $data->getTimestamp()))); ?> </option>
                                <?php endfor; ?>
                            </select>
                        </div>							
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Ano Inicial</label>
                    <div class="row">
                        <div class="col-md-12">
                            <select name="anoI" class="form-control form-control-sm">
                            <option hidden disabled selected value="">Selecione</option>
                            <?php for($e=2019;$e<=2050;$e++) : ?>
                            <option value="<?php echo $e ?>"><?php echo $e ?></option>
                            <?php endfor; ?>
                            </select>
                        </div>							
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Mês Final</label>
                    <div class="row">
                        <div class="col-md-12">
                            <select class="form-control form-control-sm" name="mesF">
                                <option hidden disabled selected value="">Selecione</option>
                                <?php for($e=1;$e<=12;$e++) : ?>
                                <option value="<?php echo $e; ?>"> <?php $data = new DateTime('now'); $data->setDate(date('Y', $data->getTimestamp()), $e, 1); echo utf8_encode(ucwords(strftime('%B', $data->getTimestamp()))); ?> </option>
                                <?php endfor; ?>
                            </select>
                        </div>							
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Ano Final</label>
                    <div class="row">
                        <div class="col-md-12">
                            <select name="anoF" class="form-control form-control-sm">
                            <option hidden disabled selected value="">Selecione</option>
                            <?php for($e=2019;$e<=2050;$e++) : ?>
                            <option value="<?php echo $e ?>"><?php echo $e ?></option>
                            <?php endfor; ?>
                            </select>
                        </div>							
                    </div>
                </div>
            </div> 
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-1">
                    <input type="submit" name="doSearch" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
                    </div>    		
                </div>		
            </div>
        </div>
    </form>
<?php
    if(isset($_GET['doSearch'])){
        if(isset($_GET['mesI']) && isset($_GET['anoI']) && $_GET['mesI'] != '' && $_GET['anoI'] != ''){
            $dataini = new DateTime($_GET['mesI'].'/01/'.$_GET['anoI']);
            $datafinal = new DateTime();
            if(isset($_GET['mesF']) && isset($_GET['anoF']) && $_GET['mesF'] != '' && $_GET['anoF'] != ''){
                $datafinal->setTimestamp($dataini->getTimestamp());
                for($i=$_GET['mesI'];$i<=$_GET['mesF']+(12*($_GET['anoF']-$_GET['anoI']));$i++){
                    $datafinal->add(date_interval_create_from_date_string('1 month'));
                }
                $datafinal->sub(date_interval_create_from_date_string('1 day'));
            } else {
                $datafinal->setTimestamp($dataini->getTimestamp());
                $datafinal->add(date_interval_create_from_date_string('1 month'));
                $datafinal->sub(date_interval_create_from_date_string('1 day'));
            }
            $aux = new DateTime();
            $db = ezcDbInstance::get();
            for($i = $dataini->getTimestamp() + 43200;$i<=$datafinal->getTimestamp() + 43200;$i+=86400){ // Chats 12-18:30 Atendentes
                $aux->setTimestamp($i);
                if((int)$aux->format('N')<6){
                    $sql = "SELECT c.id, c.user_id FROM lh_chat c INNER JOIN lh_users u ON c.user_id = u.id WHERE c.dep_id != 0 AND c.user_id != 22 AND c.time >= ".$i." AND c.time <= ".($i+23400). " ORDER BY c.time DESC;";
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $chatsDHorario[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            }
            $k = 0;
            $timestamp = $dataini->getTimestamp();
            foreach($chatsDHorario as $id => $chats){
                if(date("N", $timestamp)==6){
                    $timestamp += 172800;
                } else if(date("N", $timestamp)==7){
                    $timestamp += 86400;
                }
                $chatsDHorario[$id]['numDia'] = date("d", $timestamp);
                $chatsDHorario[$id]['numMes'] = date("m", $timestamp);
                $chatsDHorario[$id]['diaSemana'] = date("N", $timestamp);
                $timestamp += 86400;
                foreach($chats as $chat){
                    $k++;
                }
            } //
            for($i = $dataini->getTimestamp() + 43200;$i<=$datafinal->getTimestamp() + 43200;$i+=86400){ //Chats 12-18:30 Só BOT
                $aux->setTimestamp($i);
                if((int)$aux->format('N')<6){
                    $sql = "SELECT c.id, c.user_id FROM lh_chat c INNER JOIN lh_users u ON c.user_id = u.id WHERE c.dep_id != 0 AND c.user_id = 22 AND c.time >= ".$i." AND c.time <= ".($i+23400). " ORDER BY c.time DESC ;";
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $chatsDHorarioBot[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            }
            $j = 0;
            $timestamp = $dataini->getTimestamp();
            foreach($chatsDHorarioBot as $id => $chats){
                if(date("N", $timestamp)==6){
                    $timestamp += 172800;
                } else if(date("N", $timestamp)==7){
                    $timestamp += 86400;
                }
                $chatsDHorarioBot[$id]['numDia'] = date("d", $timestamp);
                $chatsDHorarioBot[$id]['numMes'] = date("m", $timestamp);
                $chatsDHorarioBot[$id]['diaSemana'] = date("N", $timestamp);
                $timestamp += 86400;
                foreach($chats as $chat){
                    $j++;
                }
            } // 
            for($i = $dataini->getTimestamp();$i<=$datafinal->getTimestamp();$i+=86400){ // Chats fora do horário (Só BOT)
                $aux->setTimestamp($i);
                if((int)$aux->format('N')>=6){
                    $sql = "SELECT c.id, c.user_id FROM lh_chat c INNER JOIN lh_users u ON c.user_id = u.id WHERE c.dep_id != 0 AND c.user_id = 22 AND c.time >= ".$i." AND c.time <= ".($i+86399). " ORDER BY c.time DESC ;";
                } else {
                    $sql = "SELECT c.id, c.user_id FROM lh_chat c INNER JOIN lh_users u ON c.user_id = u.id WHERE c.dep_id != 0 AND c.user_id = 22 AND c.time >= ".$i." AND c.time <= ".($i+43199). " OR c.dep_id != 0 AND c.user_id = 22 AND c.time >= ".($i+66601)." AND c.time <= ".($i+86399)." ORDER BY c.time DESC ;";
                }
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $chatsFHorario[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            $l = 0;
            $timestamp = $dataini->getTimestamp();
            foreach($chatsFHorario as $id => $chats){
                $chatsFHorario[$id]['numDia'] = date("d", $timestamp);
                $chatsFHorario[$id]['numMes'] = date("m", $timestamp);
                $chatsFHorario[$id]['diaSemana'] = date("N", $timestamp);
                $timestamp += 86400;
                foreach($chats as $chat){
                    $l++;
                }
            }
            include 'lib/core/lhform/PHPExcel.php';
            function colorirCell($objPHPExcel, $coordX, $coordY, $cor,$styleArray){
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($coordX).$coordY)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($cor);
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($coordX).$coordY)->applyFromArray($styleArray);
            }
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $x = 12;
            $y = 7;
            $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).($y-2))->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, "Legenda");
            $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y+1, "Dia");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y+2, "Chats 12h-18h30 Atendentes");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y+3, "Chats 12h-18h30 Bot");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y+4, "Chats fora do horario");
            $objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($x))->setWidth(27);
            $objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($x))->setAutoSize(false);
            $styleArray = array(
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000'),
                    ),
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            );
            colorirCell($objPHPExcel, $x, $y, 'FFFFFF', $styleArray);
            colorirCell($objPHPExcel, $x, $y+1, 'FFE4E1', $styleArray);
            colorirCell($objPHPExcel, $x, $y+2, 'CCFF33', $styleArray);
            colorirCell($objPHPExcel, $x, $y+3, '5F9EA0', $styleArray);
            colorirCell($objPHPExcel, $x, $y+4, 'FF3B1F', $styleArray);
            for($x=1;$x<8;$x++){
                switch($x){
                    case 1:
                        $dia = 'D';
                    break;

                    case 2:
                    case 6:
                    case 7:
                        $dia = 'S';
                    break;

                    case 3:
                        $dia = 'T';
                    break;

                    case 4:
                    case 5:
                        $dia = 'Q';
                    break;
                }
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, 1, $dia);
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).'1')->getFont()->setBold(true);
                colorirCell($objPHPExcel, $x, 1, 'CFD5D3', $styleArray);
            }
            $x = 1;
            $y = 3;
            $numSemanas = 0;
            foreach($chatsDHorario as $c){ // 12h-18h30 Atendentes
                $cor = 'CCFF33'; // Verde marca texto
                if(($c['numDia']==2 || $c['numDia']==3) && $c['diaSemana']==1){ // Meses que começam no final de semana
                    switch($c['numDia']){
                        case 2:
                            if($y>3){
                                $x = 1;
                                $y += 5;
                            } 
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, (($c['numDia']-1)>10?'0'.($c['numDia']-1):($c['numDia']-1)).'/'.$c['numMes']);
                            colorirCell($objPHPExcel, $x, $y, 'FFE4E1' /* Rosa embaçado */,$styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->getFont()->setBold(true);
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y+1, 0);
                            colorirCell($objPHPExcel, $x, $y+1, $cor,$styleArray);
                            $x++;
                        break;

                        case 3:
                            $x = 7;
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, (($c['numDia']-2)>10?'0'.($c['numDia']-2):($c['numDia']-2)).'/'.$c['numMes']);
                            colorirCell($objPHPExcel, $x, $y, 'FFE4E1' /* Rosa embaçado */,$styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->getFont()->setBold(true);
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y+1, 0);
                            colorirCell($objPHPExcel, $x, $y+1, $cor,$styleArray);
                            $x = 1;
                            $y += 5;
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, (($c['numDia']-1)>10?'0'.($c['numDia']-1):($c['numDia']-1)).'/'.$c['numMes']);
                            colorirCell($objPHPExcel, $x, $y, 'FFE4E1' /* Rosa embaçado */,$styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->getFont()->setBold(true);
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y+1, 0);
                            colorirCell($objPHPExcel, $x, $y+1, $cor,$styleArray);
                            $x++;
                        break;
                    }
                }
                if($x==1 && (int)$c['diaSemana']!=7){
                    $x += (int)$c['diaSemana'];
                }
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, $c['numDia'].'/'.$c['numMes']);
                colorirCell($objPHPExcel, $x, $y, 'FFE4E1' /* Rosa embaçado */,$styleArray);
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y+1, count($c)-3);
                colorirCell($objPHPExcel, $x, $y+1, $cor,$styleArray);
                $x++;
                if($c['diaSemana']==5){
                    for($a=1;$a<3;$a++){
                        switch((int)$c['numMes']){
                            case 4:
                            case 6:
                            case 9:
                            case 11:
                                $numDiasMes = 30;
                            break;
                            case 2:
                                if(((int)$_GET['anoI']%4==0 && (int)$_GET['anoI']%100!=0) || ((int)$_GET['anoI']%400==0)){
                                    $numDiasMes = 29;
                                } else {
                                    $numDiasMes = 28;
                                }  
                            break;
                            default:
                                $numDiasMes = 31;
                            break;

                        }
                        if($c['numDia']+$a<=$numDiasMes){
                            if($a==2){
                                $y += 5;
                                $x = 1;
                            }
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, (($c['numDia']+$a)<10?'0'.($c['numDia']+$a):($c['numDia']+$a)).'/'.$c['numMes']);
                            colorirCell($objPHPExcel, $x, $y, 'FFE4E1' /* Rosa embaçado */,$styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->getFont()->setBold(true);
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y+1, 0);
                            colorirCell($objPHPExcel, $x, $y+1, $cor,$styleArray);
                            $x++;
                        }
                    }
                }
            }
            $x=1;
            $y=5;
            foreach($chatsDHorarioBot as $c){ // 12h-18h30 Bot
                $cor = '5F9EA0'; // Azul cadete
                if(($c['numDia']==2 || $c['numDia']==3) && $c['diaSemana']==1){ // Meses que começam no final de semana
                    switch($c['numDia']){
                        case 2:
                            if($y>5){
                                $x = 1;
                                $y += 5;
                            }     
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, 0);
                            colorirCell($objPHPExcel, $x, $y, $cor,$styleArray);
                            $x++;
                        break;
                        
                        case 3:
                            $x = 7;
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, 0);
                            colorirCell($objPHPExcel, $x, $y, $cor,$styleArray);
                            $x = 1;
                            $y += 5;
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, 0);
                            colorirCell($objPHPExcel, $x, $y, $cor,$styleArray);
                            $x++;
                        break;

                    }
                }
                if($x==1 && (int)$c['diaSemana']!=7){
                    $x += (int)$c['diaSemana'];
                }
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, count($c)-3);
                colorirCell($objPHPExcel, $x, $y, $cor,$styleArray);
                $x++;
                if($c['diaSemana']==5){
                    for($a=1;$a<3;$a++){
                        switch((int)$c['numMes']){
                            case 4:
                            case 6:
                            case 9:
                            case 11:
                                $numDiasMes = 30;
                            break;
                            case 2:
                                if(((int)$_GET['anoI']%4==0 && (int)$_GET['anoI']%100!=0) || ((int)$_GET['anoI']%400==0)){
                                    $numDiasMes = 29;
                                } else {
                                    $numDiasMes = 28;
                                }
                            break;
                            default:
                                $numDiasMes = 31;
                            break;

                        }
                        if($c['numDia']+$a<=$numDiasMes){
                            if($a==2){
                                $y += 5;
                                $x = 1;
                            }
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, 0);
                            colorirCell($objPHPExcel, $x, $y, $cor,$styleArray);
                            $x++;
                        }
                    } 
                } 
            }
            $x=1;
            $y=6;
            foreach($chatsFHorario as $c){
                $cor = 'FF3B1F'; // Vermelho
                if($x==1 && (int)$c['diaSemana']!=7){
                    $x += (int)$c['diaSemana'];
                }
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, count($c)-3);
                colorirCell($objPHPExcel, $x, $y, $cor,$styleArray);
                $x++;
                if((int)$c['diaSemana']==6){
                    $y += 5;
                    $x = 1;
                    $numSemanas++;
                } 
            }

            $x = 9;
            $y = 3;
            $a = 4;
            for($p=0;$p<=$numSemanas;$p++){
                $objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($x))->setWidth(15);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, "Semana ".($p+1));
                $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->getFont()->setBold(true);
                colorirCell($objPHPExcel, $x, $y, 'FFFFFF',$styleArray);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+1, $y, "=SUM(B".$a.":H".$a.")");
                colorirCell($objPHPExcel, $x+1, $y, 'CCFF33',$styleArray);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+1, $y+1, "=SUM(B".($a+1).":H".($a+1).")");
                colorirCell($objPHPExcel, $x+1, $y+1, '5F9EA0',$styleArray);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+1, $y+2, "=SUM(B".($a+2).":H".($a+2).")");
                colorirCell($objPHPExcel, $x+1, $y+2, 'FF3B1F',$styleArray);
                $y+=4;
                $a+=5;
            }

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, "Total");
            $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($x).$y)->getFont()->setBold(true);
            colorirCell($objPHPExcel, $x, $y, 'FFFFFF',$styleArray);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+1, $y, $k);
            colorirCell($objPHPExcel, $x+1, $y, 'CCFF33',$styleArray);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+1, $y+1, $j);
            colorirCell($objPHPExcel, $x+1, $y+1, '5F9EA0',$styleArray);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+1, $y+2, $l);
            colorirCell($objPHPExcel, $x+1, $y+2, 'FF3B1F',$styleArray);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+1, $y+3, ($k+$j+$l));
            colorirCell($objPHPExcel, $x+1, $y+3, 'FFFFFF',$styleArray);


            $objPHPExcel->getActiveSheet()->setTitle('ChatsPorHorario');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            try{
                $objWriter->save('extension/autoqueue/design/autoqueuetheme/tpl/lhstatistic/tabs/ChatsPorHorario.xls');  
            } catch (Exception $e){
                echo $e->getMessage();
            }
            ?>
            <a href="/chat/extension/autoqueue/design/autoqueuetheme/tpl/lhstatistic/tabs/ChatsPorHorario.xls" class="btn btn-secondary">Baixar Relatório de <?php $data->setDate($_GET['anoI'], $_GET['mesI'], 1); echo utf8_encode(ucwords(strftime('%B', $data->getTimestamp())))." de ".$_GET['anoI']; $data->setDate($_GET['anoF'], $_GET['mesF'], 1); echo (isset($_GET['anoF']) && isset($_GET['mesF']) && $_GET['anoF']!='' && $_GET['mesF']!='') ? ' a '.utf8_encode(ucwords(strftime('%B', $data->getTimestamp())))." de ".$_GET['anoF'] : '';?></a>
            <?php
        } else {
            ?>
            <div class="alert alert-info">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Please choose statistic parameters first!');?>
            </div>
            <?php
        }
    } else {
        ?>
        <div class="alert alert-info">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Please choose statistic parameters first!');?>
        </div>
        <?php
    }
}
?>