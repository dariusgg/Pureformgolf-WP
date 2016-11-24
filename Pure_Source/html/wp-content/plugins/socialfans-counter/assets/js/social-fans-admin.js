
(function(factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals.
		factory(jQuery);
	}
}(function($) {

	var pluses = /\+/g;

	function encode(s) {
		return config.raw ? s : encodeURIComponent(s);
	}

	function decode(s) {
		return config.raw ? s : decodeURIComponent(s);
	}

	function stringifyCookieValue(value) {
		return encode(config.json ? JSON.stringify(value) : String(value));
	}

	function parseCookieValue(s) {
		if (s.indexOf('"') === 0) {
			// This is a quoted cookie as according to RFC2068, unescape...
			s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
		}

		try {
			// Replace server-side written pluses with spaces.
			// If we can't decode the cookie, ignore it, it's unusable.
			// If we can't parse the cookie, ignore it, it's unusable.
			s = decodeURIComponent(s.replace(pluses, ' '));
			return config.json ? JSON.parse(s) : s;
		} catch (e) {
		}
	}

	function read(s, converter) {
		var value = config.raw ? s : parseCookieValue(s);
		return $.isFunction(converter) ? converter(value) : value;
	}

	var config = $.cookie = function(key, value, options) {

		// Write
		if (value !== undefined && !$.isFunction(value)) {
			options = $.extend({}, config.defaults, options);

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setDate(t.getDate() + days);
			}

			return (document.cookie = [
				encode(key), '=', stringifyCookieValue(value),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path ? '; path=' + options.path : '',
				options.domain ? '; domain=' + options.domain : '',
				options.secure ? '; secure' : ''
			].join(''));
		}

		// Read

		var result = key ? undefined : {};

		// To prevent the for loop in the first place assign an empty array
		// in case there are no cookies at all. Also prevents odd result when
		// calling $.cookie().
		var cookies = document.cookie ? document.cookie.split('; ') : [];

		for (var i = 0, l = cookies.length; i < l; i++) {
			var parts = cookies[i].split('=');
			var name = decode(parts.shift());
			var cookie = parts.join('=');

			if (key && key === name) {
				// If second argument (value) is a function it's a converter...
				result = read(cookie, value);
				break;
			}

			// Prevent storing a cookie that we couldn't decode.
			if (!key && (cookie = read(cookie)) !== undefined) {
				result[name] = cookie;
			}
		}

		return result;
	};

	config.defaults = {};

	$.removeCookie = function(key, options) {
		if ($.cookie(key) === undefined) {
			return false;
		}

		// Must not alter options, thus extending a fresh object...
		$.cookie(key, '', $.extend({}, options, {
			expires: -1
		}));
		return !$.cookie(key);
	};

}));

jQuery(function($) {

	var activeBox = (parseInt(jQuery.cookie('SocialFans_active_box'))) ? parseInt(jQuery.cookie('SocialFans_active_box')) : 0;

	$('#socialfans-accordion').accordion({
		heightStyle: "content",
		animate: false,
		active: parseInt(activeBox),
		header: "> div > h3",
		activate: function(a, b, c) {
			try {
				jQuery.cookie('SocialFans_active_box', parseInt(jQuery(this).find("h3").index(b.newHeader[0])));
			} catch (e) {

			}
		}
	}).sortable({
		axis: "y",
		handle: "h3",
		stop: function(event, ui) {
			// IE doesn't register the blur when sorting
			// so trigger focusout handlers to remove .ui-state-focus
			ui.item.children("h3").triggerHandler("focusout");

			var headers = jQuery(event.target).find('h3');

			if (headers.length > 0) {

				headers.each(function(i, e) {

					var is_active = ($(e).hasClass('ui-state-active'));

					if (is_active)
						jQuery.cookie('SocialFans_active_box', i);

					$(e).children('input[type="hidden"]').val(i);

				})

			}
		}
	});


    $('#stickyfans-accordion').accordion({
        heightStyle: "content",
        animate: false,
        icons: false,
        header: "> div > h3",
    }).sortable({
        axis: "y",
        stop: function(event, ui) {
            // IE doesn't register the blur when sorting
            // so trigger focusout handlers to remove .ui-state-focus
            ui.item.children("h3").triggerHandler("focusout");

            var headers = jQuery(event.target).find('h3');

            if (headers.length > 0) {

                headers.each(function(i, e) {

                    $(e).children('input[type="hidden"]').val(i);

                })

            }
        }
    });

	$('#socialfans-accordion input[type="checkbox"]').click(function(e) {
		e.stopPropagation();
	});
	$('#stickyfans-accordion input[type="checkbox"]').click(function(e) {
		e.stopPropagation();
	});

	handleFacebook();
    handleLinkedin();
	handleVimeo();
	handleRss();
	handleShortcode();
	handleYoutube();

	jQuery('.sf-color-picker').wpColorPicker();


	$('#sfcounter_shortcode_copy').clipboard({
		path: '../wp-content/plugins/socialfans-counter/assets/js/jquery.clipboard.swf',
		copy: function() {
			var text = $('#shortcode-result').val();
			return text;
		}
	});

	$('#sfcounter_shortcode_copy').click(function() {
		alert('Copied');
	});
});


