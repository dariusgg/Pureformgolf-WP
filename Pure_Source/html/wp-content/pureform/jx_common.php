 <?php 
 require  '../../../wp-load.php';
	error_reporting(0);
 global $wpdb;
 
if($_REQUEST['action'] == 'process_user_form'  )
{
	$pure_package			= $_REQUEST['pure_package'];
	$pure_club				= $_REQUEST['pure_club'];
	$pure_city				= $_REQUEST['pure_city'];
	$pure_best_time			= $_REQUEST['pure_best_time'];
	$customer_name			= $_REQUEST['customer_name'];
	$customer_email			= $_REQUEST['customer_email'];
	$customer_phone			= $_REQUEST['customer_phone'];
	$customer_golf_link		= $_REQUEST['customer_golf_link'];

	
	
	$body = '<style>
body{font-family: "Oxygen",sans-serif;}
	h1.title_mail {border-bottom: 1px solid #969696; color: #F58220;font-size: 18px; margin: 0 0 10px; padding-bottom: 5px;}
.email_text_cont {font: 15px arial;}
 a {color: #07c5c3;}
.inner_mail_cont {height: auto; min-height: 250px;}
.v-link{font-size:14px; font-weight:800;text-transform: uppercase;}
</style>
	
	<table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td>
	<div style="width:100%; margin-bottom:20px;">
		 <img style="width: 100px;" alt="logo" src=""></div></td>
		 </tr>
		 <tr>
			<td>
				 <div style="width:display:block;width:100%;">
					<div style="display:block;" class="inner_mail_cont">
				 
						<div class="title_boxs" style="display:block"><h1 class="title_mail">Hello Admin,</h1></div>
						<div style="clear:both"></div>
							 
							<div class="mailcont_boxs" style="display:block"> 
							 
								 <table width="100%" cellspacing="0" cellpadding="5" border="0" class="email_text_cont">
									<tbody>
										<tr><td colspan="3">There is a query from the website please find details below. <br><br></td></tr>
										<tr><td align="left">Name</td>
										  <td colspan="2">'.$customer_name.'</td>
										  </tr>
										  <tr><td align="left">Email</td>
										  <td colspan="2">'.$customer_email.'</td>
										  </tr>
										  <tr><td align="left">Phone</td>
										  <td colspan="2">'.$customer_phone.'</td>
										  </tr>
										  
										  <tr><td align="left">Package</td>
										  <td colspan="2">'.$pure_package.'</td>
										  </tr>
										  
										  <tr><td align="left">Club</td>
										  <td colspan="2">'.$pure_club.'</td>
										  </tr>
										  
										   <tr><td align="left">City</td>
										  <td colspan="2">'.$pure_city.'</td>
										  </tr>
										   <tr><td align="left">Time</td>
										  <td colspan="2">'.$pure_best_time.'</td>
										  </tr>
										
										<tr><td align="left">Golf Link</td>
										  <td colspan="2">'.$customer_golf_link.'</td>
										  </tr>
										  
										
										<tr><td colspan="3">Thank You.</td></tr>
										
										 </tr>
									 </tbody>
								 </table>
							 </div>
						 <div style="clear:both"></div>
				 
		 
		 
					 
					</div>
				</div>
			</td>

			 </tr>
			 
			 <!-- footer links-->
			 
		
 
 <tr>
		<td style="padding-top:20px; text-align:center;">
		<span style="color:#000;">&copy; </span><span style="color:#f6accd;">'.get_option('name').'</span>
		</td>
		</tr>	
		
			 
			
			 
			 
			 </tbody>
 </table>';

 
	
		$subject 			= "Message from website";
		$sender 			= get_option('name');
		$admin_email 		= get_option( 'admin_email' );
		$to					= $admin_email;
		$cc = "";
	$headers = "From: ".$sender." <".$admin_email.">\n"; 
	$headers .= "Bcc: ".$bcc."sunil8986@gmail.com,\n";
	$headers .= "Cc: ".$cc.",\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\n";
		 mail( "sunil8986@gmail.com", $subject, $body, $headers );
		$mail = mail( $to, $subject, $body, $headers );
		
		if($mail)
		{
			echo 'success::';
		}
		else
		{
			echo 'fail::There must be some issue please contact admin.';
		}
}
?>