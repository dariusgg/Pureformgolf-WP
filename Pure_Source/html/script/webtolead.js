function submit_form(){
 	if(typeof(validateCaptchaAndSubmit)!='undefined'){
 		validateCaptchaAndSubmit();
 	}else{
 		check_webtolead_fields();
 	}
 }
 function check_webtolead_fields(){
     if(document.getElementById('bool_id') != null){
        var reqs=document.getElementById('bool_id').value;
        bools = reqs.substring(0,reqs.lastIndexOf(';'));
        var bool_fields = new Array();
        var bool_fields = bools.split(';');
        nbr_fields = bool_fields.length;
        for(var i=0;i<nbr_fields;i++){
          if(document.getElementById(bool_fields[i]).value == 'on'){
             document.getElementById(bool_fields[i]).value = 1;
          }
          else{
             document.getElementById(bool_fields[i]).value = 0;
          }
        }
      }
    if(document.getElementById('req_id') != null){
        var reqs=document.getElementById('req_id').value;
        reqs = reqs.substring(0,reqs.lastIndexOf(';'));
        var req_fields = new Array();
        var req_fields = reqs.split(';');
        nbr_fields = req_fields.length;
        var req = true;
        for(var i=0;i<nbr_fields;i++){
          if(document.getElementById(req_fields[i]).value.length <=0 || document.getElementById(req_fields[i]).value==0){
           req = false;
           break;
          }
        }
        if(req){
            document.WebToLeadForm.submit();
           jQuery('.spu-close').click();
            return true;
        }
        else{
          alert('Please provide all the required fields');
          return false;
         }
        return false
   }
   else{
    document.WebToLeadForm.submit();
    jQuery(".spu-close").click();
   }
}
function validateEmailAdd(){
	if(document.getElementById('email1') && document.getElementById('email1').value.length >0) {
		if(document.getElementById('email1').value.match(/^\w+(['\.\-\+]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,})+$/) == null){
		  alert('Not a valid email address');
		}
	}
	if(document.getElementById('email2') && document.getElementById('email2').value.length >0) {
		if(document.getElementById('email2').value.match(/^\w+(['\.\-\+]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,})+$/) == null){
		  alert('Not a valid email address');
		}
	}
}