var responseModal = "response_modal";


function  login(json) {
    __ajax_http("login", json, { 'Accept': 'application/json' }, AJAX_CONF.apiType.POST, "POST LOGIN", __success_login);
}

function loginFB(json) {
    __ajax_http("login/facebook", json, { 'Accept': 'application/json' }, AJAX_CONF.apiType.POST, "POST LOGIN", __success_login);
}

function loginGoogle(json) {
    __ajax_http("login/google", json, { 'Accept': 'application/json' }, AJAX_CONF.apiType.POST, "POST LOGIN", __success_login);
}

function register(json){

    __ajax_http("register", json, { 'Accept': 'application/json' }, AJAX_CONF.apiType.POST, "POST LOGIN", __success_register);      
}
function AddProduct(json){

    __ajax_httpproduct("add-product", json, headers(), AJAX_CONF.apiType.POST, "POST LOGIN", __success_product);      
}

function SetupRegister(json){

    __ajaxregister_http("company-information", json, headers(), AJAX_CONF.apiType.POST, "POST LOGIN", __success_setup);
       
}
function imageupload(json){

    __ajax_http_upload("add-image", json, headers(), AJAX_CONF.apiType.POST, __success_image);
  
       
}





function _sendOTP(email, classname,from) {
    __ajax_http("get_otp?param="+email+"&from="+from, "", { 'Accept': 'application/json' }, AJAX_CONF.apiType.GET, "POST LOGIN", __success_sendOTP);
}

function checkUserExists(email, classname) {
    __ajax_http("checkUserExists?param="+email, "", { 'Accept': 'application/json' }, AJAX_CONF.apiType.GET, "POST LOGIN", __success_checkUserExists);
}

function resetPassword(json) {
    __ajax_http("forget_password", json, { 'Accept': 'application/json' }, AJAX_CONF.apiType.POST, "POST LOGIN", __success_update_password);
}



function loginSubmit(e){

    if (e.keyCode == 13) {
        valiateLogin();
        return false;
    }
}



function signupSubmit(e){
	// console.log(e);
	console.log("onclick");
	e.preventDefault();
        alert("here");
	return;


	
    $("#signupError").addClass('hide');
    if (e.keyCode == 13) {
        sendOTP('otp_panel')
        return false;
    }
}



// function forgotPassword() {
//     var json = {
//         "identifier": $("#identifier_").val(),
//         "identifierKey": EmailOrPhoneType($("#identifier_")),
//         "countryCode": COUNTRY_CODE
//     }

//     if (!validateEmail($("#identifier_")[0])) {
//         isError = true;
//     } else {
//         isError = false;
//     }

//     if(isError){return;}

//     __ajax_http("forgot/password", json, { 'Accept': 'application/json' }, AJAX_CONF.apiType.POST, "POST LOGIN", __success_forgotOTP);
    
// }

// function forgotPasswordVerify(json) {
//     __ajax_http("forgot/password/verify", json, { 'Accept': 'application/json' }, AJAX_CONF.apiType.POST, "POST LOGIN", __success_forgotPasswordVerify);
// }





//login check
function login_check() {
    console.log("here");
    console.log(localStorage.getItem(STORAGE.accesstoken));
    if (localStorage.getItem(STORAGE.accesstoken) == null) {
            goto(UI_URL.login);
        }
        return false;
    }


    function logout_check() {
    localStorage.clear();
    login_check();
}

function setPassCode(_id){
    var json = {
        "identifier":$("#"+_id).val(),
        "passCode":$("#passCode").val(),
        "cpassCode":$("#cpassCode").val(),
        "identifierKey":EmailOrPhoneType($("#"+_id))
    };

    if (json.passCode != json.cpassCode) { return; }

    __ajax_http("login/setPassCode", json, headers(), AJAX_CONF.apiType.POST, "POST LOGIN", __success_setPassCode);
}


function updateProProfile(){
    var json = {
        "phone":$("#prophone").val(),
    };

    if(isError){
        return;
    }

    __ajax_http("users/" + STORAGE.get(STORAGE.userId), json, headers(), AJAX_CONF.apiType.PUT, "PUT PROFILE", invokeResponseModal);
}

function __success_setPassCode(response) {

    if (response.status != 200) { return; }

    if(responseModal){
        if(STORAGE.get(STORAGE.roleType)=="3"){
            showPanel("create-phone_panel");
        }else{
            $("#"+responseModal).modal();
        }
        
    }else{
        redirectToHomePageForgot();
    }

}

function __success_otp(){
 
    if($("#txtOtp").val() != "1234"){
        $("#errOtp").removeClass('hide');
        $("#otp-error").addClass("error-field");

    }else{
        $("#otp_panel").addClass('hide');
        $("#password_box").removeClass('hide');
    
    }    
}




function invokeResponseModal(){
    $("#"+responseModal).modal(); 
}

function redirectToHomePageRegister(){
    if (STORAGE.get(STORAGE.roleType) == "3") {
        goto(UI_URL.addTask);
    } else {
        goto(UI_URL.root);
    }
}

