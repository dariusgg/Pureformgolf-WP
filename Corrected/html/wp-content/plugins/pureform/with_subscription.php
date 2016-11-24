<?php
 $purl = plugin_dir_url(__FILE__); 
	 
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

	// echo $purl;
  ?>
<article class="p-container">

<link href="<?php echo $purl; ?>css/style.css" rel="stylesheet" type="text/css">
<link href='https://fonts.googleapis.com/css?family=Lato:400,100,100italic,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Ubuntu:400,300,300italic,400italic,500,500italic,700' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>
<style>
.wrapper{margin: 40px 0;}
form {margin: 0;}
.standerd_bag_box{width: 32%;margin: 30px 0.5% 0;}
.note_sec{width: 50%;}
.next_btn{ float: right; margin-top: 25px;width: auto;}
.next_btn input{color: #848484;display: block;font: 400 14px/16px "Ubuntu",sans-serif;margin: 0;padding: 0 10px 0 0;text-transform: uppercase;
background: url(../wp-content/plugins/pureform/images/next.png) no-repeat right;}
.in_form_inr input[type="button"]{background: #00ccff; padding: 15px 60px; color: #fff; margin-top: 40px;}
.ribon_prt{width: auto; position: absolute; right: -4px; top: -4px;}
.error{float: left; text-align: right; width: 100%; margin: 5px 0 0 0; }

@media only screen and (max-width:767px) 
{

.standerd_bag_box{width:100%;margin: 30px 0 0;}

}

@media only screen and (max-width:480px) 
{
.in_sec_in h3 {font-size: 19px;}
.frm_sec h3 { font:400 24px/32px "Ubuntu Condensed",sans-serif;}


}


</style>
  <div class="wrapper">
	<div class="frm-wrap-main">
		<div class="container">
		<span id="error_msg"></span>
			<form name="pre_form" id="pre_form" >
			<input type="hidden" class="required" name="pure_package" id="pure_package" value="" />
			<input type="hidden" class="required" name="pure_club" id="pure_club" value="" />
			<input type="hidden" class="required" name="action" id="pure_action" value="process_user_form" />
			
			<input type="hidden" class="required" name="pure_city" id="pure_city" value="" />
		
			<input type="hidden" class="required" name="pure_time" id="pure_time" value="AM" />
				<?php include("step1.php");
						include("step2.php");
						include("step3.php");
						include("step4.php");
						include("step5.php");
						include("step6.php");						
				?>
			</form>
		</div>
	</div>
	
</div>

  <script type="text/javascript">
jQuery(document).ready(function() {
jQuery('.datepicker').datepicker ({
dateFormat: 'dd-mm-yy'
});
});

function show_form(obj, obj1, obj2)
{//alert("Hello ajenderr sisasasdfngh th");
	var process = "success";
	var data_val = jQuery(obj2).attr('data_val');
		if( jQuery.trim(obj) == 1 && jQuery.trim(obj2) != "")
		{
			jQuery("#pure_package").val(data_val);
		}
		
	if( jQuery.trim(obj) == 2 && jQuery.trim(obj2) == 'next' )
		{
		
			if( jQuery.trim(jQuery("#pure_club").val()) == "")
			{
				alert("Please select atleast one club");

				process = "fail";
			}
			
		}
		
		if( jQuery.trim(obj) == 3 && jQuery.trim(obj2) == 'next')
		{
		//jQuery("#pure_club").val(data_val);
			if( jQuery.trim(jQuery("#pure_city").val()) == "")
			{
				alert("Please select a city");

				process = "fail";
			}
			
		}
		
		if( jQuery.trim(obj) == 4 && jQuery.trim(obj2) != "")
		{
			if( jQuery.trim(jQuery("#pure_date").val()) == "")
			{
				//alert();
				set_message("pure_date", jQuery("#pure_date").attr("data_msg"));
				process = "fail";
			}
			
			
		}
		
		if( jQuery.trim(obj) == 5 && jQuery.trim(obj2) == "process_form")
		{
			//jQuery("#pure_best_time").val(data_val);
			
			if( validate_form() == true)
			{
				submit_form();
				return false;
				process = "success";
			}
			else{
				return false;
				process = "fail";
			}
		}
	
		if(jQuery.trim(process) == 'success')
		{
			jQuery("#div_step"+obj).hide();
			jQuery("#div_step"+obj1).show();
		}		
	
	
}
function submit_form()
{
	var form_val = jQuery("#pre_form").serialize();
	var ajax_url = '<?php echo site_url().'/wp-content/plugins/pureform/jx_common.php'?>';
	
	jQuery.ajax({
		type:"post",
		url:ajax_url,
		data:form_val,
		success:function(msg)
		{
			var msg = msg.split("::");
			if(jQuery.trim(msg[0])=='success')
			{
				jQuery("#div_step5").hide();
				jQuery("#div_step6").show();
			}
			else
			{ alert(msg[1]); }
		}
	})
}
function validate_form()
{
	var msg="";
	jQuery("#pre_form .required").each(function(){
		if( jQuery.trim( jQuery(this).val() ) == "" )
		{
			msg = 1; 
			set_message(jQuery(this).attr("id"), jQuery(this).attr("data_msg"));
		}
		if( jQuery(this).hasClass('email') && jQuery.trim( jQuery(this).val() ) != "")
			{
				if(validateEmail(jQuery(this).val()) == false)
				{
					msg =1; 
					set_message(jQuery(this).attr("id"), jQuery(this).attr("data_msg"));
				}
			}
	})
	
	if( jQuery.trim(msg) == '')
	{
		return true
	}
	else
	{
	//	jQuery("#error_msg").html(msg);
		return false
	}
}

function settime(obj)
{
	var data_val = jQuery(obj).attr('data_val');
	jQuery(".active_time").removeClass('active_time');
	jQuery(obj).addClass('active_time');
	jQuery("#pure_time").val(data_val);
}
function set_message(obj1, obj2)
		{
			jQuery("#msg_"+obj1).html(obj2);
			jQuery( "#"+obj1).click(function() {
				setTimeout(function(){ 
				
				jQuery("#msg_"+obj1).html("");
					}, 1800);
				})
		}

function unset_message(obj)
{
		setTimeout(function(){ 
		jQuery("#"+obj).removeClass("error_class");
			}, 1500);
}
function validateEmail(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}
</script>
 
</article>