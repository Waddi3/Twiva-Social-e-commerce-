var API_URL = "https://api.twiva.co.ke/v1/";
// var API_URL = "https://api.twiva.test/v1/";
var MEDIA_URL = "";
var APP_URL = "/twiva-common/business";
var CURRENCY = "";
var COUNTRY_CODE = "";
var identity = {};

var api = {};

var isError = true;
var ADMIN_LOCK = 2;

const AJAX_CONF = {
    contentType: "application/json",
    contentTypeForm: "multipart/form-data",
    dataType: "json",
    timeout: 10000,
    OK: 200,
    apiType: {
        POST: "POST",
        GET: "GET",
        DELETE: "DELETE",
        PUT: "PUT",
        PATCH: "PATCH"
    }
};

const LOADER = {
    show: function () {
        //document.getElementById("postLoader").style.display = "flex";
    },
    hide: function () {
        //document.getElementById("postLoader").style.display = "none";
    }
}


const UI_URL = {
    root: APP_URL + "/index",
    dashboard: APP_URL + '/home',
    login: "/twiva-common/login.php",
    onboarding1: APP_URL + "/account-setup.php",
    onboarding2: APP_URL + "/verify_emailotp.php",
    onboarding3: APP_URL + "/welcome.php",
    onboarding4: APP_URL + "/dashboard-2.php",
    onboarding5: APP_URL + "/otp_verify.php",
    onboarding6: APP_URL + "/reset-password.php",
    onboarding7: APP_URL + "/my-catalog-2.php",
    onboarding8: APP_URL + "/product-detail.php",
    campaigns: APP_URL + "/campaigns",
    campaign: APP_URL + "/campaign",
    sponsor: APP_URL + "/sponsor-details",
    sponsee: APP_URL + "/sponsee-details",
    rootPro: APP_URL + "/professional/home",
    addTask: APP_URL + "/professional/task/add",
    search: (criteria) => {
        return '/home/search?' + criteria;
    },
    message: (chatId) => {
        return '/chats?_chatId=' + chatId;
    },
    messagePro: (chatId) => {
        return '/professional/chats?_chatId=' + chatId;
    },
    professional: (id) => {
        return '/professional/view?_pid=' + id;
    },
    quotes: APP_URL + '/quotes',
    pastJobsDetail: (id) => {
        return '/jobs/past/detail?_id=' + id;
    },
    currentJobsDetail: (id) => {
        return '/jobs/current/detail?_id=' + id;
    },
    quotesDetail: (id) => {
        return '/quotes/detail?_id=' + id;
    },
    quotesDetailPro: (id) => {
        return '/professional/quotes/detail?_id=' + id;
    },
    pastJobsDetailPro: (id) => {
        return '/professional/jobs/past/detail?_id=' + id;
    },
    currentJobsDetailPro: (id) => {
        return '/professional/jobs/current/detail?_id=' + id;
    },
    requestDetail: (id) => {
        return '/professional/requests/detail?_id=' + id;
    },
    profileViewPro: APP_URL + "/professional/profile/view"
}

const STORAGE = {
    set: async function (key, val) {
        return localStorage.setItem(key, val);
    },
    get: function (key) {
        return localStorage.getItem(key);
    },
    clear: function (key) {
        return localStorage.clear();
    },
    debug: "debug",
    accesstoken: "accesstoken",
    userId: "userId",
    name: "name",
    role: "role",
    roleType: "roleType",
    lat: "lat",
    long: "long",
    recent: "recent",
    subscribed: "subscribed",
    category: "category",
    image: "image",
    notifications: "notifications",
    username: "username",
    media: "media",
    camefrom: "camefrom",
    email: "email",
    password: "password",
    referal_code: "referal_code",
    pid: "pid",
    otp: "otp"
}

const __applog = {
    info: function (key, val) {
        return console.log(key, val);
    },
    warn: function (key, val) {
        return console.log(key, val);
    },
    error: function (key, val) {
        return console.error(key, val);
    }
}

const __toaster = {
    success: function (title, message) {
        // document.querySelector('.success-msg').innerHTML = message;
        //document.querySelector('.app-success').classList.remove('hide');
        //document.querySelector('.app-success').classList.add('on');
        setTimeout(() => {
            this.close().success()
        }, 3000);
    },
    error: function (title, message) {
        //document.querySelector('.error-msgs').innerHTML = message;
        //document.querySelector('.app-error').classList.add('on');
        //document.querySelector('.app-error').classList.remove('hide');
        setTimeout(() => {
            this.close().error()
        }, 3000)
    },
    launch_toast(_txt) {
        var x = document.getElementById("toast_msg")
        x.className = "t_show";
        //document.getElementById("__custom_text").html(_txt);
        // setTimeout(function () { x.className = x.className.replace("t_show", ""); }, 5000);
    },
    close() {
        function success() {
            //document.querySelector('.app-success').classList.add('hide');
        }

        function error() {
            //  document.querySelector('.app-error').classList.add('hide');
        }

        return {
            success: success,
            error: error
        }
    }
}

const __json = {
    parse: function (string) {
        try {
            return JSON.parse(string);
        } catch (e) {
            return [];
        }

    },
    stringify: function (json) {
        return JSON.stringify(json);
    }
}

function replaceString(string, replaceword, replacewith) {
    if (!string) {
        return string
    }

    return string.replace(replaceword, replacewith);
}

function __is_user_logged_in(toBeRedirect = true) {
    console.log("here");
    if (localStorage.getItem(STORAGE.accesstoken) == null) {
        __applog.info("Login Failed", "Session Expires");
        if (toBeRedirect) {
            goto(UI_URL.login);
        }
        return false;
    }
    return true;
}

function isFile(string) {
    var substring = ".";

    if (!string) {
        return false;
    }

    if (string.indexOf(substring) !== -1) {
        return true;
    }
    return false;
}

function logout() {
    localStorage.clear();
    __is_user_logged_in();
}