function redirectToHomePageForgot(){
    if (STORAGE.get(STORAGE.roleType) == "3") {
        goto(UI_URL.rootPro);
    } else {
        goto(UI_URL.root);
    }
}

function __success_forgotPasswordVerify(response) {
    if (response.status == 403) {
        __toaster.error("Forgot", response.errorMessage);
        return;
    }
    __save_identity(response);
    showPanel("reset-password_panel");
    //responseModal = "response_modal_pwd";
    responseModal = null;
}

function __success_forgotOTP(response) {
    if (response.status == 403) {
        //showPanel('login_panel');
        __toaster.error("Login", response.errorMessage);
        return;
    }
    showPanel('otp_panel');
}

function __success_sendOTP(response) {
    if (response.code == 400) {
        showPanel('signup_panel');
        $("#signupError").text(response.message);
        $("#signupError").removeClass('hide');
        // $("#signupError").addClass("hide");
        $("#email-error").addClass("error-field");
       // $("#accountExistError").text('Please enter valid email');

    } else {
        
                $("#signupError").addClass("hide");
                showOtpPanel();
        
       
    }
}

function __save_identity(response) {
    identity = response.data;
    console.log(response.token);

    STORAGE.set(STORAGE.accesstoken, response.token);
    STORAGE.set(STORAGE.userId, response.user.id);
    STORAGE.set(STORAGE.name, response.user.name);
    STORAGE.set(STORAGE.role, response.user.role);
    STORAGE.set(STORAGE.roleType, response.user.role);
    STORAGE.set(STORAGE.image, response.user.image);
    STORAGE.set(STORAGE.camefrom, response.user.from);
    STORAGE.set(STORAGE.username, response.user.username);
}

function __save_identity_signup(response) {
    identity = response.data;
    console.log(response);
    STORAGE.set(STORAGE.accesstoken, response.token);
    STORAGE.set(STORAGE.userId, response.user.id);
    if(response.user.name==null){
        STORAGE.set(STORAGE.name, response.user.email);
    }
    STORAGE.set(STORAGE.name, response.user.name);
 
}

function __success_login( response) {
   if (response.status != 200) {
        if(response.message){
             $("#emailerr").show().css("color","red").html(response.message);
        }
       
     return; }
      

    __save_identity(response);

    console.log();
    if(response.user.profile_completed == "1"){
        setTimeout(function() {
            goto(UI_URL.onboarding4);
          }, 100);
     }else{
        setTimeout(function() {
            goto(UI_URL.onboarding1);
          }, 100);
     }

}

function __success_google_login(response) {
    if (response.code != 200) { 
        $("#login-input1").addClass("error-field");
        $("#login-input2").addClass("error-field");
        $("#loginError").removeClass("hide");
        $("#loginError").html("You have entered wrong credentials.");
        return;
     }

    __save_identity(response);
     if(response.user.role == null || response.user.role == "null"){
        setTimeout(function() {
            goto(UI_URL.onboarding1);
          }, 100);
     }else{
        setTimeout(function() {
            goto(UI_URL.dashboard);
          }, 100);
     }
    

}

function __success_register(response) {

  
    if (response.status != 200) {
        if(response.error.email){
             $("#usercheck").show().css("color","red").html(response.error.email);
        }
       
     return; }
          valiateLogin();
    setTimeout(function() {
    
   __save_identity_signup(response);
         // goto(UI_URL.onboarding1);
      }, 1000);
}




function __success_product(response) {

  
    if (response.status != 200) {
        if(response.error.email){
             $("#usercheck").show().css("color","red").html(response.error.email);
        }
       
     return; }
     console.log("sucesss");
     alert("product Added");
    // setTimeout(function() {
         // goto(UI_URL.onboarding1);
      // }, 1000);
}


function __success_image(response) {
    console.log("chal gya");
    AccountSetup();

}

function __success_setup(response) {
    goto(UI_URL.onboarding2);
    if (response.status != 200) {
        if(response.error.email){
             $("#usercheck").show().css("color","red").html(response.error.email);
        }
       
     return; }
  
        
}


function valiateForgot() {
    
    var type = EmailOrPhoneType($("#identifier_0"))
    var json = {
        "param": $("#identifier_0").val(),
    }

    if(json.param.length !=0){

    if (!eitherValidateEmailOrPhone($("#identifier_0"))) 
    {
        $("#login-input3").addClass("error-field");
        $("#forgotError").removeClass("hide");
        isError = true;} 
    else {isError = false;}
    }else{
        $("#forgotError").removeClass("hide");
        $("#login-input3").addClass("error-field");
        $("#forgotError").text("*Please enter either valid Email or phone number ");
    }
    


    // if (eitherValidateEmailOrPhone($("#identifier_0"))) {
    //     $("#login-input3").addClass("error-field");
    //     $("#loginError").removeClass("hide");
    //     isError = true;
    // } else {
    //     isError = false;
    // }

    if (!isError) {
        checkUserExists($("#identifier_0").val());
    }

}


 //image upload

 function image_upload() {
    var json = 
        {

        
        "image": $("#logo").val(),
        "type":"2",
    };
        imageupload(json);
        console.log(json);
}

