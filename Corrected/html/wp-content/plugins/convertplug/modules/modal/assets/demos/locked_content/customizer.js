/*

jQuery(document).ready(function(){

	jQuery("body").on("click", ".cp-form-container", function(e){ parent.setFocusElement('form_bg_color'); e.stopPropagation(); });	

	// do the stuff to customize the element upon the action "smile_data_received"
	jQuery(this).on('smile_data_received',function(e,data){
		// data - this is an object that stores all your input information in a format - input:value

		// Common variables 
		var style 				= data.style,
			cp_submit 			= jQuery(".cp-submit"),
			cp_form_button      = jQuery(".form-button"),
			cp_modal_body		= jQuery(".cp-modal-body"),
			cp_modal			= jQuery(".cp-modal"),
			cp_modal_content	= jQuery(".cp-modal-content"),
			modal_overlay		= jQuery(".cp-overlay"),
			cp_modal_body_inner	= jQuery(".cp-modal-body-inner"),
			cp_md_overlay       = jQuery(".cp-modal-body-overlay"),
			form_with_name 		= jQuery(".cp-form-with-name"),
			form_without_name 	= jQuery(".cp-form-without-name"),
			cp_title 			= jQuery(".cp-title"),
			cp_short_description = jQuery(".cp-short-description"),
			cp_form_container 	 = jQuery(".cp-form-container"),
			cp_email_form		= jQuery(".cp-email-form"),
			cp_name_form		= jQuery(".cp-name-form"),
			cp_submit_container	= jQuery(".cp-submit-container");			 	

		// style dependent variables  	
		var modal_size					= data.modal_size,
			cp_modal_width				= data.cp_modal_width,
			modal_title 				= data.modal_title1,
			bg_color					= data.modal_bg_color,
			overlay_bg_color			= data.modal_overlay_bg_color,
			modal_title_color			= data.modal_title_color,
			tip_color					= data.tip_color,
			border_str 					= data.border,
			box_shadow_str 				= data.box_shadow,
			modal_content				= data.modal_content,
			close_txt					= data.close_txt,
			content_padding				= data.content_padding,
			modal_bg_image				= data.modal_bg_image,
			opt_bg						= data.opt_bg,
			modal_bg_image_size			= data.modal_bg_image_size,
			namefield 					= data.namefield,
			affiliate_title 			= data.affiliate_title,
			cp_google_fonts 			= data.cp_google_fonts,
			cp_name_form        		= jQuery(".cp-name-form"),
			cp_submit_container         = jQuery('.cp-submit-container'),
			image_vertical_position 	= data.image_vertical_position,
			image_horizontal_position 	= data.image_horizontal_position,
			image_size 					= data.image_size,
			modal_image 	    		= data.modal_image,
			image_resp_width 		  	= data.image_resp_width,
			modal_image_size			= data.modal_image_size,
			title_bg_color				= data.modal_title_bg_color,
			modal_form_bg_color 		= data.form_bg_color,
		    modal_form_border_color 	= data.form_border_color,
		    btn_disp_next_line 			= data.btn_disp_next_line,
		    
		    cp_img_container			= jQuery(".cp-image-container");


		var border = generateBorderCss(border_str);		
		var box_shadow = generateBoxShadow(box_shadow_str);
		var style = '';		
		
		if( box_shadow.indexOf("inset") > -1 ) {
				style = border; 
				cp_modal_content.attr('style', style);
				cp_md_overlay.attr('style', box_shadow);
				cp_modal_content.css('box-shadow', 'none');
			
		} else {
				cp_md_overlay.css('box-shadow', 'none');
				style = border+';'+box_shadow; 
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
			
		var modal_img_default = modal_image;		
		if( modal_img_default.indexOf('http') === -1 )
		 {					
			if( modal_image !== "" ) {
				var img_data = {action:'cp_get_image',img_id:modal_image,size:modal_image_size};
				jQuery.ajax({
					url: smile_ajax.url,
					data: img_data,
					type: "POST",
					success: function(img){
						cp_img_container.html('<img src="'+img+'" class="cp-image cp-highlight" />');
						cp_img_container.find('img').css({'top': image_vertical_position+'px','left': image_horizontal_position+'px' ,'max-width': image_size+'px'});
					}
				});
			} else {
				cp_img_container.html('');
				cp_img_container.find('img').removeAttr('style');
			}
		} else {
			modal_image_full_src = modal_image.split('|');
			modal_image_src = modal_image_full_src[0];
			cp_img_container.html('<img src="'+modal_image_src+'" class="cp-image cp-highlight" />');
			cp_img_container.find('img').css({'top': image_vertical_position+'px','left': image_horizontal_position+'px','max-width': image_size+'px'});
		}

		if( modal_title == "" ){
			jQuery(".cp-row.cp-blank-title").css('display','none');
		} else {
			jQuery(".cp-row.cp-blank-title").css('display','block');
		}

		//cp_form_container.css({"background-color":modal_form_bg_color , "border-color":modal_form_border_color});

		// cp_form_container.css({ 
		// 	"background-color":modal_form_bg_color,
		// 	"border-color":modal_form_border_color,		
		// });
	});
});
*/