function goto(url) {
    return window.location.href = url;
}

function gotoBlank(url) {
    let a = document.createElement('a');
    a.target = '_blank';
    a.href = url;
    a.click();
}

function prettyURL(string) {
    string = string.toLowerCase();
    return string.replace(/ /g, '-');
}

function urlTotext(url) {
    url = url.replace(/-/g, ' ');
    url = url.replace(/^./, url[0].toUpperCase());
    return url;
}

function spaceReplace(string) {
    return string.replace(/ /g, '')
}


function pathParam() {
    return location.pathname.split('/');
}

function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    name = name.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
    var lc = location.search.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '))
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
};

function headers() {
    return {
        ['Authorization']: "Bearer " + STORAGE.get(STORAGE.accesstoken),
        'Accept': 'application/json'
    }
}

function headersForm() {
    return {
        [STORAGE.userId]: STORAGE.get(STORAGE.userId),
        // [STORAGE.refreshToken]: STORAGE.get(STORAGE.refreshToken),
        [STORAGE.sessiontoken]: STORAGE.get(STORAGE.sessiontoken),
    }
}

function __ajax_http(script, data, headers, type, apiType, __callback__) {

    LOADER.show();

    if (type == AJAX_CONF.apiType.GET) {
        data = "";
    } else if (type == AJAX_CONF.apiType.DELETE) {
        data = "";
    } else {
        data = __json.stringify(data)
    }

    $.ajax({
        type: type,
        headers: headers,
        crossDomain: true,
        url: API_URL + script,
        dataType: AJAX_CONF.dataType,
        data: data,
        cache: false,
        contentType: AJAX_CONF.contentType,
        mimeType: AJAX_CONF.contentType,
        timeout: AJAX_CONF.timeout,

        success: function (data) {
            if (data) {

                if (data.status != 200 && data.status != 400) {
                    __applog.info(apiType, data.message);
                    __toaster.error(apiType, data.message);

                }
                // else if(type != AJAX_CONF.apiType.DELETE || type !== AJAX_CONF.apiType.GET){
                //     __applog.info(apiType, data.message);
                //     __toaster.success(apiType,data.message);
                // }

                __applog.info(apiType, data.message);
            }
            if (typeof __callback__ === 'function') {
                __callback__(data);
            }
            LOADER.hide();
        },
        error: function (x, t, m) {

            LOADER.hide();
            if (t === "timeout") {
                __toaster.error(apiType, "Server did not respond. Please try later.");
            } else {
                if (!x.responseJSON) {

                    return;
                }
                if (x.responseJSON.status >= 400 && x.responseJSON.status < 500) {

                    if (typeof __callback__ === 'function') {

                        __callback__(x.responseJSON);
                    }

                    __toaster.error("Server", "Something went wrong")
                    return;
                }
                if (x.responseJSON.status == false) {
                    __callback__(x.responseJSON);

                }
                __toaster.error(apiType, x.responseJSON.status);
            }
        }
    });
}


function __ajax_httpproduct(script, data, headers, type, apiType, __callback__) {

    LOADER.show();

    if (type == AJAX_CONF.apiType.GET) {
        data = "";
    } else if (type == AJAX_CONF.apiType.DELETE) {
        data = "";
    } else {
        data = __json.stringify(data)
    }

    $.ajax({
        type: type,
        headers: headers,
        crossDomain: true,
        url: API_URL + script,
        dataType: AJAX_CONF.dataType,
        data: data,
        cache: false,
        contentType: AJAX_CONF.contentType,
        mimeType: AJAX_CONF.contentType,
        timeout: AJAX_CONF.timeout,

        success: function (data) {
            if (data) {

                if (data.status != 200 && data.status != 400) {
                    __applog.info(apiType, data.message);
                    __toaster.error(apiType, data.message);
                    console.log("test");
                    console.log(data.message);

                }
                // else if(type != AJAX_CONF.apiType.DELETE || type !== AJAX_CONF.apiType.GET){
                //     __applog.info(apiType, data.message);
                //     __toaster.success(apiType,data.message);
                // }

                __applog.info(apiType, data.message);
            }
            if (typeof __callback__ === 'function') {
                __callback__(data);
            }
            LOADER.hide();
        },
        error: function (x, t, m) {
            LOADER.hide();
            if (t === "timeout") {
                __toaster.error(apiType, "Server did not respond. Please try later.");
            } else {
                if (!x.responseJSON) {
                    return;
                }
                if (x.responseJSON.status >= 400 && x.responseJSON.status < 500) {
                    if (typeof __callback__ === 'function') {
                        __callback__(x.responseJSON);
                    }
                    __toaster.error("Server", "Something went wrong")
                    return;
                }
                __toaster.error(apiType, x.responseJSON.status);
            }
        }
    });
}

//register
function __ajaxregister_http(script, data, headers, type, apiType, __callback__) {

    LOADER.show();

    if (type == AJAX_CONF.apiType.GET) {
        data = "";
    } else if (type == AJAX_CONF.apiType.DELETE) {
        data = "";
    } else {
        data = __json.stringify(data)
    }

    $.ajax({
        type: type,
        headers: headers,
        crossDomain: true,
        url: API_URL + script,
        dataType: AJAX_CONF.dataType,
        data: data,
        cache: false,
        contentType: AJAX_CONF.contentType,
        mimeType: AJAX_CONF.contentType,
        timeout: AJAX_CONF.timeout,

        success: function (data) {
            if (data) {

                if (data.status != 200 && data.status != 400) {
                    __applog.info(apiType, data.message);
                    __toaster.error(apiType, data.message);
                    console.log("test");
                    console.log(data.message);

                }
                // else if(type != AJAX_CONF.apiType.DELETE || type !== AJAX_CONF.apiType.GET){
                //     __applog.info(apiType, data.message);
                //     __toaster.success(apiType,data.message);
                // }

                __applog.info(apiType, data.message);
            }
            if (typeof __callback__ === 'function') {
                __callback__(data);
            }
            LOADER.hide();
        },
        error: function (x, t, m) {
            LOADER.hide();
            if (t === "timeout") {
                __toaster.error(apiType, "Server did not respond. Please try later.");
            } else {
                if (!x.responseJSON) {
                    return;
                }
                if (x.responseJSON.status >= 400 && x.responseJSON.status < 500) {
                    if (typeof __callback__ === 'function') {
                        __callback__(x.responseJSON);
                    }
                    __toaster.error("Server", "Something went wrong")
                    return;
                }
                if (x.responseJSON.status == false) {
                    __callback__(x.responseJSON);

                }
                __toaster.error(apiType, x.responseJSON.status);
            }
        }
    });
}

