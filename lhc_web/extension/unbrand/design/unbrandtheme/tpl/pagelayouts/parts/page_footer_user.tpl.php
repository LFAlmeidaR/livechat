<meta charset="utf-8">

<div class="mt-1 p-1 border-top">
    <p class="float-end small mb-0"><a target="_blank" href="http://www.tjdft.jus.br">Tribunal de Justiça do Distrito Federal e Territórios &copy; <?php echo date('Y')?></a></p>
	<p class="small mb-0"><a href="<?php echo erLhcoreClassModelChatConfig::fetch('customer_site_url')->current_value ?>">	<?php echo htmlspecialchars(erLhcoreClassModelChatConfig::fetch('customer_company_name')->current_value) ?></a></p></div>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer_js.tpl.php')); ?>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer_js_extension_multiinclude.tpl.php')); ?>