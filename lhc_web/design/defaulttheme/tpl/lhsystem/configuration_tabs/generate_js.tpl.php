<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs/generate_js_pre.tpl.php'));?>
<?php if ($system_configuration_tabs_generate_js_enabled == true && $currentUser->hasAccessTo('lhsystem','generate_js_tab')) : ?>
<li role="presentation" class="nav-item"><a href="#embed" class="nav-link" aria-controls="embed" role="tab" data-bs-toggle="tab"><?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_titles/embed_code_title.tpl.php'));?></a></li>
<?php endif; ?>