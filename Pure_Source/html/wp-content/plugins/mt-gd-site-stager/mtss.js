jQuery('document').ready( function ($) {

            // Select all
        jQuery("A[href='#select_all']").click( function() {
            jQuery("#" + jQuery(this).attr('rel') + " INPUT[type='checkbox']").attr('checked', true);
            return false;
        });

        // Select none
        jQuery("A[href='#select_none']").click( function() {
            jQuery("#" + jQuery(this).attr('rel') + " INPUT[type='checkbox']").attr('checked', false);
            return false;
        });

        // Invert selection
        jQuery("A[href='#invert_selection']").click( function() {
            jQuery("#" + jQuery(this).attr('rel') + " INPUT[type='checkbox']").each( function() {
                jQuery(this).attr('checked', !jQuery(this).attr('checked'));
            });
            return false;
        });

    $("#checkAll").click(function () {
        if ($("#checkAll").is(':checked')) {
            $("input.overview").each(function () {
                $(this).prop("checked", true);
            });

        } else {
            $("input.overview").each(function () {
                $(this).prop("checked", false);
            });
        }
    });

    $("#checkFiles").click(function () {
        if ($("#checkFiles").is(':checked')) {
            $("input.file").each(function () {
                $(this).prop("checked", true);
            });

        } else {
            $("input.file").each(function () {
                $(this).prop("checked", false);
            });
        }
    });   

    $("#checkTables").click(function () {
        if ($("#checkTables").is(':checked')) {
            $("input.table").each(function () {
                $(this).prop("checked", true);
            });

        } else {
            $("input.table").each(function () {
                $(this).prop("checked", false);
            });
        }
    });  

    $(document).ready(function() {
    $("body").prepend('<div id="overlay" class="ui-widget-overlay" style="display: none;"></div>');
    $("body").prepend("<div id='PleaseWait' style='display: none;'><img src='../wp-content/plugins/wpstagecoach/ajax-loader.gif'/></div>");
});

$('#mtss').submit(function() {
    var pass = true;
    //some validations

    if(pass == false){
        return false;
    }
    $("#overlay, #PleaseWait").show();

    return true;
});
});