function __ajax_http_background(script, data, headers, type, apiType, __callback__) {

    //LOADER.show();

    if (type == AJAX_CONF.apiType.GET) {
        data = "";
    } else if (type == AJAX_CONF.apiType.DELETE) {
        data = "";
    } else {
        data = __json.stringify(data)
    }

    $.ajax({
        type: type,
        headers: headers,
        crossDomain: true,
        url: API_URL + script,
        dataType: AJAX_CONF.dataType,
        data: data,
        cache: false,
        contentType: AJAX_CONF.contentType,
        mimeType: AJAX_CONF.contentType,
        timeout: AJAX_CONF.timeout,
        success: function (data) {
            if (data) {
                if (data.status != 200) {
                    __applog.info(apiType, data.errorMessage);
                    __toaster.error(apiType, data.errorMessage);
                }
                // else if(type != AJAX_CONF.apiType.DELETE || type !== AJAX_CONF.apiType.GET){
                //     __applog.info(apiType, data.message);
                //     __toaster.success(apiType,data.message);
                // }

                __applog.info(apiType, data.data);
            }
            if (typeof __callback__ === 'function') {
                __callback__(data);
            }
            //LOADER.hide();
        },
        error: function (x, t, m) {
            //LOADER.hide();
            if (t === "timeout") {
                __toaster.error(apiType, "Server did not respond. Please try later.");
            } else {
                if (!x.responseJSON) {
                    return;
                }
                if (x.responseJSON.status >= 400 && x.responseJSON.status < 500) {
                    __toaster.error("Server", "Something went wrong")
                    return;
                }
                __toaster.error(apiType, x.responseJSON.errorMessage.error);
            }
        }
    });
}


function __ajax_http_upload(script, data, headers, type, apiType, __callback__) {

    LOADER.show();

    var xhr = $.ajax({
        type: type,
        headers: headers,
        crossDomain: true,
        url: API_URL + script,
        data: data,
        cache: false,
        dataType: AJAX_CONF.dataType,
        mimeType: 'multipart/form-data', // this too
        processData: false,
        contentType: false,
        timeout: AJAX_CONF.timeout,
        xhr: function () {
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                myXhr.upload.addEventListener('progress', progress, false);
            }
            return myXhr;
        },
        success: function (data) {
            if (data) {
                if (data.status != 200) {
                    __applog.info(apiType, data.errorMessage);
                    __toaster.error(apiType, data.errorMessage);
                }
                __applog.info(apiType, data.data);
            }
            if (typeof __callback__ === 'function') {
                __callback__(data);
            }
            LOADER.hide();
        },
        error: function (x, t, m) {
            LOADER.hide();
            if (t === "timeout") {
                __toaster.error(apiType, "Server did not respond. Please try later.");
            } else {
                if (!x.responseJSON) {
                    return;
                }
                if (x.responseJSON.status >= 400 && x.responseJSON.status < 500) {
                    __toaster.error("Server", "Something went wrong")
                    return;
                }
                __toaster.error(apiType, x.responseJSON.errorMessage.error);
            }
        }

    });


}

function __ajax_local_file(script, __callback__) {
    $.ajax({
        url: script,
        method: AJAX_CONF.apiType.GET,
        dataType: AJAX_CONF.dataType,
        contentType: AJAX_CONF.contentType,
        success: function (data) {
            __callback__(data);
        }
    })
}


function date_to_local(utc) {
    utc = utc.replace(" ", "T");
    utc = utc + "Z";
    utc = utc.split('.');
    utc = utc[0];
    return new Date(utc).toDateString();
}

function __date_to_local(utc) {
    utc = utc.replace(" ", "T");
    utc = utc + "Z";
    return new Date(utc).toDateString();
}

function time_to_local(utc) {
    utc = utc.replace(" ", "T");
    utc = utc + "Z";

    return new Date(utc).toLocaleTimeString([], {timeStyle: 'short'});
}

function date_diff_days(d1, d2) {
    var date1 = new Date(d1);
    var date2 = new Date(d2);
    var diffTime = Math.abs(date2 - date1);
    var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}

function date_diff_weeks(d1, d2) {
    var date1 = new Date(d1);
    var date2 = new Date(d2);
    var t2 = date2.getTime();
    var t1 = date1.getTime();

    return parseInt((t2 - t1) / (24 * 3600 * 1000 * 7));
}


