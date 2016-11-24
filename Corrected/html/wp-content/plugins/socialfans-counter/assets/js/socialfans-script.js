(function($) {

    $.Loadingdotdotdot = function(el, options) {

        var base = this;

        base.$el = $(el);

        base.$el.data("Loadingdotdotdot", base);

        base.dotItUp = function($element, maxDots) {
            if ($element.text().length == maxDots) {
                $element.text(".");
            } else {
                $element.append(".");
            }
        };

        base.stopInterval = function() {
            clearInterval(base.theInterval);
        };

        base.init = function() {

            if ( typeof( speed ) === "undefined" || speed === null ) speed = 300;
            if ( typeof( maxDots ) === "undefined" || maxDots === null ) maxDots = 3;

            base.speed = speed;
            base.maxDots = maxDots;

            base.options = $.extend({},$.Loadingdotdotdot.defaultOptions, options);

            base.$el.html("<span class='sf-loading-dots'>" + base.options.word + "<em></em></span>");

            base.$dots = base.$el.find("em");
            base.$loadingText = base.$el.find("span");

            //base.$el.css("position", "relative");
            base.$loadingText.css({
                "display": "inline-block",
                //"top": (base.$el.outerHeight() / 2) - (base.$loadingText.outerHeight() / 2),
                //"left": (base.$el.width() / 2) - (base.$loadingText.width() / 2)
            });

            base.theInterval = setInterval(base.dotItUp, base.options.speed, base.$dots, base.options.maxDots);

        };

        base.init();

    };

    $.Loadingdotdotdot.defaultOptions = {
        speed: 300,
        maxDots: 3,
        word: "Loading"
    };

    $.fn.Loadingdotdotdot = function(options) {

        if (typeof(options) == "string") {
            var safeGuard = $(this).data('Loadingdotdotdot');
            if (safeGuard) {
                safeGuard.stopInterval();
            }
        } else {
            return this.each(function(){
                (new $.Loadingdotdotdot(this, options));
            });
        }

    };

})(jQuery);
(function($) {
    $.fn.animateNumbers = function(number, duration, ease, stop) {
        return this.each(function() {
            var $this = jQuery(this);
            var start = parseInt($this.text().replace(/,/g, ""));

            if (isNaN(start))
                start = 0;

            jQuery({value: start}).animate({value: number}, {
                duration: duration == undefined ? 1000 : duration,
                easing: ease == undefined ? "swing" : ease,
                step: function() {
                    $this.text(Math.floor(this.value));
                },
                complete: function() {
                    $this.text(stop);
                }
            });
        });
    };
})(jQuery);

jQuery(function($) {

    var screenWidth = (window.innerWidth > 0) ? window.innerWidth : screen.width;

    if (screenWidth <= 320)
    {
        jQuery('.sf-front').each(function() {

            var main_link = jQuery(this).find('a');
            var effect_link = jQuery(this).siblings('.sf-mask').find('a');

            main_link.attr('href', effect_link.attr('href'));

        });
    }

    handleLazyLoad();
    handleStickyLazyLoad();

});

function handleLazyLoad() {

    jQuery('.sf-widget-holder').each(function() {

        var containerEl = jQuery(this);
        var is_lazy = jQuery(this).data('is_lazy');
        var animate_numbers = jQuery(this).data('animate_numbers');

        if (is_lazy) {

            var lazyEl = containerEl.find('.sf-widget-lazy');
            handleLazyLoadNumbers(lazyEl, 'sfcounter');
        }

    });
}

function handleStickyLazyLoad() {

    jQuery('.ss-widget-lazy').each(function() {

        handleLazyLoadNumbers(jQuery(this), 'sscounter');
    });
}

function handleLazyLoadNumbers(lazyEl, action) {

    var duration = 5000;

    lazyEl.find('span.sf-social-count').Loadingdotdotdot({
        "speed": 300,
        "maxDots": 3,
        "word": ""
    });

    jQuery.ajax({
        url: SfcounterObject.ajaxurl,
        data: {action: action},
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            lazyEl.removeClass('sf-widget-lazy');

            lazyEl.find('.sf-block').each(function() {

                var socialName = jQuery(this).data('social');
                var countEl = jQuery(this).find('.sf-front').find('span.sf-social-count');

                var count = response.social[socialName]['count'];
                var stop = response.social[socialName]['count_formated'];

                animateNumbers(countEl, count, stop, duration);
            });
        },
        error: function() {
            lazyEl.removeClass('sf-widget-lazy');
            lazyEl.find('span.sf-social-count').html(0);
        }
    });
}

function animateNumbers(el, count, stop) {

    var duration = 200;

    if (!count || isNaN(count))
        count = 0;

    el.animateNumbers(count, duration, 'swing', stop);

}

function handleLazyScroll(el) {

    jQuery(window).scroll(function() {
        updateMargin(el);
    });
}

function updateMargin(el) {

    var docTop = jQuery(window).scrollTop();
    var elmTop = el.offset().top;
    var elmBot = el.height();

    var margin = 50;

    if (docTop > elmTop) {
        margin = (docTop - elmTop + 50);

        if ((elmTop + margin - 20) > elmBot) {
            margin = (elmBot - 20 - 250);
        }
    }
    
    el.find('.sf-loader-holder').css('margin-top', margin);

}