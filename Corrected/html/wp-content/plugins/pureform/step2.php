
<div class="frm-wrap" id="div_step2" style="display:none;">
	<div class="frm_sec">
		<div class="top_line">
			<img src="<?php echo $purl; ?>images/step2_brdr.png" alt="" class="line2"/>
		</div>
		<h3>Select the club(s) for your fitting</h3>
		<!--
		<div class="selct_bx">
				<a href="javascript:void(0)" id="ultimate_full_clubs" class="" onClick="set_all_club(this)" data_val="Ultimat Full Bag Fitting" >
					<img src="<?php echo $purl; ?>images/select_item1.png" alt="" /> Ultimat Full Bag Fitting
				</a>
			</div>
			<div class="clear"></div>
			-->
		<div class="select_club_sec">
		
			<div class="selct_bx">
				<a href="javascript:void(0)" class="p_club" onClick="set_club(2, 3, this)" data_val="Driver">
					<img src="<?php echo $purl; ?>images/select_item1.png" alt="" /> Drivers
				</a>
			</div>
			<div class="selct_bx">
				<a href="javascript:void(0)"  class="p_club" onClick="set_club(2, 3, this)" data_val="Irons">
					<img src="<?php echo $purl; ?>images/select_item2.png" alt="" /> Irons
				</a>
			</div>
			<div class="selct_bx">
				<a href="javascript:void(0)" class="p_club" onClick="set_club(2, 3, this)" data_val="Woods">
					<img src="<?php echo $purl; ?>images/select_item3.png" alt="" /> Woods
				</a>
			</div>
			<div class="selct_bx">
				<a href="javascript:void(0)" class="p_club" onClick="set_club(2, 3, this)" data_val="Hybrids">
					<img src="<?php echo $purl; ?>images/select_item4.png" alt="" /> Hybrids
				</a>
			</div>
			<div class="selct_bx">
				<a href="javascript:void(0)" class="p_club" onClick="set_club(2, 3, this)" data_val="Wedges">
					<img src="<?php echo $purl; ?>images/select_item5.png" alt="" /> Wedges
				</a>
			</div>
			<div class="selct_bx">
				<a href="javascript:void(0)" class="p_club" onClick="set_club(2, 3, this)" data_val="Putters">
					<img src="<?php echo $purl; ?>images/select_item6.png" alt="" /> Putters
				</a>
			</div> 
		</div>
		<div class="note_sec">
			<a href="javascript:void(0)" onClick="show_form(2, 1)"  id="step2_back" ><img src="<?php echo $purl; ?>images/arw.jpg" alt="" /> Back</a>
		</div>
		<div class="next_btn">
			<input type="button" onClick="show_form(2, 3, 'next')" data_val="" name="" value="Next"/>
		</div>
	</div>
</div>
		
		<script>
		function set_club(obj, obj1, obj2)
		{
			var clubs = "";
			/*
			if($("#ultimate_full_clubs").hasClass('data_selected'))
			{
				return false;
			}
			*/
			//var club_arra = clubs.split("::");
			var action_done = 0;
			if(jQuery(obj2).hasClass('data_selected') )
			{
				jQuery(obj2).removeClass('data_selected') ;
			}
			else{
				jQuery(obj2).addClass('data_selected') ;
			}	

			jQuery(".data_selected").each(function(){
				clubs = clubs+jQuery(this).attr('data_val')+'::';
			})
			
			/*
			if(club_arra.length >1)
			{
				
				for(var i=0; i<club_arra.length; i++)
				{
				
					if(jQuery.trim(club_arra[i]) == jQuery.trim(jQuery(obj2).attr('data_val')))
					{
						var index = array.indexOf(club_arra[i]);
						alert(jQuery.trim(club_arra[i]) +"  "+ jQuery.trim(jQuery(obj2).attr('data_val')));
						
						club_arra.splice(index, 1);
						action_done = 1
					}
				}
				if(action_done == 0)
				{
					clubs = clubs+jQuery(obj2).attr('data_val')+'::';
				}
				else{
					 clubs = club_arra.join("::");
				}
			}
			else{
				clubs = jQuery(obj2).attr('data_val')+'::';
			}
		*/
			jQuery("#pure_club").val(clubs);
		}


function set_all_club(obj)
{

	var clubs = "";
	if(jQuery(obj).hasClass('ultimate_full'))
	{
		jQuery(obj).removeClass('ultimate_full');
		jQuery(obj).removeClass('data_selected') ;
	}
	else
	{
		clubs = jQuery(obj).attr('data_val');
		jQuery(obj).addClass('ultimate_full');
		jQuery(obj).addClass('data_selected') ;
	}

	jQuery("#pure_club").val(clubs);
}
		</script>