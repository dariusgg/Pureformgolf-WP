jQuery(function(){
    widgetconstructor(jQuery);
});

function widgetconstructor($){
    var form = $('.post-form'),
    	wizard = $('.post-wizard',form),
        orientation = $('.post-wizard__orientation',wizard),
        position = $('.post-wizard__position',form),
        preview = $('.post-wizard__preview',wizard),
        quickpreview = $('.post-wizard__quickpreview__show',wizard),
        wizardStyle = $('.post-wizard__style',wizard),
        curType = $('input:checked',wizardStyle).attr('data-type');
        pinError = $('.post-wizard__preview__pinit',wizard);

	var defaultCode = '<div class="pw-widget pw-size-small" pw:url="[PAGEURL]" pw:title="[PAGETITLE]">\n\t<a class="pw-button-facebook"></a>\n\t<a class="pw-button-twitter"></a>\n\t<a class="pw-button-email"></a>\n\t<a class="pw-button-stumbleupon"></a>\n\t<a class="pw-button-post"></a>\n</div>'

	$('.return-custom-defaults__item',form).bind('click', function(){

		$('textarea[name=design_custom_code]',form)[0].value = defaultCode;
		return false;
	});

    quickpreview.each(function(){

        if ($(wizard).hasClass('post-wizard_horizontal')){
            $(this).sortable({vertical: false});
        } else {
            $(this).sortable({vertical: true});
        }
    });

    $('input',orientation).bind('click',function(){
        var self = this;
        if(!$(this).next().hasClass('selected')) {
            wizard.removeClassRegEx(/^post-wizard_/);
            wizard.addClass('post-wizard_'+this.value);
            $('.post-wizard__orientation__item',orientation).removeClass('selected');
            $(this).next().addClass('selected');

            quickpreview.each(function(){
                if (self.value === 'vertical' ){
                    $(this).sortable("vertical",true);
                } else {
                    $(this).sortable("vertical",false);
                }
            });
        }
    });

	$('label',orientation).bind('click', function(){
		if(!$(this).hasClass('selected')) {
			$(this).parent().find('input').trigger('click');
		}
	});

	position.each(function($t){
		var $t = $(this);

		$('input',$t).bind('click',function(){

			if(!$(this).next().hasClass('selected') && $(this).attr('name') === 'display_position_vertical') {
				$('.post-wizard__position__item',$t).removeClass('selected');
				$(this).next().addClass('selected');
				var idx = $('input',$t).index(this);
				if($t.hasClass('post-wizard__position__vertical')){
					$('.post-wizard__position__vertical-pretext .post-wizard__position__pretext__inner').hide();
					$('.post-wizard__position__vertical-pretext .post-wizard__position__pretext__inner').eq(idx).show();
				}
			}
		});
		$('label',$t).bind('click', function(){
			if ($(this).parent().find('input').attr('name') === 'display_position_horizontal[]' || $(this).parent().find('input').attr('name') === 'display_custom_position_horizontal[]') {
				if(!$(this).hasClass('selected')) {
					$(this).addClass('selected');
					$(this).parent().find('input').attr('checked', !$(this).parent().find('input').is(':checked'))

				} else {
					if (!($(this).closest('ul').find('.selected').length === 2)) {
						$('.post-wizard__position__item', $t).addClass('selected');
						$('input', $t).attr('checked', true)
					}
					$(this).removeClass('selected');
					$(this).parent().find('input').attr('checked', false);
				}

			} else {
				if(!$(this).hasClass('selected')) {
					$(this).parent().find('input').trigger('click');
				}
			}
			return false;
		});

		if($t.hasClass('post-wizard__position__vertical')) {
			var activeSidePosition = $('input',$t).index($('input:checked',$t));
			$('.post-wizard__position__vertical-pretext .post-wizard__position__pretext__inner').hide();
			$('.post-wizard__position__vertical-pretext .post-wizard__position__pretext__inner').eq(activeSidePosition).show();
		}


	});

    $('input',wizardStyle).bind('click',function(){
        if(!$(this).parent().hasClass('selected')) {
            curType = $(this).attr('data-type');
            $('#total-type').val(curType);
            if(!preview.hasClass('post-wizard__preview_'+curType)) {
                preview.removeClassRegEx(/^post-wizard__preview_/);
                preview.addClass('post-wizard__preview_'+curType);
            }
            preview.removeClassRegEx(/^post-wizard__type_/);
            preview.addClass('post-wizard__type_'+this.value);
            $('.post-wizard__style__item',wizardStyle).removeClass('selected');
            $(this).parent().addClass('selected');
        }
    });

    $('label',wizardStyle).bind('click', function(){
    	if(!$(this).hasClass('selected')) {
    		$(this).parent().find('input').trigger('click');
    	}
    	return false;
    });

    $('.post-wizard__services__li',wizard).bind('click',function(){
        var $t = $(this);
        if($t.hasClass('added')) {
            $t.removeClass('added');
            $('.counter',$t).removeClass('active');
            $('.post-wizard__quickpreview .post-wizard__services__'+curType,preview).find('.service-'+$t.attr('data-name')).remove();
        }
        else {
            $t.addClass('added');
            var newButton = $("<li class='post-wizard__quickpreview__show__li service-"+$t.attr('data-name')+"'>\
                <span class='icon'></span><span class='c'></span>\
                <input type='hidden' name='"+curType+"["+$t.attr('data-name')+"]' value='0' />\
            </li>");
            if($t.attr('data-name') == 'post') {
                $(newButton).appendTo('.post-wizard__quickpreview .post-wizard__services__'+curType,preview);
            }
            else {
                $(newButton).prependTo('.post-wizard__quickpreview .post-wizard__services__'+curType,preview);
            }
        }
        return false;
    });
    $('.post-wizard__services__li .counter',wizard).bind('click',function(){
        var $t = $(this),
            $tPar = $t.closest('.post-wizard__services__li'),
            quickIcon = $('.post-wizard__quickpreview .post-wizard__services__'+curType,preview).find('.service-'+$tPar.attr('data-name'));

        if($t.hasClass('active')) {
            $t.removeClass('active');
            quickIcon.removeClass('counter');
            $('input',quickIcon).val('0');
        }
        else {
            $t.addClass('active');
            quickIcon.addClass('counter');
            $('input',quickIcon).val('1');
        }

        return false;
    });

    $('.type-wizard__link',wizard).bind('click',function(){
        var customOn = $('input[name="design_custom_code_on"]',wizard).val();
        if (customOn == 1) {
            $('input[name="design_custom_code_on"]',wizard).val(0);
            $(this).html(customCodeText);
            $('.wizard-pane',wizard).show();
            $('.custom-code',wizard).hide();
        } else {
            $('input[name="design_custom_code_on"]',wizard).val(1);
            $(this).html(wizardCodeText);
            $('.wizard-pane',wizard).hide();
            $('.custom-code',wizard).show();
        }

        return false;
    });


    $('.post-wizard__fullpreview').bind('click',function(){

        var form = $('#post_form');
        var iFrameLink = this;

        $.post(
            ajaxurl,
            form.serialize() + '&post_action=preview&action=post_ajax_preview',
            function (response){
                var tbWidth = $(window).width() - 90;
                var tbHeight = $(window).height() - 60;
                if ( typeof tb_click != 'undefined' &&  $.isFunction(tb_click.call)){
                   tb_click.call(iFrameLink);
                }

                $('#TB_iframeContent').width('100%');

                var tbWindow = $('#TB_window');

                if ( tbWindow.size() ) {
                    tbWindow.width(tbWidth).height(tbHeight);
                    $('#TB_iframeContent').width(tbWidth).height(tbHeight - 27);
                    tbWindow.css({'margin-left': '-' + parseInt((tbWidth / 2),10) + 'px'});
                    if ( typeof document.body.style.maxWidth != 'undefined' )
                        tbWindow.css({'top':'30px','margin-top':'0'});
                }
            }
        );
        return false;
    });

    $('#post_form').bind('submit',function(){
        var p_key = $.trim($('#p_key').val()).length;
        if (p_key < 1){
            $('#p_key').addClass('error-ip').focus();
            $('#pubkeyerror').removeClass('updated').addClass('error');
            $('.post-form__pubkey_error').show();
            $(window).scrollTop(0);
             $('#p_key').bind('keydown', function(ev){
                $('.post-form__pubkey_error').hide();
                $('#p_key').removeClass('error-ip').off(ev);
             })
            return false;
        }
    });

}

(function($)
{
    $.fn.removeClassRegEx = function(regex)
    {
        var classes = $(this).attr('class');

        if(!classes || !regex) return false;

        var classArray = [];
        classes = classes.split(' ');

        for(var i=0, len=classes.length; i<len; i++) {
            if(!classes[i].match(regex)) classArray.push(classes[i]);
		}
        $(this).attr('class', classArray.join(' '));
    };
})(jQuery);
