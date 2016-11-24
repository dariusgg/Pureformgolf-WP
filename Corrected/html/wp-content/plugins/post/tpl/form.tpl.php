<?php
global $showOn, $displayTypes, $avServices, $orientationType, $positionType;
?>
<link rel="stylesheet" type="text/css" href="<?php print $plugin_location;?>style.css">

<div class='post-form'>
    <script type="text/javascript">
          var _gaq = _gaq || [];
          _gaq.push(["_setAccount", "UA-33664506-1"]);
          _gaq.push(["_trackPageview"]);
          (function() {
            var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
            ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
            var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
          })();
    </script>

    <h1><a href="http://po.st" class="post-home"></a> <?php _e('Po.st options', 'po.st');?></h1>

    <form action='' method='post' id='post_form'>
        <?php wp_nonce_field( 'update-po.st-settings' ); ?>

		<div class='post-form__pubkey_error'><?php _e('Mandatory "Publisher Key" field cannot be left blank.', 'po.st');?></div>

        <h2 class="post-form__heading">
            <strong><?php _e('Publisher Key', 'po.st');?></strong> (mandatory)
        </h2>

        <div class="post-form__pubkey">
            <input type='text' name='p_key' id='p_key' value='<?php print $p_key?>' class="i-p" />
        </div>

        <div class="post-form__pubkey-content">
            <?php _e('<p>A publisher key is mandatory. The plugin will not work without it. If you don\'t have this key yet, <a href="http://www.po.st/portal/register" target="_blank">register at Po.st</a>. It will only take a minute.</p><p>If you already have a Po.st account, you can find your publisher key by <a href="http://www.po.st/portal/dashboard" target="_blank">signing in to your dashboard</a> and clicking on Account at the top.</p>', 'po.st');?>
        </div>

        <h2 class="post-form__heading">
            <strong><?php _e('Show po.st widget on', 'po.st');?></strong>
        </h2>

        <div class="post-form__showwidgets">
            <ul>
                <?php foreach($showOn as $k=>$name):?>
                <li>
                    <input type='checkbox' id='show_on_<?php print $k;?>' name='show_on[<?php print $k;?>]' value='1' <?php if (in_array($k, $display_pages))print 'checked';?>/>
                    <label for='show_on_<?php print $k;?>'>
                        <?php print $name;?>
                    </label>
                </li>
                <?php endforeach?>
            </ul>
        </div>

        <div class="post-wizard post-wizard_<?php print $design_orientation?>">
            <h1>
                <?php _e('Design settings', 'po.st')?>
                <small class="type-wizard">
                    <a href="" class="type-wizard__link"><?php $design_custom_code_on?_e('Step by Step Wizard','po.st'):_e('Use Custom Code','po.st');?></a>
                </small>
                <input type='hidden' name='design_custom_code_on' value='<?php print $design_custom_code_on?>'/>
            </h1>

            <div class="wizard-pane" style='<?php if ($design_custom_code_on) print "display: none;"?>'>
                <h2><strong><?php _e('Orientation', 'po.st');?></strong></h2>

                <ul class="post-wizard__orientation">
                    <?php foreach ($orientationType as $type => $data):?>
                    <li>
                        <input type='radio' id='orientation-<?php print $type?>' name='design_orientation' value='<?php print $type?>' <?php if ($design_orientation == $type)print 'checked="checked"';?>/>
                        <label for='orientation-<?php print $type?>' class="post-wizard__orientation__item<?php if ($design_orientation == $type)print ' selected';?>">
                            <i class="post-wizard__orientation__ico <?php print $type?>"></i>
                            <span class="post-wizard__orientation__item__inner"><?php print $data['label']?></span>
                        </label>
                    </li>
                    <?php endforeach?>
                </ul>

                <h2><strong><?php _e('Style', 'po.st')?></strong></h2>
                <input type='hidden' id="total-type" name='design_totaltype' value='<?php print $design_totaltype?>' />
                <ul class="post-wizard__style">
                    <?php foreach ($displayTypes as $type => $set): ?>
                    <li class="post-wizard__style__item post-wizard__style__item_<?php print $type?> <?php if ($type == $design_type) print 'selected';?>">
                        <label class="figure" for='design_type_<?php print $type?>'>
                            <span class="figure__inner">
                                <span class="figure__vis">
                                    <i class="l1"></i>
                                    <i class="l2"></i>
                                    <i class="l3"></i>
                                    <i class="l4"></i>
                                    <i class="l5"></i>
                                </span>
                            </span>
                        </label>
                        <input type='radio' id='design_type_<?php print $type?>' name='design_type' value='<?php print $type?>' data-type="<?php print $set['showtype'];?>" <?php if ($type == $design_type) print 'checked="checked"';?> />
                        <div class="title"><?php print $set['label'];?></div>
                    </li>
                    <?php endforeach;?>
                </ul>

                <h2><strong><?php _e('Position', 'po.st')?></strong></h2>
                <ul class="post-wizard__position post-wizard__position__horizontal">
                    <?php foreach ($positionType['horizontal'] as $type => $data):?>
                    <li>
                        <input type='checkbox' id='position-horizontal-<?php print $type?>' name='display_position_horizontal[]' value='<?php print $type?>' <?php if ((is_array($display_position_horizontal) && in_array($type, $display_position_horizontal)) || ($display_position_horizontal == $type))print 'checked="checked"';?>/>
                        <label for='position-horizontal-<?php print $type?>' class="post-wizard__position__item<?php if ((is_array($display_position_horizontal) && in_array($type, $display_position_horizontal)) || ($display_position_horizontal == $type))print ' selected';?>">
                            <i class="post-wizard__position__ico <?php print $type?>"></i>
                            <span class="post-wizard__position__item__inner"><?php print $data['label']?></span>
                        </label>
                    </li>
                    <?php endforeach?>
                </ul>
                <ul class="post-wizard__position post-wizard__position__vertical">
                    <?php foreach ($positionType['vertical'] as $type => $data):?>
                    <li>
                        <input type='radio' id='position-vertical-<?php print $type?>' name='display_position_vertical' value='<?php print $type?>' <?php if ($display_position_vertical == $type)print 'checked="checked"';?>/>
                        <label for='position-vertical-<?php print $type?>' class="post-wizard__position__item<?php if ($display_position_vertical == $type)print ' selected';?>">
                            <i class="post-wizard__position__ico <?php print $type?>"></i>
                            <span class="post-wizard__position__item__inner"><?php print $data['label']?></span>
                        </label>
                    </li>
                    <?php endforeach?>
                </ul>

                <div class="post-wizard__position__vertical-pretext post-wizard__pretext">
                	<div class="post-wizard__position__pretext__inner">
                		<p>Left side - the widget will be fixed to the left side of the page.</p>
                	</div>
                	<div class="post-wizard__position__pretext__inner">
                		<p>Right side - the widget will be fixed to the right side of the page.</p>
                	</div>
                </div>

                 <h2><strong><?php _e('Services', 'po.st')?></strong></h2>
                 <div class="post-wizard__preview post-wizard__preview_<?php print $design_totaltype?> post-wizard__type_<?php print $design_type?>">
                 	<div class="post-wizard__pretext">
                 		<p>Select the systems and counters from the list below and change their order by dragging &amp; dropping. Enable/disable the system counters by clicking on them.<br />
                 		To see how your widget will appear, use the "Full Preview" link at the bottom.</p>
                 	</div>

                    <!-- // QUICK PREVIEW // -->
                    <div class="post-wizard__quickpreview">
                        <ul class="post-wizard__quickpreview__show post-wizard__services__icons" id="boxes">
                            <?php foreach ($design_icons as $serv => $counter): ?>
                                <?php if (isset($avServices[$serv])): ?>
                                    <li class="post-wizard__quickpreview__show__li service-<?php print $serv?> <?php if ($counter)print 'counter';?>" name='<?php print $serv?>'>
                                        <span class="icon"></span>
                                        <span class="c"></span>
                                        <input type='hidden' name='icons[<?php print $serv?>]' value='<?php print $counter?>' />
                                    </li>
                                <?php endif ?>
                           <?php endforeach ?>
                        </ul>

                        <ul class="post-wizard__quickpreview__show post-wizard__services__buttons" id="boxes1">
                            <?php foreach ($design_buttons as $serv => $counter): ?>
                                <?php if (isset($avServices[$serv])): ?>
                                    <li class="post-wizard__quickpreview__show__li service-<?php print $serv?>" name='<?php print $serv?>'>
                                        <span class="icon"></span>
                                        <span class="c"></span>
                                        <input type='hidden' name='buttons[<?php print $serv?>]' value='<?php print $counter?>' />
                                    </li>
                                <?php endif ?>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <!-- services -->
                    <div class="post-wizard__services">
                    	<div class="post-wizard__preview__pinit">
                    		In order for Pinterest to work properly, an image needs to be specified to ‘pin’ when you write a post.
                    	</div>
                        <div class="post-wizard__services__icons">
                            <ul class="post-wizard__services__list">
                                <?php ksort($avServices);?>
                                 <?php foreach ($avServices as $serv => $data): ?>

                                 <li class="post-wizard__services__li service-<?php print $serv?> <?php if (isset($design_icons[$serv])) print 'added';?>" id='service_<?php print $serv?>' data-name="<?php print $serv?>">
                                    <i class="icon"></i>
                                    <span class="title"><?php print $data['name'];?></span>
                                    <span class="toolbar">
                                         <?php if (isset($data['counter']) && $data['counter']):?>
                                         <span class="counter <?php if (isset($design_icons[$serv]) && $design_icons[$serv]) print 'active';?>" title="Enable counter"></span>
                                          <?php endif;?>
                                        <i class="in"></i>
                                    </span>
                                </li>
                                  <?php endforeach ?>
                            </ul>
                        </div>
                        <div class="post-wizard__services__buttons">
                            <ul class="post-wizard__services__list">
                             <?php foreach ($avServices as $serv => $data): ?>
                                <?php if (isset($data['expanded'])):?>

                                    <li class="post-wizard__services__li service-<?php print $serv?> <?php if (isset($design_buttons[$serv])) print 'added';?>" id='service_<?php print $serv?>' data-name="<?php print $serv?>">
                                    <i class="icon"></i>
                                    <span class="title"><?php print $data['name'];?></span>
                                    <span class="toolbar">
                                        <i class="in"></i>
                                    </span>
                                </li>
                                <?php endif?>
                            <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                    <!-- end  -->
                </div>

            </div>

            <div class='custom-code' style='<?php if (!$design_custom_code_on) print "display: none;"?>'>
                <h2>
	                <strong>Custom Code</strong>
				</h2>

				<div class="custom-code-area">
					<div class="custom-code-area__textarea">
						<small class="return-custom-defaults">
							<a href="#" class="return-custom-defaults__item">Reset to default code</a>
						</small>
