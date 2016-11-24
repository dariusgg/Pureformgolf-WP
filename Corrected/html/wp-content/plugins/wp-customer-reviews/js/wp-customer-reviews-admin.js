(function($){
	$(function() {
		
		// begin: post meta fields
		var metabox = $('#wpcr3-meta-box');
		
		if (metabox.length) {
			var jqfields = metabox.find('input,select,textarea');
			var fields = {};
			jqfields.each(function(i,v){
				v = $(v), id = v.attr('id');
				fields[id] = v;
			});
			
			var toggleAllFields = function(show) {
				if (show) { jqfields.not("#wpcr3_enable").parents("tr").show(); }
				else { jqfields.not("#wpcr3_enable").parents("tr").hide(); }
			};
			
			var toggleTypeFields = function(type) {
				jqfields.removeAttr('disabled');
				jqfields.filter("[id^=wpcr3_business_], [id^=wpcr3_product_]").parents("tr").hide();
				if (type === "business") { jqfields.filter("[id^=wpcr3_business_]").parents("tr").show(); }
				else if (type === "product") { jqfields.filter("[id^=wpcr3_product_]").parents("tr").show(); }				
				jqfields.not(":visible").attr('disabled','disabled');
			};
			
			fields.wpcr3_format.change(function(){
				var t = $(this);
				toggleTypeFields(t.val());
			}).change();
			
			fields.wpcr3_enable.change(function(){
				var t = $(this), checked = t.is(":checked");
				toggleAllFields(checked);
				if (checked) {
					fields.wpcr3_format.change();
				}
			}).change();
		}
		// end: post meta fields
	
		// begin: plugin settings fields
		var options = $('.wpcr3_myplugin_options');
		if (options.length) {
			$('.setting_wpcr3_option_custom_fields input.need_pro').click(function(e){
				e.preventDefault();
				$(this).removeAttr('checked');
				alert('Rating custom fields is available in the pro version.');
			});
			
			$('.table_multi_input_checkbox a.addmore').click(function(e){
				e.preventDefault(); var t = $(this);
				if (t.hasClass('need_pro')) {
					alert('Additional custom fields are available in the pro version.');
					return false;
				}
			});
		}
		// end: plugin settings fields
	});
})(jQuery);

function make_stars_from_rating(me) {
    var w = '', html = me.html();
    switch (html) {
        case 'Rated 1 Star':
            w = '20'; break;
        case 'Rated 2 Stars':
            w = '40'; break;
        case 'Rated 3 Stars':
            w = '60'; break;
        case 'Rated 4 Stars':
            w = '80'; break;
        case 'Rated 5 Stars':
            w = '100'; break;
    }
    me.html('<div class="sp_rating"><div class="base"><div class="average" style="width:'+w+'%"></div></div></div>');
}