function _Price(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function timeSince(date) {
    date = date.replace(" ", "T");
    date = date + "Z";
    date = new Date(date);
    var seconds = Math.floor((new Date() - date) / 1000);

    var interval = Math.floor(seconds / 31536000);

    if (interval > 1) {
        return interval + " years ago";
    }
    interval = Math.floor(seconds / 2592000);
    if (interval > 1) {
        return interval + " months ago";
    }
    interval = Math.floor(seconds / 86400);
    if (interval > 1) {
        return interval + " days ago";
    }
    interval = Math.floor(seconds / 3600);
    if (interval > 1) {
        return interval + " hours ago";
    }
    interval = Math.floor(seconds / 60);
    if (interval > 1) {
        return interval + " minutes ago";
    }
    return Math.floor(seconds) + " seconds ago";
}

function validatePassword($this) {

    var minLength = 8;
    var maxLength = 20;
    //console.log($($this).val());

    var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/
    var isPasswordValidate = regex.test($($this).val());


    if (!isPasswordValidate) {

        $("#errPassword").removeClass("hide");
        $("#resetpasswordError").removeClass("hide");
        $("#password-err").addClass("error-field");
        isError = true;
    } else {
        $("#errPassword").addClass("hide");
        $("#resetpasswordError").addClass("hide");
        $("#password-err").removeClass("error-field");

        isError = false;
    }

    return !isError;
}

function matchPassword($this) {
    if ($($this).val() != $("#password").val()) {
        $("#errConfirmPassword").removeClass("hide");
        $("#conf-password-err").addClass("error-field");

        //isError = true;
    } else {
        $("#errConfirmPassword").addClass("hide");
        //isError = false;
    }
}


function eitherValidateEmailOrPhone($this) {
    if (!validatephone($this) && !validateEmail($this)) {
        $("#signupError").removeClass("hide");
        $("#signupError").text("*Please enter either valid Email or phone number ");
        // $("#" + $this.id).next('.error-msg').removeClass('hide');
        $("#email-error").addClass("error-field");
        return false;
    } else {
        $("#signupError").addClass("hide");
        $("#email-error").removeClass("error-field");
        // $("#" + $this.id).next('.error-msg').addClass('hide');;
        return true;
    }
}

function validateEmailOnly($this) {
    if (!validateEmail($this)) {
        $("#" + $this.id).next('.error-msg').css('display', 'block');
        isError = true;
    } else {
        $("#" + $this.id).next('.error-msg').css('display', 'none');
        isError = false;
    }
}

function validatePhoneOnly($this) {
    if (!validatephone($this)) {
        $("#" + $this.id).next('.error-msg').css('display', 'block');
        isError = true;
    } else {
        $("#" + $this.id).next('.error-msg').css('display', 'none');
        isError = false;
    }
}

function EmailOrPhoneType($this) {
    if (validatephone($this)) {
        return "phone";
    } else {
        return "email";
    }
}


function validatephone($this) {

    if ($($this).val().match('^[0-9]{8,12}$')) {
        api.identifier = "phone";
        api.identifierO = "email";
        return true;
    } else {
        return false;
    }

}

function validatenumber($this) {

    if ($($this).val().match('^[0-9]{1,7}$')) {
        $("#" + $this.id).next('.error-msg').css('display', 'none');
        isError = false;
    } else {
        $("#" + $this.id).next('.error-msg').css('display', 'block');
        isError = true;
    }

}

function validatename($this) {

    if ($($this).val().match('^[a-zA-Z ]{3,30}$')) {
        $("#" + $this.id).next('.error-msg').css('display', 'none');
        isError = false;
    } else {
        $("#" + $this.id).next('.error-msg').css('display', 'block');
        isError = true;
    }

}

function validatenoempty($this) {

    if ($($this).val()) {
        $("#" + $this.id).next('.error-msg').css('display', 'none');
        isError = false;
    } else {
        $("#" + $this.id).next('.error-msg').css('display', 'block');
        isError = true;
    }

}

function validateEmail($this) {

    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    var isEmailValidate = regex.test($($this).val());

    if (isEmailValidate) {
        // if (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test($this.value)){
        api.identifier = "email";
        api.identifierO = "phone";
        return isEmailValidate;
    } else {
        return isEmailValidate;
    }
}

function __success_profile(response) {
    if (response.status != 200) {
        return;
    }

    api.profile = response;

    if (response.data.status == ADMIN_LOCK) {
        $(".adminlock").removeClass("hide");
        $(".adminunlock").addClass("hide");
    }

    $("#bookmarks").html(response.data.bookmarks);
    $("#jobInProgress").html(response.data.jobInProgress);
    $("#pastJobs").html(response.data.pastJobs);

    $("#name").html(response.data.name);
    $("#name_mobile").html(response.data.name);
    $("#email").html(response.data.email);
    if (response.data.phone > 1) {
        $("#phone").html(response.data.phone);
    }
    $("#profilePic").attr('src', MEDIA_URL + response.data.profilePic);

    if (response.data.quotePending > 0) {
        $("#quotePending").html(response.data.quotePending).removeClass('hide');
    }

    if ($("#_email").length > 0) {
        $("#_email").val(response.data.email);
    }

    if ($("#notifications-settings").length > 0) {
        if (response.data.isPush) {
            $("#isPush").attr('checked', true)
        }
        if (response.data.isNotifyEmail) {
            $("#isNotifyEmail").attr('checked', true)
        }
        if (response.data.isNotifiySMS) {
            $("#isNotifiySMS").attr('checked', true)
        }
    }

}

function gotoNotification(index) {
    var notified = api.notifications[index];

    switch (notified.type) {
        case "SIGNUP":
            break;
        case "PRO_SIGNUP":
            break;
        case "Q_NEW":
            goto(UI_URL.requestDetail(notified.tblId));
            break;
        case "Q_EDIT":
            if (STORAGE.get(STORAGE.roleType) == "3") {
                goto(UI_URL.quotesDetailPro(notified.tblId));
            } else {
                goto(UI_URL.quotesDetail(notified.tblId));
            }
            break;
        case "JOB_COMPLETE":
            if (STORAGE.get(STORAGE.roleType) == "3") {
                goto(UI_URL.pastJobsDetailPro(notified.tblId));
            } else {
                goto(UI_URL.pastJobsDetail(notified.tblId));
            }
            break;
        case "ADMIN_ANNOUNCEMENT_USER":
            break;
        case "ADMIN_ANNOUNCEMENT_PRO":
            break;
        case "ADMIN_ANNOUNCEMENT":
            break;
        case "PRO_APPROVED":
            break;
        case "QUOTE_ACCEPTED":
            goto(UI_URL.currentJobsDetailPro(notified.tblId));
            break;
    }
}

function __success_chatBadge(response) {
    if (response.data.count > 0) {
        $("#unread").removeClass("hide").html(response.data.count);
    } else {
        $("#unread").addClass("hide");
    }
}

function __success_notifications(response) {
    api.notifications = response.data;
    var appendHTML = "";
    var appendHTML2 = "";

    var isUnRead = 0;

    if (api.notifications.length != 0) {
        $("#notifications_seemore").removeClass('hide');
        $("#notifications").removeClass('hide');
    } else {
        $("#notifications_empty").removeClass('hide');
    }


    api.notifications.forEach(function (element, index) {

        if (element.isRead == 0 && element.type != "ADMIN_ANNOUNCEMENT") {
            ++isUnRead;
        }

        var image = "/../images/notifications/default.png";
        switch (element.type) {
            case "SIGNUP":
                image = "/../images/notifications/default.png";
                break;
            case "PRO_SIGNUP":
                image = "/../images/notifications/default.png";
                break;
            case "Q_NEW":
                image = "/../images/notifications/quote.png";
                break;
            case "Q_EDIT":
                image = "/../images/notifications/quote.png";
                break;
            case "JOB_COMPLETE":
                image = "/../images/notifications/job.png";
                break;
            case "ADMIN_ANNOUNCEMENT_USER":
                image = "/../images/notifications/default.png";
                break;
            case "ADMIN_ANNOUNCEMENT_PRO":
                image = "/../images/notifications/default.png";
                break;
            case "ADMIN_ANNOUNCEMENT":
                image = "/../images/notifications/default.png";
                break;
            case "PRO_APPROVED":
                image = "/../images/notifications/default.png";
                break;
            case "QUOTE_ACCEPTED":
                image = "/../images/notifications/job.png";
                break;
        }

        var _timeSince = timeSince(element.createdDate);
        appendHTML += '<ul onclick=gotoNotification(' + index + ') >' +
            '<li class="image">' +
            '<img src="' + image + '" alt="Image" />' +
            '</li>' +
            '<li class="data">' +
            '<h2>' + element.message + '</h2>' +
            '<p>' + _timeSince + '</p>' +
            '</li>' +
            '</ul>';

        appendHTML2 += '<ul onclick=gotoNotification(' + index + ') >' +
            '<li class="image">' +
            '<img src="' + image + '" alt="Image" />' +
            '</li>' +
            '<li class="data">' +
            '<h2>' + element.message + '</h2>' +
            '<p></p>' +
            '<span>' + _timeSince + '</span>' +
            '</li>' +
            '</ul>';
    });

    $("#notifications").html(appendHTML);


    if ($("#notifications-vw").length > 0) {
        $("#notifications-vw").html(appendHTML2);
        if (api.notifications.length == 0) {
            $(".no_data").removeClass('hide');
        }
    }

    if (isUnRead > 0) {
        $("#notUnread").removeClass("hide").html(isUnRead);
    } else {
        $("#notUnread").addClass("hide");
    }

}

function nologin() {
    $(".login-section").addClass('hide');
    $(".no-login-section").removeClass('hide');
}

function afterLogin() {
    //if (!__is_user_logged_in(false)) { nologin(); return }

    $(".login-section").removeClass('hide');
    $(".no-login-section").addClass('hide');

    if ($('.message_section').length > 0) {
        return;
    }

    //__ajax_http("login/profile", "", headers(), AJAX_CONF.apiType.GET, "GET profile", __success_profile);

    setInterval(function () {
        if (__is_user_logged_in(false)) {

            //if($('.message_section').length>0){return;}

            // __ajax_http_background("notifications", "", headers(), AJAX_CONF.apiType.GET, "GET notifications", __success_notifications);
            // __ajax_http_background("chats/badge/count", "", headers(), AJAX_CONF.apiType.GET, "GET chatBadge", __success_chatBadge);
        }
    }, 8000)

}

function readNotification() {
    __ajax_http_background("notifications/mark/read", "", headers(), AJAX_CONF.apiType.PATCH, "PATCH notifications", function () {
        return;
    });
}

function addbookmark(professionalId) {
    var json = {
        "userId": STORAGE.get(STORAGE.userId),
        "professionalId": professionalId
    }
    __ajax_http("bookmarkprofessionals", json, headers(), AJAX_CONF.apiType.POST, "POST Bookmark Professionals", __success_addbookmark);
}

function removeBookmark(bookmarkId) {
    __ajax_http("bookmarkprofessionals/" + bookmarkId, {}, headers(), AJAX_CONF.apiType.DELETE, "DELETE Bookmark Professionals", __success_removeBookmark);
}

function cms(pagetype, __callback__ = "__success_cms") {
    __callback__ = eval(__callback__);
    __ajax_http("cms/" + pagetype, "", headers(), AJAX_CONF.apiType.GET, "GET" + cms.caller.name, __callback__);
}

function userProfile(userId) {
    __ajax_http_background("users/" + userId, {}, headers(), AJAX_CONF.apiType.GET, "GET userProfile", __success_userProfile);
}

function updateNotificaionSettings() {
    var json = {
        isPush: $("#isPush").is(':checked'),
        isNotifyEmail: $("#isNotifyEmail").is(':checked'),
        isNotifiySMS: $("#isNotifiySMS").is(':checked')
    };

    __ajax_http("users/" + STORAGE.get(STORAGE.userId), json, headers(), AJAX_CONF.apiType.PUT, "PUT updateProfile", __success_updateProfile);
}

// function getLocation() {
//     if (navigator.geolocation) {
//         navigator.geolocation.getCurrentPosition(showPosition, showError);
//     } else {
//         __toaster.error("Location", "Geolocation is not supported by this browser.");
//     }
// }

function showError(error) {
    var innerHTML = "";
    switch (error.code) {
        case error.PERMISSION_DENIED:
            innerHTML = "User denied the request for Geolocation."
            break;
        case error.POSITION_UNAVAILABLE:
            innerHTML = "Location information is unavailable."
            break;
        case error.TIMEOUT:
            innerHTML = "The request to get user location timed out."
            break;
        case error.UNKNOWN_ERROR:
            innerHTML = "An unknown error occurred."
            break;
    }

    __toaster.error("Location", innerHTML);
}

function showPosition(position) {

    console.log(position);
    var url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + position.coords.latitude + "," + position.coords.longitude + "&key=AIzaSyA5zjhM6wmjVHbWv_RFzzVXkBrPr-mZLFQ";
    //console.log(url);
    $.getJSON(url, function (json) {
        var myAddress = json.results[0].formatted_address;
        //console.log(myAddress);
        if ($("#txtPlaces").length > 0) {
            $("#txtPlaces").val(myAddress);
        }
        if ($(".txtPlaces2").length > 0) {
            $(".txtPlaces2").val(myAddress);
        }
        STORAGE.set(STORAGE.lat, position.coords.latitude);
        STORAGE.set(STORAGE.long, position.coords.longitude);
        if (__is_user_logged_in(false)) {
            if (STORAGE.get(STORAGE.roleType) == "2") {
                __ajax_http_background("users/" + STORAGE.get(STORAGE.userId), {
                    lat: position.coords.latitude,
                    long: position.coords.longitude,
                    location: myAddress
                }, headers(), AJAX_CONF.apiType.PUT, "PUT updateProfile", null);
            }
        }

    });


}

function showLocation(response) {
    console.log(response);
}


function sendMessage(user2) {

    var json = {
        "professionalId": user2,
        "message": $("#messages").val(),
        "quoteId": getUrlParameter("_id")
    };

    __ajax_http("chats", json, headers(), AJAX_CONF.apiType.POST, "POST Chat by ChatId for ", __success_postChatByChatId);

}

function searchQuotesEvt(e) {
    if (e.keyCode == 13) {
        searchQuotes();
    }
}

function searchQuotes() {

    var keyword = $("#keyword").val();
    var location = $("#txtPlaces").val();

    var recent = __json.parse(STORAGE.get(STORAGE.recent));
    if (!recent) {
        recent = [];
    }

    if (recent.indexOf(keyword) !== -1) {

    } else {
        recent.push(keyword);
    }

    if ($("#txtPlaces2").length > 0) {
        location = $("#txtPlaces2").val();
    }


    STORAGE.set(STORAGE.recent, __json.stringify(recent));

    var criteria = "keyword=" + keyword + "&cat=" + $("#cat").val() + "&location=" + location;
    //console.log(criteria);
    goto(UI_URL.search(criteria));
}


function subscribed() {
    if (STORAGE.get(STORAGE.subscribed) == "1") {
        $(".err_thanks_sub").addClass('hide');
        $("#succ_thanks_sub").removeClass('hide');
    } else {
        var email = $("#thanksSub").val();
        if (email != "" && $("#thanksSub").length > 0) {
            __ajax_http_background("subscribed", {email: email}, {'Accept': 'application/json'}, AJAX_CONF.apiType.POST, "POST Susbcribed", null);
            $(".err_thanks_sub").addClass('hide');
            $("#succ_thanks_sub").removeClass('hide');
            STORAGE.set(STORAGE.subscribed, "1");
        }
    }
}

function recent_search() {
    var recent = __json.parse(STORAGE.get(STORAGE.recent));
    var appendHTML = "";
    if (!recent) {
        $("#recent_search_div").addClass('hide');
        return;
    }

    recent.forEach(function (element) {
        appendHTML += '<li><a href="javascript:void(0)" onclick="searchProduct(this)">' + element + '</a></li>';
    })

    $("#recent_search").html(appendHTML);

}

function contactus() {

    __ajax_http("contactus/guest", {
        email: $("#_email").val(),
        suggestion: $("#suggestion").val()
    }, headers(), AJAX_CONF.apiType.POST, "POST Contact us ", __success_contactus);
}

function __success_contactus(response) {
    __toaster.success("Contact Us", response.message);
    setTimeout(function () {
        location.reload();
    }, 3000)
}

function progress(e) {

    if (e.lengthComputable) {
        var max = e.total;
        var current = e.loaded;

        var Percentage = (current * 100) / max;
        //console.log(Percentage);
        $("#progressval").css('width', Percentage + '%');

        if (Percentage >= 100) {
            // process completed
        }
    }
}

function displayNotification(title, body) {
    if (Notification.permission == 'granted') {
        navigator.serviceWorker.getRegistration().then(function (reg) {
            var options = {
                body: 'Welcome to Find-IT Web!',
                icon: '/../images/logo/logo.png',
                vibrate: [100, 50, 100],
                data: {
                    dateOfArrival: Date.now(),
                    primaryKey: 1
                },
                actions: [{
                    action: 'login',
                    title: 'Login',
                    icon: '/../images/icons/user.svg'
                },
                    {
                        action: 'close',
                        title: 'Close notification',
                        icon: '/../images/icons/cross.svg'
                    },
                ]
            };
            reg.showNotification(title || 'Hello!!', options);
        });
    }
}

function subscribeUser() {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.ready.then(function (reg) {

            reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(
                    'AAAA1T4k43c:APA91bGs-ROv6JKvPETWtJqgMFh2OUd32pXahTg3osuR-viLiBimV4yf9lG8zRQbLolj0MPPGTEJEKI6GzcKSzdLCRQ9Fv5skjJ1CgfQ7SoU-NkYc_lb63HHPo4QwPPXPJJEccq2KaP_'
                )
            }).then(function (sub) {
                console.log('Endpoint URL: ', sub.endpoint);
            }).catch(function (e) {
                if (Notification.permission === 'denied') {
                    console.warn('Permission for notifications was denied');
                } else {
                    console.error('Unable to subscribe to push', e);
                }
            });
        })
    }
}

