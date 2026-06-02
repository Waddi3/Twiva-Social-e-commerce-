function  login(json) {
    __ajax_http("login/influencer", json, { 'Accept': 'application/json' }, AJAX_CONF.apiType.POST, "POST LOGIN", __success_login);
}


function forgetpass(json){

    __ajaxregister_http("forgot-password/otp", json, headers(), AJAX_CONF.apiType.POST, "", __success_forget);
       
}

function otpverify(json){

    __ajaxregister_http("forgot-password/Otp-verify", json, headers(), AJAX_CONF.apiType.POST, "POST LOGIN", __success_otp_verify);
       
}

function resetPassword(json) {
    __ajax_http("forget_password", json, { 'Accept': 'application/json' }, AJAX_CONF.apiType.POST, "POST LOGIN", __success_update_password);
}
function chnagepass(json){

    __ajaxregister_http("forgot-password/verify/otp", json, headers(), AJAX_CONF.apiType.POST, "POST LOGIN", __success_pass_change);
       
}

function resetpassword(){

var json = {
"email":localStorage.getItem(STORAGE.email),
"otp_id":localStorage.getItem(STORAGE.otp_id),
"password": $("#password").val(),
"c_password": $("#c_password").val(),

};
chnagepass(json);
}

function __success_pass_change(response){

    console.log(response);
    goto(UI_URL.login);

}

function __success_update_password(response){
    if(response.code=200){
        $('#resetpasswordError').removeClass('hide');
        $('#resetpasswordError').html(response.message); 
        setTimeout(function() {
            goto(UI_URL.login);
          }, 2000);
    }
}

function valiateLogin() {


    var json = {

     "email": $("#email").val(),
        "password": $("#password").val(),
        "device_type": "3",
        "device_token": "sadsadasd",
       
};

            login(json);

}


//forget password 
function forget_password(){
    var json = 
    {
      "email": $("#email").val(),
    };
    forgetpass(json);
}

function __success_forget(response){
    console.log(response.user.email);
    STORAGE.set(STORAGE.email, response.user.email);
    STORAGE.set(STORAGE.otp_id, response.user.otp_id);
    goto(UI_URL.onboarding5);
    
}



function __success_login( response) {
   if (response.status == false) {
        if(response.error){
             $("#emailerr").show().css("color","red").html(response.error);
        }
       
     return; }
      

    __save_identity(response);
       
     }

function __save_identity(response) {
    identity = response.data;
    console.log(response);

    STORAGE.set(STORAGE.accesstoken, response.token);
    STORAGE.set(STORAGE.userId, response.data.id);
    STORAGE.set(STORAGE.name, response.data.bussiness_name);
    STORAGE.set(STORAGE.logo, response.data.logo);
     setTimeout(function() {
            goto(UI_URL.onboarding4);
          }, 100);
    // STORAGE.set(STORAGE.name, response.user.name);
    // STORAGE.set(STORAGE.role, response.user.role);
    // STORAGE.set(STORAGE.roleType, response.user.role);
    // STORAGE.set(STORAGE.image, response.user.image);
    // STORAGE.set(STORAGE.camefrom, response.user.from);
    // STORAGE.set(STORAGE.username, response.user.username);
}

function verify_otp(){

var json = {
"email":localStorage.getItem(STORAGE.email),
"otp_id":localStorage.getItem(STORAGE.otp_id),
"otp": $("#otp").val(),

};
otpverify(json);
}

function __success_otp_verify(response){
    console.log(response);
    goto(UI_URL.onboarding6);
}


//validation 
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
            $('#usercheck').html("Please enter your email");
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
        var email_val = $('#password').val();
        if(email_val.length == ''){
            $('#passcheck').show();
            $('#passcheck').html("Please enter the password");
            $('#passcheck').focus();
            $('#passcheck').css("color","red");
            user_err = false;
            return false;
        


        }
        else{
            $('#passcheck').hide();

        }

    }

    $('#submitbtn').click(function(){
         user_err = true;
         pass_err = true;
        email_check();
        pass_check();
        if((user_err == true) && (pass_err == true) ){
            valiateLogin();
        }
        else{
            return false;
        }



    })

});