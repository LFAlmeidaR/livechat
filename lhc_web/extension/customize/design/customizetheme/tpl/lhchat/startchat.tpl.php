<?php include(erLhcoreClassDesign::designtpl('lhchat/startchat_pre.tpl.php')); ?>
<?php if ($chat_startchat_enabled == true) : ?>
    <?php if ($disabled_department === true) : ?>
        <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Department is disabled'); ?></h4>

    <?php elseif (isset($department_invalid) && $department_invalid === true) : ?>
        <?php $errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Please provide a department'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php')); ?>

    <?php else : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/widget_geo_adjustment.tpl.php')); ?>
        <?php if ($exitTemplate == true) {
            return;
        } ?>

        <?php if (isset($errors)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php')); ?>
        <?php endif; ?>

        <?php if ($leaveamessage == false || ($forceoffline === false && erLhcoreClassChat::isOnline($department, false, array('ignore_user_status' => (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value, 'online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout']))) === true) : ?>
            <?php
            $onlyBotOnline = erLhcoreClassChat::isOnlyBotOnline($department);
            ?>

            <?php if (isset($theme) && $theme !== false && isset($theme->bot_configuration_array['custom_html']) && !empty($theme->bot_configuration_array['custom_html']) && $onlyBotOnline == false) : ?>
                <?php echo $theme->bot_configuration_array['custom_html'] ?>
            <?php elseif (isset($theme) && $theme !== false && isset($theme->bot_configuration_array['custom_html_bot']) && !empty($theme->bot_configuration_array['custom_html_bot']) && $onlyBotOnline == true) : ?>
                <?php echo $theme->bot_configuration_array['custom_html_bot'] ?>
            <?php elseif (isset($theme) && $theme !== false && isset($theme->bot_configuration_array['trigger_id']) && !empty($theme->bot_configuration_array['trigger_id']) && $theme->bot_configuration_array['trigger_id'] > 0) :  ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/part/render_intro.tpl.php')); ?>
            <?php else : ?>
                <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Fill out this form to start a chat'); ?></h4>
            <?php endif; ?>

            <form id="form-start-chat" method="post" action="<?php echo erLhcoreClassDesign::baseurl('chat/startchat') ?><?php $department !== false ? print '/(department)/' . $department : '' ?><?php $input_data->priority !== false ? print '/(priority)/' . $input_data->priority : '' ?><?php $input_data->vid !== false ? print '/(vid)/' . htmlspecialchars($input_data->vid) : '' ?><?php $input_data->hash_resume !== false ? print '/(hash_resume)/' . htmlspecialchars($input_data->hash_resume) : '' ?><?php echo $append_mode_theme ?>" onsubmit="return lhinst.addCaptcha('<?php echo time() ?>',$(this))">


                <?php $adminCustomFieldsMode = 'on'; ?>
                <!-- CAMPO CPF -->
                <?php include(erLhcoreClassDesign::designtpl('lhchat/part/admin_form_variables.tpl.php')); ?>

                <?php $formResubmitId = 'form-start-chat'; ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/part/auto_resubmit.tpl.php')); ?>

                <input type="hidden" name="onlyBotOnline" value="<?php echo $onlyBotOnline == true ? 1 : 0 ?>">


                <!-- CAMPO NOME -->
                <?php if (isset($start_data_fields['name_visible_in_popup']) && $start_data_fields['name_visible_in_popup'] == true) : ?>
                    <?php if (isset($start_data_fields['name_hidden']) && $start_data_fields['name_hidden'] == true) : ?>
                        <input type="hidden" name="Username" value="<?php echo htmlspecialchars($input_data->username); ?>" />
                    <?php else : ?>
                        <?php if (in_array('username', $input_data->hattr)) : ?>
                            <input class="form-control form-control-sm<?php if (isset($errors['nick'])) :
                                                                        ?> is-invalid<?php
                                                                                    endif; ?>" type="hidden" name="Username" value="<?php echo htmlspecialchars($input_data->username); ?>" />
                        <?php elseif (!($onlyBotOnline == true && isset($start_data_fields['name_hidden_bot']) && $start_data_fields['name_hidden_bot'] == true)) : ?>
                            <div class="form-group">
                                <!-- Titulo do Input -->
                                <label class="col-form-label">
                                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', /*'Name'*/ 'Nome Completo'); ?>
                                    <?php if (isset($start_data_fields['name_require_option']) && $start_data_fields['name_require_option'] == 'required') :
                                    ?>*<?php
                                    endif; ?></label>

                                <!-- Campo de digitação do Input -->
                                <!-- O maxlength foi alterado de 100 para 60 -->
                                <input maxlength="60" <?php if (isset($start_data_fields['name_require_option']) && $start_data_fields['name_require_option'] == 'required') :
                                                        ?>aria-required="true" required <?php endif; ?> aria-label="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Enter your full name'); ?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Enter your full name'); ?>" class="form-control form-control-sm<?php if (isset($errors['nick'])) :
                                                                                                                                                                                                                                                                                                                                                                                                                        ?> is-invalid
                            <?php endif; ?>" type="text" id="myInputUsername" name="Username" value="<?php echo htmlspecialchars($input_data->username); ?>" />
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- CAMPO E-MAIL -->
                <?php if (isset($start_data_fields['email_visible_in_popup']) && $start_data_fields['email_visible_in_popup'] == true) : ?>
                    <?php if (isset($start_data_fields['email_hidden']) && $start_data_fields['email_hidden'] == true) : ?>
                        <input type="hidden" name="Email" value="<?php echo htmlspecialchars($input_data->email); ?>" />
                    <?php else : ?>
                        <?php if (in_array('email', $input_data->hattr)) : ?>
                            <input class="form-control" type="hidden" name="Email" value="<?php echo htmlspecialchars($input_data->email); ?>" />
                        <?php elseif (!($onlyBotOnline == true && isset($start_data_fields['email_hidden_bot']) && $start_data_fields['email_hidden_bot'] == true)) : ?>
                            <div class="form-group">
                                <!-- Titulo do Input -->
                                <label class="col-form-label">
                                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'E-mail'); ?>
                                    <?php if (isset($start_data_fields['email_require_option']) && $start_data_fields['email_require_option'] == 'required') :
                                    ?>*<?php
                                    endif; ?></label>

                                <!-- Campo de digitação do Input -->
                                <input autofocus="autofocus" <?php if (isset($start_data_fields['email_require_option']) && $start_data_fields['email_require_option'] == 'required') :
                                                                ?>aria-required="true" required<?php
                                                                                            endif; ?> aria-label="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Enter your email address') ?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Enter your email address') ?>" class="form-control form-control-sm<?php if (isset($errors['email'])) :
                                                                                                                                                                                                                                                                                                                                                                                                ?> is-invalid<?php
                                                                                                                                                                                                                                                                                                                                                                                                endif; ?>" type="text" name="Email" id="myInputEmail" maxlength="60" size='65' value="<?php echo htmlspecialchars($input_data->email); ?>" />
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- CAMPO TELEFONE -->
                <?php if (isset($start_data_fields['phone_visible_in_popup']) && $start_data_fields['phone_visible_in_popup'] == true) : ?>
                    <?php if (isset($start_data_fields['phone_hidden']) && $start_data_fields['phone_hidden'] == true) : ?>
                        <input type="hidden" name="Phone" value="<?php echo htmlspecialchars($input_data->phone); ?>" />
                    <?php else : ?>
                        <?php if (in_array('phone', $input_data->hattr)) : ?>
                            <input class="form-control" type="hidden" name="Phone" value="<?php echo htmlspecialchars($input_data->phone); ?>" />
                        <?php elseif (!($onlyBotOnline == true && isset($start_data_fields['phone_hidden_bot']) && $start_data_fields['phone_hidden_bot'] == true)) : ?>
                            <div class="form-group">
                                <label class="col-form-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Phone'); ?><?php if (isset($start_data_fields['phone_require_option']) && $start_data_fields['phone_require_option'] == 'required') :
                                                                                                                                                                        ?>*<?php
                                                                                                                                                                        endif; ?></label>
                                <input autofocus="autofocus" <?php if (isset($start_data_fields['phone_require_option']) && $start_data_fields['phone_require_option'] == 'required') :
                                                                ?>aria-required="true" required<?php
                                                                                            endif; ?> aria-label="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Enter your phone') ?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Enter your phone') ?>" class="form-control<?php if (isset($errors['phone'])) :
                                                                                                                                                                                                                                                                                                                                                                ?> is-invalid<?php
                                                                                                                                                                                                                                                                                                                                                                endif; ?>" type="text" id="myInputPhone" name="Phone" value="<?php echo htmlspecialchars($input_data->phone); ?>" maxlength="13" />
                            </div>


                            <script>
                                /**
                                 * VALIDA SOMENTE A ENTRADA DE LETRAS NO CAMPO NOME
                                 */
                                var nome = document.getElementById("myInputUsername").addEventListener("keypress", checkName, false);

                                function checkName(evt) {
                                    var charCode = evt.charCode;
                                    if (charCode != 0) {
                                        if (charCode >= 33 && charCode <= 64 ||
                                            charCode >= 91 && charCode <= 96 ||
                                            charCode >= 123 && charCode <= 126) {

                                            evt.preventDefault();
                                            alert("Por favor, use apenas letras." +
                                                "\n" +
                                                "charCode: " + charCode + "\n");
                                            return false;
                                        } else {
                                            return true;
                                        }
                                    }
                                }
                            </script>
                            <script>
                                /**
                                 * CHAMA A FUNÇÃO QUE VALIDA O CAMPO DE E-MAIL
                                 */
                                var email = document.getElementById("myInputEmail");

                                email.onblur = function checkEmail() {
                                    validacaoEmail(email); // chamando a função.
                                }
                            </script>
                            <script>
                                /**
                                 * validacaoEmail -> [explicação de como funciona a função está no link: 
                                 *                      https://www.devmedia.com.br/validando-e-mail-em-inputs-html-com-javascript/26427]
                                 *
                                 * @param   field    [ Recebe um parametro]
                                 *
                                 * @return  [type = boolean]   [return = true - O codigo da proceguimento
                                 *                                       false - alert]
                                 */
                                function validacaoEmail(field) {
                                    usuario = field.value.substring(0, field.value.indexOf("@")); // usuario= antes do @
                                    dominio = field.value.substring(field.value.indexOf("@") + 1, field.value.length); // domino= depois do @

                                    if ((usuario.length >= 1) && // Tamanho de usuário maior ou igual a 1 caracter.
                                        (dominio.length >= 3) && // Tamanho do domínio maior ou igual a 3 caracteres.
                                        (usuario.search("@") == -1) && // Usuário não pode conter o @.
                                        (dominio.search("@") == -1) && // Domínio não pode conter o @.
                                        (usuario.search(" ") == -1) && // Usuário não pode conter o “ ” espaço em branco.
                                        (dominio.search(" ") == -1) && // Domínio não pode conter o “ ” espaço em branco.
                                        (dominio.search(".") != -1) && // Domínio tem que possuir “.” Ponto.
                                        (dominio.indexOf(".") >= 1) && // A posição do primeiro ponto tem que ser maior ou igual a 1, lembrando a posição 0 deve ser ocupado por algum caracter após o @.
                                        (dominio.lastIndexOf(".") < dominio.length - 1)) { // A posição do ultimo ponto tem que ser menor que o ultimo caracter, deve ser finalizado o domínio por um caracter.

                                        return true;
                                    } else {

                                        alert("'" + field.value + "'" +
                                            "\n" +
                                            "E-mail informado é Inválido!" +
                                            "\n" +
                                            "Por favor digite seu e-mail novamente.");
                                        field.value = ("");
                                        return false;
                                    }
                                }
                            </script>
                            <script>
                                /**
                                 * MASCARA DO TELEFONE
                                 */
                                var telefone = document.getElementById("myInputPhone");
                                telefone.oninput = function() {
                                    v = telefone.value;
                                    //Remove tudo o que não é dígito
                                    v = v.replace(/\D/g, "");
                                    //Coloca um hífen entre o segundo e o terceiro dígitos
                                    v = v.replace(/(\d{2})(\d)/, "$1-$2");

                                    if (v.length <= 11) { //Fixo
                                        //Coloca um hífen entre o quinto e o sexto dígitos
                                        v = v.replace(/(\d{4})(\d)/, "$1-$2");
                                    } else { //Celular
                                        //Coloca um hífen entre o sexto e o setimo dígitos
                                        v = v.replace(/(\d{5})(\d)/, "$1-$2");
                                    }
                                    telefone.value = v;
                                }
                            </script>

                        <?php endif; ?>
                    <?php endif; ?>

                <?php endif; ?>


                <?php if (isset($start_data_fields['message_visible_in_popup']) && $start_data_fields['message_visible_in_popup'] == true) : ?>
                    <?php if (isset($start_data_fields['message_hidden']) && $start_data_fields['message_hidden'] == true) : ?>
                        <textarea class="hide" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Enter your message'); ?>" name="Question"><?php echo htmlspecialchars($input_data->question); ?></textarea>
                    <?php elseif (!($onlyBotOnline == true && isset($start_data_fields['message_hidden_bot']) && $start_data_fields['message_hidden_bot'] == true)) : ?>
                        <div class="form-group">
                            <label class="col-form-label">
                                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Your question'); ?>
                                <?php if (isset($start_data_fields['message_require_option']) && $start_data_fields['message_require_option'] == 'required') :
                                ?>*<?php
                                endif; ?></label>
                            <textarea autofocus="autofocus" <?php if (isset($start_data_fields['message_require_option']) && $start_data_fields['message_require_option'] == 'required') :
                                                            ?>aria-required="true" required<?php
                                                                                        endif; ?> aria-label="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Enter your message'); ?>" class="form-control form-control-sm<?php if (isset($errors['question'])) :
                                                                                                                                                                                                                                            ?> is-invalid<?php
                                                                                                                                                                                                                                            endif; ?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Enter your message'); ?>" name="Question"><?php echo htmlspecialchars($input_data->question); ?></textarea>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/part/user_variables.tpl.php')); ?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/part/user_timezone.tpl.php')); ?>

                <?php if ($department === false) : ?>
                    <?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/department.tpl.php')); ?>
                <?php endif; ?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/part/product.tpl.php')); ?>

                <?php $tosVariable = 'tos_visible_in_popup';
                $tosCheckedVariable = 'tos_checked_online'; ?>
                <?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/accept_tos.tpl.php')); ?>

                <div class="btn-group" role="group" aria-label="...">
                    <?php $startChatText = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Start chat'); ?>
                    <?php if (isset($theme) && $theme !== false && isset($theme->bot_configuration_array['custom_start_button']) && !empty($theme->bot_configuration_array['custom_start_button']) && $onlyBotOnline == false) {
                        $startChatText = htmlspecialchars($theme->bot_configuration_array['custom_start_button']);
                    } elseif (isset($theme) && $theme !== false && isset($theme->bot_configuration_array['custom_start_button_bot']) && !empty($theme->bot_configuration_array['custom_start_button_bot']) && $onlyBotOnline == true) {
                        $startChatText = htmlspecialchars($theme->bot_configuration_array['custom_start_button_bot']);
                    } ?>

                    <input type="submit" class="btn btn-primary btn-sm startchat" value="<?php echo $startChatText; ?>" name="StartChatAction" />

                    <?php include(erLhcoreClassDesign::designtpl('lhchat/startchat_button_multiinclude.tpl.php')); ?>
                    <?php if (erLhcoreClassModelChatConfig::fetch('reopen_chat_enabled')->current_value == 1 && ($reopenData = erLhcoreClassChat::canReopenDirectly(array('reopen_closed' => erLhcoreClassModelChatConfig::fetch('allow_reopen_closed')->current_value))) !== false) : ?>
                        <input type="button" class="btn btn-secondary btn-sm resumechat" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatnotexists', 'Resume chat'); ?>" onclick="document.location.replace('<?php echo erLhcoreClassDesign::baseurl('chat/reopen') ?>/
                <?php echo $reopenData['id'] ?>/<?php echo $reopenData['hash'] ?>
                <?php if (isset($modeAppend) && $modeAppend != '') :
                ?>/(embedmode)/embed<?php
                                endif; ?>')">
                    <?php endif; ?>
                </div>

                <input type="hidden" value="<?php echo htmlspecialchars($referer); ?> " name="URLRefer" />
                <input type="hidden" value="<?php echo htmlspecialchars($referer_site); ?> " name="r" />
                <input type="hidden" value="<?php echo htmlspecialchars($input_data->operator); ?> " name="operator" />
                <input type="hidden" value="1" name="StartChat" />

                <?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/switch_to_offline.tpl.php')); ?>

            </form>
        <?php else : ?>
            <h4>
                <?php if (isset($theme) && $theme !== false && $theme->noonline_operators_offline) : ?>
                    <?php echo htmlspecialchars($theme->noonline_operators_offline) ?>
                <?php else : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'There are no online operators at the moment, please leave your message') ?>
                <?php endif; ?>
            </h4>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/offline_form_startchat.tpl.php')); ?>

        <?php endif; ?>

    <?php endif; ?>

<?php endif; ?>