function subscribeUserToPush() {
    return navigator.serviceWorker.register('service-worker.js')
        .then(function (registration) {
            const subscribeOptions = {
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(
                    'BEUS1yRKfmQjqcY2cRXseN-I-dE1xiuyIY4YvzUAzdMT-UIyIMC3445taNcBZHxoznvgcBIUql2auebkkeVut1Y'
                )
            };

            return registration.pushManager.subscribe(subscribeOptions);
        })
        .then(function (pushSubscription) {
            console.log('Received PushSubscription: ', JSON.stringify(pushSubscription));
            return pushSubscription;
        });
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');
    const rawData = window.atob(base64);
    return Uint8Array.from([...rawData].map((char) => char.charCodeAt(0)));
}

function checkPro() {
    if (STORAGE.get(STORAGE.roleType) == "3") {
        goto(UI_URL.rootPro);
    }
}


function checkUserRole() {
    return STORAGE.get(STORAGE.roleType);
}

window.addEventListener('DOMContentLoaded', (event) => {
    afterLogin();
    //getLocation();
    //subscribed();
    //LOADER.hide();

});

if ('serviceWorker' in navigator) {
    subscribeUserToPush();
    // navigator.serviceWorker.register('/service-worker.js')
    //     .then(function (registration) {
    //         console.log('Registration successful, scope is:', registration.scope);
    //         registration.pushManager.getSubscription().then(function (sub) {
    //             if (sub === null) {
    //                 // Update UI to ask user to register for Push
    //                 console.log('Not subscribed to push service!');
    //             } else {
    //                 // We have a subscription, update the database
    //                 console.log('Subscription object: ', sub);
    //             }
    //         });
    //     })
    //     .catch(function (error) {
    //         console.log('Service worker registration failed, error:', error);
    //     });
}