function handleFacebook() {

	var accountTypeEl = jQuery('#sfcounter_facebook_account_type');
	var accessTokenEl = jQuery('#sfcounter_facebook_access_token');
	var followersCountEl = jQuery('#sfcounter_facebook_followers_count');

	if (accountTypeEl.val() == 'followers') {
		accessTokenEl.parents('.form-field').hide();
        followersCountEl.parents('.form-field').show();
	} else {
		accessTokenEl.parents('.form-field').show();
        followersCountEl.parents('.form-field').hide();
	}

	accountTypeEl.change(function() {

        if (accountTypeEl.val() == 'followers') {
            accessTokenEl.parents('.form-field').hide();
            followersCountEl.parents('.form-field').show();
        } else {
            accessTokenEl.parents('.form-field').show();
            followersCountEl.parents('.form-field').hide();
        }
	});

}

function handleLinkedin() {

	var accountTypeEl = jQuery('#sfcounter_linkedin_account_type');
	var connectionsCountEl = jQuery('#sfcounter_linkedin_connections_count');

	if (accountTypeEl.val() == 'profile') {
        connectionsCountEl.parents('.form-field').show();
	} else {
        connectionsCountEl.parents('.form-field').hide();
	}

	accountTypeEl.change(function() {

        if (accountTypeEl.val() == 'profile') {
            connectionsCountEl.parents('.form-field').show();
        } else {
            connectionsCountEl.parents('.form-field').hide();
        }
	});

}

function handleVimeo() {

	var accountTypeEl = jQuery('#sfcounter_vimeo_account_type');
	var accessTokenEl = jQuery('#sfcounter_vimeo_access_token');

	if (accountTypeEl.val() == 'user') {
		accessTokenEl.parents('.form-field').show();
	} else {
		accessTokenEl.parents('.form-field').hide();
	}

	accountTypeEl.change(function() {

		if (jQuery(this).val() == 'user') {
			accessTokenEl.parents('.form-field').show();
		} else {
			accessTokenEl.parents('.form-field').hide();
		}
	});

}

function handleRss() {

	var accountTypeEl = jQuery('#sfcounter_rss_account_type');
	var jsonFileEl = jQuery('#sfcounter_rss_json_file');
	var countEl = jQuery('#sfcounter_rss_count');

	if (accountTypeEl.val() == 'manual') {
		jsonFileEl.parents('.form-field').hide();
		countEl.parents('.form-field').show();
	} else {
		jsonFileEl.parents('.form-field').show();
		countEl.parents('.form-field').hide();
	}

	accountTypeEl.change(function() {

		if (jQuery(this).val() == 'manual') {
			jsonFileEl.parents('.form-field').hide();
			countEl.parents('.form-field').show();
		} else {
			jsonFileEl.parents('.form-field').show();
			countEl.parents('.form-field').hide();
		}
	});

}

function handleYoutube() {

	var accountTypeEl = jQuery('#sfcounter_youtube_account_type');
	var channelCustomUrlEl = jQuery('#sfcounter_youtube_custom_channel_url');

	if (accountTypeEl.val() == 'channel') {
		channelCustomUrlEl.parents('.form-field').show();
	} else {
		channelCustomUrlEl.parents('.form-field').hide();
	}

	accountTypeEl.change(function() {

		if (accountTypeEl.val() == 'channel') {
			channelCustomUrlEl.parents('.form-field').show();
		} else {
			channelCustomUrlEl.parents('.form-field').hide();
		}
	});

}

function handleShortcode() {

    var shortcodeElementsEl = jQuery('.shortcode-elements');

    if (shortcodeElementsEl.length == 1) {
        generateShortcode();

        shortcodeElementsEl.find('input,select').on('change focus blur keyup keydown', function (){
            generateShortcode();
        });
    }

    var stickyShortcodeElementsEl = jQuery('.sticky-shortcode-elements');

    if (stickyShortcodeElementsEl.length == 1) {

        generateStickyShortcode();

        stickyShortcodeElementsEl.find('input,select').on('change focus blur keyup keydown', function() {
            generateStickyShortcode()
        });
    }
}

