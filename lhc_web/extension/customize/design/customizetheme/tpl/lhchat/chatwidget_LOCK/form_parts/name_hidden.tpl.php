<div class="col-6 form-group<?php if (isset($errors['nick'])) :
                            ?> is-invalid<?php
                                        endif; ?>">
    <label class="col-form-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Nome Completo'); ?>
        <?php if (isset($start_data_fields['name_require_option']) && $start_data_fields['name_require_option'] == 'required') :
        ?>*<?php
        endif; ?></label>
    <input maxlength="60" type="text" id="myInputUsername" <?php if (isset($start_data_fields['name_require_option']) && $start_data_fields['name_require_option'] == 'required') :
                                                            ?>aria-required="true" required<?php
                                                                                        endif; ?> aria-label="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Informe seu Nome Completo'); ?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Informe seu Nome Completo'); ?>" class="form-control form-control-sm" name="Username" value="<?php echo htmlspecialchars($input_data->username); ?>" />

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




</div>