if ('Notification' in window && navigator.serviceWorker) {
    // Display the UI to let the user toggle notifications
    Notification.requestPermission(function (status) {
        console.log('Notification permission status:', status);
        //displayNotification();
    });
}

function _checkAndReload(response) {

    if (response.code != 200) {
        __toaster.error("Info", response.errorMessage);
        return;
    }
    //__toaster.success("Info", response.message);
    __toaster.success("Info", "Record Updated");
    setTimeout(function () {
        // location.reload();
    }, 1000)

}

String.prototype.escape = function () {
    var tagsToReplace = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;'
    };
    return this.replace(/[&<>]/g, function (tag) {
        return tagsToReplace[tag] || tag;
    });
};


// https://tc39.github.io/ecma262/#sec-array.prototype.find
if (!Array.prototype.find) {
    Object.defineProperty(Array.prototype, 'find', {
        value: function (predicate) {
            // 1. Let O be ? ToObject(this value).
            if (this == null) {
                throw TypeError('"this" is null or not defined');
            }

            var o = Object(this);

            // 2. Let len be ? ToLength(? Get(O, "length")).
            var len = o.length >>> 0;

            // 3. If IsCallable(predicate) is false, throw a TypeError exception.
            if (typeof predicate !== 'function') {
                throw TypeError('predicate must be a function');
            }

            // 4. If thisArg was supplied, let T be thisArg; else let T be undefined.
            var thisArg = arguments[1];

            // 5. Let k be 0.
            var k = 0;

            // 6. Repeat, while k < len
            while (k < len) {
                // a. Let Pk be ! ToString(k).
                // b. Let kValue be ? Get(O, Pk).
                // c. Let testResult be ToBoolean(? Call(predicate, T, Â« kValue, k, O Â»)).
                // d. If testResult is true, return kValue.
                var kValue = o[k];
                if (predicate.call(thisArg, kValue, k, o)) {
                    return kValue;
                }
                // e. Increase k by 1.
                k++;
            }

            // 7. Return undefined.
            return undefined;
        },
        configurable: true,
        writable: true
    });
}


