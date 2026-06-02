//check email

function check_email(json){

    __ajax_http("email/status", json, { 'Accept': 'application/json' }, AJAX_CONF.apiType.POST, "", __success_email);      
}


function __success_email(response){
	if(response.status == true)

	{
			valiateRegister();

			
	}
	else
	{
	
		$("#usercheck").show().css("color","red").html("Email is already exist");
	}

	
}


function emailcheck()
{
	var json = {
		 "email": $("#email").val()
	};
	check_email(json);
}



function valiateRegister() {


    var json = 
        {
      
        "email": $("#email").val(),
        "password": $("#password").val(),
        "referal_code": $("#referral").val(),
    };


    STORAGE.set(STORAGE.email, json.email);
    STORAGE.set(STORAGE.password, json.password);
    STORAGE.set(STORAGE.referral, json.referal_code);
   setTimeout(function() {
            goto(UI_URL.onboarding1);
          }, 100);

}










//validattions

$(document).ready(function(){
	$('#usercheck').hide();
	$('#passcheck').hide();

	var user_err = true;
	var pass_err = true;

	$('#email').keyup(function(){
		email_check();
		
	});

	function email_check(){
		var email_val = $('#email').val();
		if(email_val.length == ''){
			$('#usercheck').show();
			$('#usercheck').html("Please fill the email");
			$('#usercheck').focus();
			$('#usercheck').css("color","red");
			user_err = false;
			return false;



		}
		else{
			$('#usercheck').hide();

		}

	}

	$('#password').keyup(function(){
		pass_check();
		
	});

	function pass_check(){
		var pass_val = $('#password').val();
		if(pass_val.length == '' && pass_val.length>8){
			$('#passcheck').show();
			$('#passcheck').html("Password should be minimum 8 characters");
			$('#passcheck').focus();
			$('#passcheck').css("color","red");

			pass_err = false;
			return false;




		}
		else{
			$('#passcheck').hide();

		}

	}

	$('#submitbtn').click(function(){
		var pass_val = $('#password').val();
	 if(pass_val.length>8)
         {
		 user_err = true;
	 	 pass_err = true;
		email_check();
		pass_check();
		if((user_err == true) && (pass_err == true) ){
			emailcheck();
		}
		else{
			return false;
		}
	}
	else{
		 $("#passcheck").show().css("color","red").html("Password should be minimum 8 characters");
		var email_val = $('#email').val();
		 if(email_val.length == ''){
			$("#usercheck").show().css("color","red").html("Please enter your email");
		}
	}



	})

});