<textarea name='design_custom_code'><?php
$str = stripslashes($design_custom_code);
echo htmlentities($str);

?></textarea>
					</div>
					<div class="custom-code-area__helper">
						<?php _e('<p><strong>Available WordPress variables:</strong></p>
						<p>[PAGEURL] — permalink to shared item;<br />
							[PAGETITLE] — shared item\'s title;<br />
							[IMAGEURL] — link to post\'s image, mandatory for Pinterest button.</p>');?>

						<p><?php _e('Additional customizations like size, share counter, systems set, etc. are available. Please refer to the <a href="http://support.po.st/hc/en-us/articles/200279567-Po-st-Integration-Guide">integration guide</a> for more details.');?></p>
					</div>
				</div>







                <h2><strong><?php _e('Position', 'po.st')?></strong></h2>
                <ul class="post-wizard__position  post-wizard__position__horizontal">
                    <?php foreach ($positionType['horizontal'] as $type => $data):?>
                    <li>
                        <input type='checkbox' id='position-custom-horizontal-<?php print $type?>' name='display_custom_position_horizontal[]' value='<?php print $type?>' <?php if ((is_array($display_custom_position_horizontal) && in_array($type, $display_custom_position_horizontal)) || $display_custom_position_horizontal == $type)print 'checked="checked"';?>/>
                        <label for='position-custom-horizontal-<?php print $type?>' class="post-wizard__position__item<?php if ((is_array($display_custom_position_horizontal) && in_array($type, $display_custom_position_horizontal)) || $display_custom_position_horizontal == $type)print ' selected';?>">
                            <i class="post-wizard__position__ico <?php print $type?>"></i>
                            <span class="post-wizard__position__item__inner"><?php print $data['label']?></span>
                        </label>
                    </li>
                    <?php endforeach?>
                </ul>
            </div>
            <div id='preview'>
            </div>

              <div class="post-wizard__btn">
                        <input type="submit" value="<?php _e('Save changes', 'po.st');?>" class="button-primary" />
<?php
$preview_link = esc_url( get_option( 'home' ) . '/' );
if ( is_ssl() ){
    $preview_link = str_replace( 'http://', 'https://', $preview_link );
}
$stylesheet = get_option('stylesheet');
$template = get_option('template');
$preview_link = htmlspecialchars( add_query_arg( array( 'preview' => 1, 'template' => $template, 'stylesheet' => $stylesheet, 'preview_iframe' => true, 'TB_iframe' => 'true' ), $preview_link ) );
?>
                        <a href="<?php print $preview_link?>" class="post-wizard__fullpreview thickbox thickbox-preview">
                            <?php _e('Full Preview', 'po.st')?>
                        </a>
                        <input type='hidden' value='save' name='post_action'/>
                    </div>

        </div>





           <!-- <input type='submit' onclick='validateSave();' value=''/> -->


    </form>
</div>
<script type="text/javascript">
    var avServices = <?php print json_encode($avServices)?>;
    var displayTypes = <?php print json_encode($displayTypes)?>;
    var customCodeText = "<?php _e('Use Custom Code','po.st')?>";
    var wizardCodeText = "<?php _e('Step by Step Wizard','po.st')?>";
    var servicesClases = "<?php print implode(' ', array_keys($displayTypes))?>";
</script>
