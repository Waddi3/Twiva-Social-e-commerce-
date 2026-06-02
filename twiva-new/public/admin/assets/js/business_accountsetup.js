function SetupRegister(json) {

    __ajaxregister_http("register/influencer", json, headers(), AJAX_CONF.apiType.POST, "", __success_setup);

}

function state(country_id) {

    __ajax_http("state/select?country_id=" + country_id, "", {'Accept': 'application/json'}, AJAX_CONF.apiType.GET, "", sucessstate);

}

function city(state_id) {

    __ajaxregister_http("city/select?state_id=" + state_id, "", {'Accept': 'application/json'}, AJAX_CONF.apiType.GET, "POST LOGIN", sucesscity);

}


function verifyotp(json) {

    __ajaxregister_http("verify/otp", json, headers(), AJAX_CONF.apiType.POST, "", __success_otp);

}


function logoUpload(file) {
    var formData = new FormData();

    formData.append('image', file[0]);


    __ajax_http_upload("upload-image", formData, headers(), AJAX_CONF.apiType.POST, "", __success_logo);


}


//verify email otp
function verify_otp() {
    var json = {
        "email": STORAGE.get(STORAGE.email),
        "device_token": "jdbfjvdsj",
        "device_type": "3",
        "otp": $("#otp").val(),

    };
    verifyotp(json);
}

function __success_otp(response) {
    console.log(response);
    if (response.status == true) {
        $('#otpsuc').show().html(response.message);
        STORAGE.set(STORAGE.accesstoken, response.token);
        STORAGE.set(STORAGE.userId, response.id);
        setTimeout(function () {
            goto(UI_URL.onboarding3);
        }, 100);
    } else (response.status == false)
    {
        $('#otperr').show().html(response.message);
    }

}


//logo upload

function logoUpload1(input) {
    var json =
        {


            "image": $("#logo").val(),
        };
    if (input.files && input.files[0]) {
        logoUpload(input.files);

    }
}

function __success_logo(response) {
    // console.log(response.image);
    if (response.code == 200) {


        $("#message").show().html(response.message);

    }

    STORAGE.set(STORAGE.image, response.image);


}


//state fetch

function state1() {

    var country = document.getElementById('country').value;
    var country_id = country;
    // alert(`country_id ${country_id}`);
    state(country_id);

}

function sucessstate(response) {
    $('#state').html('');
    $(response.states).each(function (index, key) {
        $('#state').append(`
             <option value="${key.id}">${key.name}</option>`
        )
    })
}

//city fetch

function city1() {
    var state = document.getElementById('state').value;
    var state_id = state;
    city(state_id);
}


function sucesscity(response) {
    $('#city').html('');
    $(response.cities).each(function (index, key) {
        $('#city').append(`
             <option value="${key.id}">${key.name}</option>`
        )
    })

}


function AccountSetup() {
    var json =
        {
            "email": STORAGE.get(STORAGE.email),
            "password": STORAGE.get(STORAGE.password),
            "referal_code": STORAGE.get(STORAGE.referal_code),
            "type": "1",
            "comp_busi_name": $("#business").val(),
            "comp_contact_name": $("#name").val(),
            "comp_busi_number": $("#mobile").val(),
            "comp_description": $("#description").val(),
            "comp_logo": STORAGE.get(STORAGE.image),
            "comp_email": $("#email").val(),
            "comp_country": $("#country").val(),
            "comp_state": $("#state").val(),
            "comp_city": $("#city").val(),
            "comp_pincode": $("#pincode").val(),
            "comp_area": $("#area").val(),
            "comp_building": $("#building").val(),
        };
    console.log(json);
    SetupRegister(json);
}


function __success_setup(response) {
    STORAGE.set(STORAGE.otp, response.otp);


    if (response.status == true) {
        setTimeout(function () {
            goto(UI_URL.onboarding2);
        }, 100);
    } else (response.status == false)
    {
        $("#usercheck").show().css("color", "red").html("Email is already exist");
    }

}