function generateShortcode() {

	var titleEl = jQuery('#sfcounter_shortcode_title');
	var hide_titleEl = jQuery('#sfcounter_shortcode_hide_title');
	var hide_numbersEl = jQuery('#sfcounter_shortcode_hide_numbers');
	var show_totalEl = jQuery('#sfcounter_shortcode_show_total');
	var box_widthEl = jQuery('#sfcounter_shortcode_box_width');
	var is_lazyEl = jQuery('#sfcounter_shortcode_is_lazy');
	var block_shadowEl = jQuery('#sfcounter_shortcode_block_shadow');
	var block_dividerEl = jQuery('#sfcounter_shortcode_block_divider');
	var block_marginEl = jQuery('#sfcounter_shortcode_block_margin');
	var block_radiusEl = jQuery('#sfcounter_shortcode_block_radius');
	var columnsEl = jQuery('#sfcounter_shortcode_columns');
	var effectsEl = jQuery('#sfcounter_shortcode_effects');
	var icon_colorEl = jQuery('#sfcounter_shortcode_icon_color');
	var bg_colorEl = jQuery('#sfcounter_shortcode_bg_color');
	var hover_text_colorEl = jQuery('#sfcounter_shortcode_hover_text_color');
	var hover_text_bg_colorEl = jQuery('#sfcounter_shortcode_hover_text_bg_color');
	var show_diffEl = jQuery('#sfcounter_shortcode_show_diff');
	var show_diff_lt_zeroEl = jQuery('#sfcounter_shortcode_show_diff_lt_zero');
	var diff_count_text_colorEl = jQuery('#sfcounter_shortcode_diff_count_text_color');
	var diff_count_bg_colorEl = jQuery('#sfcounter_shortcode_diff_count_bg_color');


	var shortcode = '[sfcounter';
	shortcode += ' title="' + titleEl.val() + '"';
	shortcode += ' hide_title="' + (hide_titleEl.is(':checked') ? 1 : 0) + '"';
	shortcode += ' hide_numbers="' + (hide_numbersEl.is(':checked') ? 1 : 0) + '"';
	shortcode += ' show_total="' + (show_totalEl.is(':checked') ? 1 : 0) + '"';
	shortcode += ' box_width="' + box_widthEl.val() + '"';
	shortcode += ' is_lazy="' + (is_lazyEl.is(':checked') ? 1 : 0) + '"';
	shortcode += ' block_shadow="' + (block_shadowEl.is(':checked') ? 1 : 0) + '"';
	shortcode += ' block_divider="' + (block_dividerEl.is(':checked') ? 1 : 0) + '"';
	shortcode += ' animate_numbers="' + (is_lazyEl.is(':checked') ? 1 : 0) + '"';
	shortcode += ' block_margin="' + block_marginEl.val() + '"';
	shortcode += ' block_radius="' + block_radiusEl.val() + '"';
	shortcode += ' columns="' + columnsEl.val() + '"';
	shortcode += ' effects="' + effectsEl.val() + '"';
	shortcode += ' icon_color="' + icon_colorEl.val() + '"';
	shortcode += ' bg_color="' + bg_colorEl.val() + '"';
	shortcode += ' hover_text_color="' + hover_text_colorEl.val() + '"';
	shortcode += ' hover_text_bg_color="' + hover_text_bg_colorEl.val() + '"';
	shortcode += ' show_diff="' + (show_diffEl.is(':checked') ? 1 : 0) + '"';
	shortcode += ' show_diff_lt_zero="' + (show_diff_lt_zeroEl.is(':checked') ? 1 : 0) + '"';
	shortcode += ' diff_count_text_color="' + diff_count_text_colorEl.val() + '"';
	shortcode += ' diff_count_bg_color="' + diff_count_bg_colorEl.val() + '"';
	shortcode += ']';

	jQuery('#shortcode-result').html(shortcode);
}

function generateStickyShortcode() {

	var is_lazyEl = jQuery('#sscounter_shortcode_is_lazy');
	var show_numbersEl = jQuery('#sscounter_shortcode_show_numbers');
	var show_totalEl = jQuery('#sscounter_shortcode_show_total');
	var block_shadowEl = jQuery('#sscounter_shortcode_block_shadow');
	var block_dividerEl = jQuery('#sscounter_shortcode_block_divider');
	var positionEl = jQuery('#sscounter_shortcode_position');
	var block_radiusEl = jQuery('#sscounter_shortcode_block_radius');
	var block_marginEl = jQuery('#sscounter_shortcode_block_margin');
	var icon_colorEl = jQuery('#sscounter_shortcode_icon_color');
	var bg_colorEl = jQuery('#sscounter_shortcode_bg_color');


	var shortcode = '[sscounter';
    shortcode += ' is_lazy="' + (is_lazyEl.is(':checked') ? 1 : 0) + '"';
    shortcode += ' show_numbers="' + (show_numbersEl.is(':checked') ? 1 : 0) + '"';
	shortcode += ' show_total="' + (show_totalEl.is(':checked') ? 1 : 0) + '"';
	shortcode += ' block_shadow="' + (block_shadowEl.is(':checked') ? 1 : 0) + '"';
	shortcode += ' block_divider="' + (block_dividerEl.is(':checked') ? 1 : 0) + '"';
	shortcode += ' position="' + positionEl.val() + '"';
	shortcode += ' block_radius="' + block_radiusEl.val() + '"';
	shortcode += ' block_margin="' + block_marginEl.val() + '"';
	shortcode += ' icon_color="' + icon_colorEl.val() + '"';
	shortcode += ' bg_color="' + bg_colorEl.val() + '"';
	shortcode += ']';

	jQuery('#shortcode-result').html(shortcode);
}
