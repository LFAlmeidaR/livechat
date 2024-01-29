<?php
if (isset($start_data_fields['custom_fields']) && $start_data_fields['custom_fields'] != '') :
    $customAdminfields = json_decode($start_data_fields['custom_fields'], true);
    if (is_array($customAdminfields)) : ?>
<div class="row">
        <?php foreach ($customAdminfields as $key => $adminField) :
            if ($adminField['visibility'] == 'all' || $adminCustomFieldsMode == $adminField['visibility']) : ?>        
                            <?php if ($adminField['fieldtype'] == 'hidden' || (isset($input_data->via_hidden[$key]) && $input_data->via_hidden[$key] == 't')) : ?>
                                <?php if (isset($input_data->via_hidden[$key]) && $input_data->via_hidden[$key] == 't') : ?>
            <input class="form-control" type="hidden" name="via_hidden[<?php echo $key?>]" value="t" />
                                <?php endif;?>
        
                                <?php if (isset($input_data->via_encrypted[$key]) && $input_data->via_encrypted[$key] == 't') : ?>
            <input class="form-control" type="hidden" name="via_encrypted[<?php echo $key?>]" value="t" />
                                <?php endif;?>
        
            <input class="form-control" type="hidden" name="value_items_admin[<?php echo $key?>]" value="<?php isset($input_data->value_items_admin[$key]) ? print htmlspecialchars($input_data->value_items_admin[$key]) : print htmlspecialchars($adminField['defaultvalue'])?>" />
                            <?php elseif ($adminField['fieldtype'] == 'dropdown') : ?>
                                <?php if (!isset($adminField['showcondition']) || $adminField['showcondition'] == 'always' || ($adminField['showcondition'] == 'uempty' && ($input_data->username == '' || isset($_POST['show_admin_item'][$key])))) : ?>
            <input type="hidden" name="show_admin_item[<?php echo $key?>]" value="true" />
            <div class="col-<?php echo htmlspecialchars($adminField['size'])?>">
                <!--
                Campo FORMA DE TRATANTO - formulario Inicial (startchat)
                -->                  
                <div class="form-group">
                    <label class="col-form-label" id="label-<?php echo htmlspecialchars('additional_admin_' . $key)?>"><?php echo htmlspecialchars($adminField['fieldname'])?><?php $adminField['isrequired'] == 'true' ? print '*' : ''?></label>
                    <select name="value_items_admin[<?php echo $key?>]" class="form-control form-control-sm<?php if (isset($errors['additional_admin_' . $key])) :
                        ?> is-invalid<?php
                                                    endif;?>">
                        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel', 'Please choose');?></option>
                                    <?php foreach (explode("\n", $adminField['options']) as $option) : ?>
                                        <option <?php isset($input_data->value_items_admin[$key]) &&
                                        $option == $input_data->value_items_admin[$key] ? print 'selected="selected"' : ''?>
                                        value="<?php echo htmlspecialchars($option)?>"><?php echo htmlspecialchars($option)?></option>
                                    <?php endforeach; ?>
                    </select>
                </div>
            </div>
                                <?php endif; ?>
                            <?php else :
                                $hasExtraField = true; ?>
                                <?php if (!isset($adminField['showcondition']) || $adminField['showcondition'] == 'always' || ($adminField['showcondition'] == 'uempty' && ($input_data->username == '' || isset($_POST['show_admin_item'][$key])))) : ?>
            <input type="hidden" name="show_admin_item[<?php echo $key?>]" value="true" />
            <div class="col-<?php echo htmlspecialchars($adminField['size'])?>">
            
                <!--
                Campo CPF - formulario Inicial (startchat)
                -->
                <div class="form-group">
                    <label class="col-form-label" id="label-<?php echo htmlspecialchars('additional_admin_' . $key)?>"><?php echo htmlspecialchars($adminField['fieldname'])?><?php $adminField['isrequired'] == 'true' ? print '*' : ''?></label>
                    <input class="form-control form-control-sm<?php if (isset($errors['additional_admin_' . $key])) :
                        ?> is-invalid<?php
                                                              endif;?>" aria-labelledby="label-<?php echo htmlspecialchars('additional_admin_' . $key)?>" type="text" id="myInputCPF"name="value_items_admin[<?php echo $key?>]" <?php $adminField['isrequired'] == 'true' ? print 'aria-required="true" required' : ''?> value="<?php isset($input_data->value_items_admin[$key]) ? print htmlspecialchars($input_data->value_items_admin[$key]) : print htmlspecialchars($adminField['defaultvalue'])?>" <?php echo ($adminField['fieldidentifier'] == 'cpf') ? "placeholder=\"Informe o seu CPF\" maxlength=\"14\" minlength=\"11\" autofocus=\"autofocus\" " : '';?> />
                </div>       
                                   
                <script> 
                    /**
                     *  VALIDA SOMENTE A ENTRADA DE NUMEROS NO CAMPO CPF 
                    */  
                    var cpf = document.getElementById("myInputCPF")
                        .addEventListener("keypress", checkCPF, false); //verifica as teclas digitadas
              
                        function checkCPF(evt) {
                            var charCode = evt.charCode;   
                            if (charCode >= 48 && charCode <= 57){ //compara as teclas digitadas com seu charcode                 
                                return true; //se for apenas numero ele continua a execução.
                            }else{
                                evt.preventDefault();
                                alert( "Por favor, use apenas números."
                                    + "\n" + "charCode: "+ charCode+ "\n");
                                return false;
                            }                  
                        }
                </script> 
                <script> 
                    /**
                     *  CHAMA A FUNÇÃO QUE VALIDA O CPF DIGITADO E APLICA A MASCARA AO CPF
                    */ 
                    var cpf = document.getElementById("myInputCPF");
              
                        cpf.oninput = function validandocpf (){ 
                            validar (cpf);
                        }
                </script>
                <script>
                    /**
                     * validar -> [chama a função que faz o calculo do CPF, confere se os digitos são iguais e mostra a mensagem de erro]
                     *
                     * @param   obj    [ recebe um objeto]
                     *
                     * @return  [type = boolean] [1ºfalse = alert
                     *                            2ºfalse = alert
                     *                            true = retorna o CPF mascarado.]
                    */
                    function validar (obj){
                        var cpf = (obj.value).replace(/\D/g, ''); //Remove tudo o que não é dígito
                        var tam = (cpf).length;
                        
                        if (tam == 11) {
                            if (cpf === "11111111111" || cpf === "22222222222" || 
                                cpf === "33333333333" || cpf === "44444444444" || 
                                cpf === "55555555555" || cpf === "66666666666" || 
                                cpf === "77777777777" || cpf === "88888888888" || 
                                cpf === "99999999999" || cpf === "00000000000") {
                                    alert("'" + cpf + "'"
                                        + "\n"
                                        + "É um CPF Inválido"); // se quiser mostrar o erro
                                    obj.select(); // se quiser selecionar o campo em questão
                                    obj.focus(); // se quiser destacar um elemento
                                    obj.value = (""); // limpa o campo de CPF apos o o alert
                                return false;
                            }else if (!validaCPF(cpf)) { // chama a função que valida o CPF
                                    alert("'" + cpf + "'"
                                        + "\n"
                                        + "CPF informado é Inválido!"
                                        + "\n"
                                        + "Por favor digite seu CPF novamente."); // se quiser mostrar o erro
                                    obj.select(); // se quiser selecionar o campo em questão
                                    obj.focus(); // se quiser destacar um elemento
                                    obj.value = (""); // limpa o campo de CPF apos o o alert
                                return false;
                            }else{ // se validou o CPF mascaramos corretamente                          
                                    obj.value = maskCPF(cpf); 
                                    return true;
                                }
                        }
                    }
                </script>
                <script>
                    /**
                     * validaCPF -> [calcula o primeiro dígito verificador a partir dos 9 primeiros dígitos do CPF, 
                     *      e em seguida, calcula o segundo dígito verificador a partir dos 9 (nove) primeiros dígitos do CPF, 
                     *      mais o primeiro dígito, obtido na primeira parte.]
                     *
                     * @param   s    [ recebe um objeto]
                     *
                     * @return  [type = boolean] [return description]
                    */
                    function validaCPF(s) {
                        var c = s.substr(0, 9);
                        var dv = s.substr(9, 2);
                        var d1 = 0;
                        for (var i = 0; i < 9; i++) {
                            d1 += c.charAt(i) * (10 - i);
                        }
                        if (d1 == 0)
                            return false;
                        d1 = 11 - (d1 % 11);
                        if (d1 > 9)
                            d1 = 0;
                        if (dv.charAt(0) != d1) {
                            return false;
                        }
                        d1 *= 2;
                        for (var i = 0; i < 9; i++) {
                            d1 += c.charAt(i) * (11 - i);
                        }
                        d1 = 11 - (d1 % 11);
                        if (d1 > 9)
                            d1 = 0;
                        if (dv.charAt(1) != d1) {
                            return false;
                        }
                        return true;
                    }
                </script>
                <script>
                    /**
                     * maskCPF -> [Implementa a mascara de CPF: 000.000.000-00]
                     *
                     * @param   CPF    [ Recebe um parametro]
                     * @metodo substring() [retorna a parte da string entre os índices inicial e final, ou até o final da string.]
                     * @return  [type = string ] [retorna o cpf com sua mascara através do metodo substring.]
                    */
                    function maskCPF(CPF) {
                        return CPF.substring(0, 3) + "." 
                        + CPF.substring(3, 6) + "." 
                        + CPF.substring(6, 9) + "-" 
                        + CPF.substring(9, 11);
                    }

                    /** 
                     * CODIGO ANTIGO: APLICA A MASCARA AO CPF DIGITADO 
                     
                        var cpf = document.querySelector('input[name="value_items_admin[0]"]');
                        cpf.oninput =  function (){       
                            v = cpf.value;
                            v = v.replace(/\D/g, "");  //Remove tudo o que não é dígito
                            if (v.length <= 11) { //Cpf
                                v = v.replace(/(\d{3})(\d)/, "$1.$2");
                                v = v.replace(/(\d{3})(\d)/, "$1.$2");
                                v = v.replace(/(\d{3})(\d)/, "$1-$2");
                            } 
                        }
                    */
                </script>
            </div>
                                <?php endif; ?>
                            <?php endif;
            endif;
        endforeach;?>
</div>
    <?php endif;
endif;?>


