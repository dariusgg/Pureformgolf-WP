
<div class="frm-wrap full_box" id="div_step5" style="display:none;">
	<div class="frm_sec">
		<div class="top_line">
			<img src="<?php echo $purl; ?>images/step5_brdr.png" alt="" class="line5"/>
		</div>
		<div class="in_form">
			<div class="in_form_inr">
				<h4>Tell us a bit about yourself </h4>
				
					<label>Name</label>
					<input type="text" id="customer_name" class="required" name="customer_name" data_msg="Please enter your name." value=""/>
					<div id="msg_customer_name" class="error"></div>
					<label>Email</label>
					<input type="text" id="customer_email" class="required email" name="customer_email" data_msg="Please enter a valid email." value=""/>
					<div id="msg_customer_email" class="error"></div>
					
					<label>Phone number</label>
					<input type="text" class="required" id="customer_phone" name="customer_phone" data_msg="Please enter phone no." value=""/>
					<div id="msg_customer_phone" class="error" ></div>
					
					<label>Golflink number (optional)</label>
					<input type="text" id="customer_golf_link" name="customer_golf_link" value=""/>	
					<div id="msg_customer_golf_link" class="error"></div>
					
					<input type="button" onClick="show_form(5, 6, 'process_form')" data_val="" name="" value="DONE"/>
				
			</div>
		</div>
		
		<div class="note_sec">
			<a href="javascript:void(0)" onClick="show_form(5, 4, '')"><img src="<?php echo $purl; ?>images/arw.jpg" alt="" /> Back</a>
		</div>
	</div>
</div>
		