//validations
$(document).ready(function () {
    $('#businesscheck').hide();
    $('#namecheck').hide();
    $('#emailcheck').hide();
    $('#mobilecheck').hide();
    $('#countrycheck').hide();
    $('#pincodecheck').hide();
    $('#citycheck').hide();
    $('#statecheck').hide();
    $('#areacheck').hide();
    $('#buildingcheck').hide();

    var business_err = true;
    var name_err = true;
    var email_err = true;
    var mobile_err = true;
    var description_err = true;
    var country_err = true;
    var pincode_err = true;
    var city_err = true;
    var area_err = true;
    var building_err = true;

    $('#business').keyup(function () {
        business_check();

    });

//Business name validation

    function business_check() {
        var business_val = $('#business').val();
        if (business_val.length == '') {
            $('#businesscheck').show();
            $('#businesscheck').html("Please fill the business name");
            $('#businesscheck').focus();
            $('#businesscheck').css("color", "red");
            business_err = false;
            return false;


        } else {
            $('#businesscheck').hide();

        }

    }

//name validation
    $('#name').keyup(function () {
        name_check();

    });

    function name_check() {
        var business_val = $('#name').val();
        if (business_val.length == '') {
            $('#namecheck').show();
            $('#namecheck').html("Please fill the name");
            $('#namecheck').focus();
            $('#namecheck').css("color", "red");
            name_err = false;
            return false;


        } else {
            $('#namecheck').hide();

        }

    }


    //email validation
    $('#email').keyup(function () {
        email_check();

    });

    function email_check() {
        var email_val = $('#email').val();
        if (email_val.length == '') {
            $('#emailcheck').show();
            $('#emailcheck').html("Please fill the email");
            $('#emailcheck').focus();
            $('#emailcheck').css("color", "red");
            email_err = false;
            return false;


        } else {
            $('#emailcheck').hide();

        }

    }


    //mobile validation
    $('#mobile').keyup(function () {
        mobile_check();

    });

    function mobile_check() {
        var mobile_val = $('#mobile').val();
        if (mobile_val.length == '') {
            $('#mobilecheck').show();
            $('#mobilecheck').html("Please fill the mobile number");
            $('#mobilecheck').focus();
            $('#mobilecheck').css("color", "red");
            mobile_err = false;
            return false;


        } else {
            $('#mobilecheck').hide();

        }

    }


    //description validation
    $('#description').keyup(function () {
        description_check();

    });

    function description_check() {
        var description_val = $('#description').val();
        if (description_val.length == '') {
            $('#descriptioncheck').show();
            $('#descriptioncheck').html("Please fill the description number");
            $('#descriptioncheck').focus();
            $('#descriptioncheck').css("color", "red");
            description_err = false;
            return false;


        } else {
            $('#descriptioncheck').hide();

        }

    }


    $('#country').keyup(function () {
        country_check();

    });

//Business name validation

    function country_check() {
        var country_val = $('#country').val();
        if (country_val.length == '') {
            $('#countrycheck').show();
            $('#countrycheck').html("Please fill the country name");
            $('#countrycheck').focus();
            $('#countrycheck').css("color", "red");
            country_err = false;
            return false;


        } else {
            $('#countrycheck').hide();

        }

    }


    $('#pincode').keyup(function () {
        pincode_check();

    });

//Business name validation

    function pincode_check() {
        var pincode_val = $('#pincode').val();
        if (pincode_val.length == '') {
            $('#pincodecheck').show();
            $('#pincodecheck').html("Please fill the pincode name");
            $('#pincodecheck').focus();
            $('#pincodecheck').css("color", "red");
            pincode_err = false;
            return false;


        } else {
            $('#pincodecheck').hide();

        }

    }

    $('#area').keyup(function () {
        area_check();

    });

//Business name validation

    function area_check() {
        var area_val = $('#area').val();
        if (area_val.length == '') {
            $('#areacheck').show();
            $('#areacheck').html("Please fill the area name");
            $('#areacheck').focus();
            $('#areacheck').css("color", "red");
            area_err = false;
            return false;


        } else {
            $('#areacheck').hide();

        }

    }


    $('#building').keyup(function () {
        building_check();

    });

//Business name validation

    function building_check() {
        var building_val = $('#building').val();
        if (building_val.length == '') {
            $('#buildingcheck').show();
            $('#buildingcheck').html("Please fill the building name");
            $('#buildingcheck').focus();
            $('#buildingcheck').css("color", "red");
            building_err = false;
            return false;


        } else {
            $('#buildingcheck').hide();

        }

    }

    $('#submitbtn').click(function () {
        $("#s1").removeClass('active');
        $("#s2").addClass('active');


        business_err = true;
        name_err = true;
        email_err = true;
        mobile_err = true;
        description_err = true;
        // country_err = true;
        // pincode_err = true;
        // area_err = true;
        // building_err = true;

        business_check();
        name_check();
        email_check();
        mobile_check();
        description_check();
        // country_check();
        // pincode_check();
        // area_check();
        // building_check();
        if ((business_err == true) && (name_err == true) && (email_err == true) && (mobile_err == true) && (description_err == true)) {
            $("#step1").hide();
            $("#step2").show();
        } else {
            return false;
        }


    })


});


fetch('http://twiva-api.apparrant.com/api/v1/country/list').then((apidata) => {
    return apidata.json();
}).then((actualdata) => {
    $(actualdata.countries).each(function (index, key) {
        $('#country').append(`
             <option value="${key.id}">${key.name}</option>`
        )
    })

})
    .catch((error) => {
        console.log(error);
    })