window.fbAsyncInit = function () {
    FB.init({
        appId: '3382098745253194',
        cookie: true,
        xfbml: true,
        version: 'v9.0'
    });

    FB.AppEvents.logPageView();
    console.log(FB);
};

(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement(s);
    js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


function checkLoginState() {
    FB.getLoginStatus(function (response) {
        statusChangeCallback(response);
    });
}

function facebookLogout() {
    FB.logout(function (response) {
        // Person is now logged out
    });
}

function statusChangeCallback(response) { // Called with the results from FB.getLoginStatus().
    console.log('statusChangeCallback');
    console.log(response); // The current login status of the person.
    if (response.status === 'connected') { // Logged into your webpage and Facebook.
        fetchInfo();
    } else { // Not logged into your webpage or we are unable to tell.
        facebookLogin();
    }
}

function facebookLogin() {
    FB.login(function (response) {
        statusChangeCallback(response);
    }, {scope: 'public_profile,email'});
}

function fetchInfo() { // Testing Graph API after login.  See statusChangeCallback() for when this call is made.
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me?fields=id,name,email,birthday', function (response) {
        console.log('Successful login for: ' + JSON.stringify(response));
        if (location.href.indexOf("profile") !== -1) {
            updateFBId({fbId: response.email});
        } else {

            socialFBLogins({"fullName": response.name, "email": response.email, gId: "", "fbId": response.id});
        }

    });
}

function googleLogin() {
    console.log("Google login");
    googleLoginPage();
    googleProfilePage();
}

function googleLoginPage() {
    gapi.load('auth2', function () {
        auth2 = gapi.auth2.init({
            client_id: '427640940440-bibcimsshs1l8escl20j2b377s267g8f.apps.googleusercontent.com',
            cookiepolicy: 'single_host_origin',
            scope: 'profile email'
        });
        console.log(element.id);
        auth2.attachClickHandler(element, {},
            function (googleUser) {
                console.log('Signed in: ' + googleUser.getBasicProfile().getName());
                if (location.href.indexOf("profile") !== -1) {
                    updateGId({"gId": googleUser.getBasicProfile().getId()});
                } else {
                    socialGLogins({
                        "fullName": googleUser.getBasicProfile().getName(),
                        "email": googleUser.getBasicProfile().getEmail(),
                        "gId": googleUser.getBasicProfile().getId(),
                        "fbId": null
                    });
                }
            },
            function (error) {
                console.log('Sign-in error', error);
            }
        );
    });

    element = document.getElementById('googleSignIn');
}

