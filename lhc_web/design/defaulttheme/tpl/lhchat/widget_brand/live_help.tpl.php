<?php $iconsStatuses['copyright_image_url'] = ($theme !== false && $theme->copyright_image_url !== false && strpos($theme->copyright_image_url, 'http') !== false);          	          	
if ($theme === false || $theme->show_copyright == 1) : ?><span id="lhc_title"><a title="<?php $theme !== false && $theme->widget_copyright_url != '' ? print '' : print 'Powered by Live Helper Chat' ?>" href="<?php $theme !== false && $theme->widget_copyright_url != '' ? print htmlspecialchars($theme->widget_copyright_url) : print 'http://livehelperchat.com' ?>" target="_blank"><img src="<?php if ($theme !== false && $theme->copyright_image_url !== false) : print $theme->copyright_image_url; else : ?><?php echo erLhcoreClassSystem::getHost()?><?php echo erLhcoreClassDesign::design('images/general/logo_grey.png');?><?php endif;?>" alt="Live Helper Chat" /></a></span><?php endif;?>