/*jQuery(document).ready(function(){

	// do the stuff to customize the element upon the action "smile_data_received"
	jQuery(this).on('smile_data_received',function(e,data){
		
		var cp_submit 			= jQuery(".cp-submit"),
			cp_form_button      = jQuery(".form-button"),
			cp_modal_body		= jQuery(".cp-modal-body"),
			cp_modal			= jQuery(".cp-modal"),
			cp_modal_content	= jQuery(".cp-modal-content"),
			modal_overlay		= jQuery(".cp-overlay"),
			cp_modal_body_inner	= jQuery(".cp-modal-body-inner"),
			cp_md_overlay       = jQuery(".cp-modal-body-overlay"),
			cp_submit_container = jQuery('.cp-submit-container');
	
		// data variables  	
		var style 						= data.style,
			modal_size					= data.modal_size,
			cp_modal_width				= data.cp_modal_width,
			modal_title 				= data.modal_title1,
			bg_color					= data.modal_bg_color,
			overlay_bg_color			= data.modal_overlay_bg_color,
			modal_title_color			= data.modal_title_color,
			tip_color					= data.tip_color,
			border_str 					= data.border,
			btn_disp_next_line  		= data.btn_disp_next_line,
			box_shadow_str 				= data.box_shadow,
			modal_content				= data.modal_content,
			close_txt					= data.close_txt,
			content_padding				= data.content_padding,
			modal_bg_image				= data.modal_bg_image,
			opt_bg						= data.opt_bg,
			cp_google_fonts 			= data.cp_google_fonts,
			affiliate_title 			= data.affiliate_title,
			cp_modal_height       		= data.cp_modal_height,
			cp_custom_height 			= data.cp_custom_height;
 		
		modal_content = htmlEntities(modal_content);

		if( modal_content !== "" && typeof modal_content !== "undefined" && jQuery( "#short_desc_editor" ).length !== 0 ){
			CKEDITOR.instances.short_desc_editor.setData(modal_content);
		}	

		var border = generateBorderCss(border_str);		
		var box_shadow = generateBoxShadow(box_shadow_str);
		var style = '';		

		var bgcoloroptn = ';background:'+bg_color;
		if( box_shadow.indexOf("inset") > -1 ) {
			style = border + bgcoloroptn;
			cp_modal_content.attr('style', style);
			cp_md_overlay.attr('style', box_shadow);
			cp_modal_content.css('box-shadow', 'none');			
		} else {
			cp_md_overlay.css('box-shadow', 'none');
			style = border+';'+box_shadow+bgcoloroptn; 
			cp_modal_content.attr('style', style);						
		}
		
		if( typeof content_padding !== "undefined" && content_padding !== "" ){
			if( content_padding == "1" || content_padding == 1){
				cp_modal_body.addClass('no-padding');
			} else {
				cp_modal_body.removeClass('no-padding');
			}
		}

		modal_overlay.css('background',overlay_bg_color);
		
		if( !cp_modal.hasClass("cp-modal-exceed") ){
			cp_modal.attr('class', 'cp-modal '+modal_size);
		} else {
			cp_modal.attr('class', 'cp-modal cp-modal-exceed '+modal_size);
		}

		//apply height to body		
		if( cp_custom_height == 1 ) {
			cp_modal_body.find(".cp-row").addClass('cp-row-center');
			cp_modal_body.find(".cp-text-container").addClass('cp-row-equalized-center');
			cp_modal_body.css( 'min-height', cp_modal_height+'px' );
		} else {
			cp_modal_body.css( 'min-height', '' );
			cp_modal_body.find(".cp-row").removeClass('cp-row-center');
			cp_modal_body.find(".cp-text-container").removeClass('cp-row-equalized-center');
		}
	});
});*/