function googleProfilePage() {
    if (!document.getElementById('googleSignIn2')) {
        return;
    }
    gapi.load('auth2', function () {
        auth2 = gapi.auth2.init({
            client_id: '427640940440-bibcimsshs1l8escl20j2b377s267g8f.apps.googleusercontent.com',
            cookiepolicy: 'single_host_origin',
            scope: 'profile'
        });

        auth2.attachClickHandler(element, {},
            function (googleUser) {
                console.log('Signed in: ' + googleUser.getBasicProfile().getName());
                if (location.href.indexOf("profile") !== -1) {
                    updateGId({"gId": googleUser.getBasicProfile().getId()});
                } else {
                    socialGLogin({
                        "fullName": googleUser.getBasicProfile().getName(),
                        "email": googleUser.getBasicProfile().getEmail(),
                        "gId": googleUser.getBasicProfile().getId(),
                        "fbId": ""
                    });
                }
            },
            function (error) {
                console.log('Sign-in error', error);
            }
        );
    });

    element = document.getElementById('googleSignIn2');
}

function socialFBLogins(gData) {
    //          alert(JSON.stringify(gData));
    var email = gData.email;
    var data = {
        fullName: gData.fullName,
        param: email,
        fbId: gData.fbId,
        gId: gData.gId,
    };

    __ajax_http("fbLoginforWeb", data, {'Accept': 'application/json'}, AJAX_CONF.apiType.POST, "Login", __success_login);
}

function socialGLogins(gData) {
    //  alert(JSON.stringify(gData));
    var data = {
        fullName: gData.fullName,
        param: gData.email,
        gId: gData.gId,
    };

    __ajax_http("gmailLoginforWeb", data, {'Accept': 'application/json'}, AJAX_CONF.apiType.POST, "Login", __success_google_login);

}

function notifications(json) {
    __ajax_http("notifications", json, headers(), AJAX_CONF.apiType.GET, "GET Notifications", __success_notifications);
}

function __success_notifications(response) {
    appendHTML = "";
    count1 = response.details.length;
    if (count1 > 0) {
        var notification_count = 0;
        response.details.forEach(function (element) {
            if (notification_count == 5) {
                appendHTML += '<li ><a href="/notifications" style="text-align:center;display:block;">--See More--</a></li>';
            }
            notification_count += 1;
            if (notification_count > 5) {
                return;
                //appendHTML += '<li ><a style="text-align:center;display:block;">--See More--</a></li>';
            }

            if (element.type == 1) {
                var image = element.user_details.image;
                if (image == null || image == "null") {
                    image_name = '../images/card-images/empty-profile.jpg';
                } else {
                    image_name = MEDIA_URL + '../images/' + image;
                }
                appendHTML += '<li><a><span class="image">' +
                    '<img src="' + image_name + '" alt="Profile Image" />' +
                    '</span>' +
                    '<div class="notification-txt">' +
                    '<span>' +
                    '<span>' + element.user_details.name + '</span>' +
                    '<span class="status-msg"></span>' +
                    '</span>' +
                    '<span class="message">' + element.message + '</span>'
                    // +'<span class="date-notification">17 Jan 2021</span>'
                    +
                    '</div>' +
                    '</a>' +
                    '</li>';

            } else {
                imgUrl = '';
                if (element.title == "Contract Started") {
                    imgUrl = "../images/icons/contract-accepted.jpg";
                }

                if (element.title == "Contract Accepted") {
                    imgUrl = "../images/icons/contract-accepted.jpg";
                }

                if (element.title == "New Contract Received") {
                    imgUrl = "../images/icons/contract- received.jpg";
                }

                if (element.title == "There's a Dispute!") {
                    imgUrl = "../images/icons/contract-dispute.jpg";
                }
                notification_status = '';
                if (element.is_read == 0) {
                    notification_status = '<span class="status-msg"></span>';
                }


                appendHTML += '<li><a><span class="image">' +
                    '<img src="' + imgUrl + '" alt="Profile Image" />' +
                    '</span>' +
                    '<div class="notification-txt">' +
                    '<span>' +
                    '<span>' + element.title + '</span>' +
                    notification_status +
                    '</span>' +
                    '<span class="message">' + element.message + '</span>'
                    // +'<span class="date-notification">17 Jan 2021</span>'
                    +
                    '</div>' +
                    '</a>' +
                    '</li>';

            }
        });

    } else {
        appendHTML = '<div class="notification-empty"><img src="../images/icons/notification-default.png"><div class="empty-txt"><h2>No notifications</h2><p>No notifications</p></div></div>';


    }


    $("#menu1").html(appendHTML);
}

function getNotificationStatus() {
    __ajax_http("notification_status", "", headers(), AJAX_CONF.apiType.GET, "GET Notifications Status", _checkAndReload);
}

function change_password(json) {
    __ajax_http("change-password", json, headers(), AJAX_CONF.apiType.POST, "GET Password Status", __success_logout);
}

function __success_logout(response) {
    if (response.code != 200) {
        $("#lblError").removeClass("hide");
        $("#lblError").removeClass("alert alert-success");
        $("#lblError").addClass("alert alert-danger");
        $("#lblError").html(response.message);
    } else {
        $("#lblError").removeClass("hide");
        $("#lblError").removeClass("alert alert-danger");
        $("#lblError").addClass("alert alert-success");
        $("#lblError").html(response.message);
    }
}


function deActivate(json) {
    __ajax_http("de-activate", json, headers(), AJAX_CONF.apiType.POST, "GET Password Status", __success_blocked);
}

function __success_blocked(response) {
    if (response.code != 200) {
        $("#lblError").removeClass("hide");
        $("#lblError").html(response.message);
    } else {
        logout();
    }
}

function logout_check() {
    localStorage.clear();
    login_check();
}
