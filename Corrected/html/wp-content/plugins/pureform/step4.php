<div class="frm-wrap" id="div_step4" style="display:none;">
	<div class="frm_sec">
		<div class="top_line">
			<img src="<?php echo $purl; ?>images/step4_brdr.png" alt="" class="line4"/>
		</div>
		<div class="in_sec_in">
			<h3>When is the best time for you?</h3>
			<ul>
				<li class="big_list"><input type="text" class="datepicker" name="pure_date" id="pure_date" data_msg="Please select date." readonly placeholder="Select date" value=""/>
				</li>
				<li><a href="javascript:void(0)" class="active_time" onClick="settime( this )" data_val="AM"><img src="<?php echo $purl; ?>images/pic1.png" alt=""/> AM</a></li>
				<li><a href="javascript:void(0)" onClick="settime( this )" data_val="PM"><img src="<?php echo $purl; ?>images/pic2.png" alt=""/> PM</a></li>
				
			</ul>
			<div id="msg_pure_date" class="error"></div>
			
		</div>
		<div class="note_sec">
			<a href="javascript:void(0)" onClick="show_form(4, 3, '')" data_val=""><img src="<?php echo $purl; ?>images/arw.jpg" alt="" /> Back</a>
			
		</div>
		<div class="next_btn">
			<input type="button" onClick="show_form(4, 5, this)" data_val="" name="" value="Next"/>
		</div>
	</div>
</div>
		