//register

function valiateRegister() {
    var json = 
        {
      
        "email": $("#email").val(),
        "password": $("#password").val(),
        "referal_code": $("#referral").val(),
        "type":"Business User",
    };

    // if (!isError) {
        register(json);
console.log("here");

 // }
}

function valiateLogin() {


    var json = {

     "email": $("#email").val(),
        "password": $("#password").val(),

};

            login(json);

}

 function AccountSetup() {
    var json = 
        {
            "id": STORAGE.get(STORAGE.userId),
        "busi_name": $("#business").val(),
        "contact_name": $("#name").val(),
        "busi_number": $("#mobile").val(),
        "description": $("#description").val(),
        "logo": "test.png",
        "comp_email": $("#email").val(),
        "country": $("#country").val(),
        "state": $("#state").val(),
        "city": $("#city").val(),
        "area": $("#area").val(),
        "building": $("#building").val(),
    };
console.log(json);
    // if (!isError) {
        SetupRegister(json);
console.log("here");

 // }
}




 function addproduct() {
    var json = 
        {"name":$("#title").val(),
        "price":$("#price").val(),
        "category":$("#category").val(),
        "stock":$("#stock").val(),
        "size":$("#size").val(),
        "color":$("#color").val(),
        "sku":$("#specification").val(),
        "main_image":$("#image").val(),
        "description":"description"};
        AddProduct(json);
}

function sendOTP(classname) {
    var json = {
        "param": $("#identifier").val(),
    }

    if(json.param.length !=0){
        if (!eitherValidateEmailOrPhone($("#identifier"))) 
        {isError = true;} 
        else {isError = false;}
    }else{
        $("#signupError").removeClass("hide");
        $("#email-error").addClass("error-field");
        $("#signupError").text("*Please enter either valid Email or phone number ");
    }
    if (!isError) {
        _sendOTP($("#identifier").val(), classname,$('.from').val());
    }
}


function showPanel(classname) {
    $(".showPanel").addClass('hide');
    $("." + classname).removeClass('hide');
}

function showSignupEMail() {
    $("#signup").addClass('hide');
    $("#signup-email" ).removeClass('hide');
}

function showOtpPanel() {

    $("#otp_panel").removeClass('hide');
    $("#signup-identifire" ).addClass('hide');
}

function validateSignup(){
    var json = {
        "password": $("#password").val(),
        "oldPassword": $("#oldPassword").val(),
        "confirmPassword": $("#confirmPassword").val()
    };

    if (json.oldPassword == "") { return; }
    if (json.password != json.confirmPassword) { return; }
}

function __success_checkUserExists(response){
    if (response.code != 200) { 
        $("#login-input3").addClass("error-field");
        $("#forgotError").removeClass("hide");
        $("#forgotError").html(response.message);
        return;
     }
     $("#otp_panel_login").removeClass('hide');
     $("#login-forgot").addClass('hide');
}

function forgotpassword(){
    $("#login-email").addClass("hide");
    $("#login-forgot").removeClass("hide");
}

function _success_banner(response){
    var content  = __json.parse(response.data.content);
    $(".login").css('background-image','url('+MEDIA_URL+content.login+')')
}

function __success_otp_forgot(){
    if($("#txtOtp").val()!="1234"){
        $("#login-input4").addClass("error-field");
        $("#otpError").removeClass('hide');
        return ;
    }

    $("#login_password").removeClass("hide");
    $("#otp_panel_login").addClass("hide");

} 

function validateReset(){
    var passwordJson = {
        "password": $("#reset_password").val(),
        "confirmPassword": $("#confirm_reset_password").val()
    };

    if(passwordJson.password.length>0 && passwordJson.confirmPassword.length>0){
        validatePassword()
        if (passwordJson.password != passwordJson.confirmPassword) { 
        $('#resetpasswordError').removeClass('hide');
        $("#input-field5").addClass("error-field");
        $("#input-field6").addClass("error-field");
        $('#resetpasswordError').html('Please adhere to password guidelines'); 
         return; }

        var json = {
            "param": $("#identifier_0").val(),
            "otp": $("#txtOtp").val(),
            "password": $("#reset_password").val(),
        };
        resetPassword(json);
    }
    else{
        $('#resetpasswordError').removeClass('hide');
        $("#input-field5").addClass("error-field");
        $("#input-field6").addClass("error-field");
        $('#resetpasswordError').html('Please adhere to password guidelines'); 
        return; 
    }    
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

window.addEventListener('DOMContentLoaded', (event) => {
    // STORAGE.clear();
    $( "#identifier_0" ).keypress(function() {
        $("#forgotError").addClass("hide");
      });

   // cms("BANNER",_success_banner)
});
