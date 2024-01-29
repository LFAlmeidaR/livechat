
<!-- Conteudo da aba "Alerta Geral"-->
<div role="tabpanel" class="tab-pane" id="alerta_geral">
    <form action="<?php echo erLhcoreClassDesign::baseurl('user/alertageral');?>" method="post" autocomplete="off">
        <div class="form-group">
            <label>Digite a mensagem:</label>
            <input type="text" name="Mensagem" id="Msg1" class="form-control" placeholder="Mensagem a ser enviada"/>
        </div>


        <div class="form-group">
            <label>Selecione o(s) departamento(s): </label><br>
            <label>
                <input type="checkbox" name="UserDepartment[]" value="0" onclick="disableCheckBox()" id="Todos">Todos</label><br>
            <?php foreach (erLhcoreClassDepartament::getDepartaments() as $department) : ?>
                <label><input type="checkbox" name="UserDepartment[]" class="Deps" value="<?php echo $department['id']?>" <?php echo in_array($department['id'],$userDepartments) ? 'checked="checked"' : '';?> /><?php echo htmlspecialchars($department['name'])?></label><br>
        	<?php endforeach; ?>
        </div>

        <input type="submit" class="btn btn-secondary" name="MandarAlertaGeral" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Send'); ?>" />
    </form>
</div>

<!-- Desabilita/habilita as outras checkboxes quando a checkbox "Todos" é marcada/desmarcada -->
<script type="text/javascript">
    function disableCheckBox(){
        if($("#Todos").prop("checked")){
            $("input.Deps").prop("disabled", true);
            $("input.Deps").prop("checked", false);
        } else {
            $("input.Deps").prop("disabled", false);
        }
    }
</script>


