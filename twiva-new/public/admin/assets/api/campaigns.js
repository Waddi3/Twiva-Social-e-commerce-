var uploaded_media = [];

function getCampaigns() {
    __ajax_http("campaigns", "", headers(), AJAX_CONF.apiType.GET, "GET campaigns", __success_getCampaigns);
}

function campaigndetails(id) {
    __ajax_http("campaigns?id=" + id, "", headers(), AJAX_CONF.apiType.GET, "GET campaigns", __success_campaignDetails);
}


function deleteCampaign(id) {
    __ajax_http("campaign?id=" + id, "", headers(), AJAX_CONF.apiType.DELETE, "Delete campaign", _success_delete);
}

function pastCollaborations(campaign_id) {

    __ajax_http("campaign_past_collaborations?campaign_id=" + campaign_id, "", headers(), AJAX_CONF.apiType.GET, "GET past collab", __success_past_collab);
}

function ongoingCollaborations(campaign_id) {

    __ajax_http("campaign_ongoing_collaborations?campaign_id=" + campaign_id, "", headers(), AJAX_CONF.apiType.GET, "GET ongoing collab", __success_ongoing_collab);
}

function __success_getCampaigns(response) {

    //dvCampaignList
    var appendHTML = "";
    var count = 0;

    var campaigns_number = response.campaigns.length;
    if (campaigns_number > 0) {

        appendHTML += '<div class="campaign-list-sec" >';
        response.campaigns.forEach(function(element) {
            var campaign_image = "../images/card-images/campaign-list.jpg";

            if (element.images.length != 0) {
                campaign_image = MEDIA_URL + '../images/' + element.images[0].image
            }
            //if (element.runningStatus == 0) {
            appendHTML += ' <div class="campaign-list-box" onclick="location.href=\'' + UI_URL.campaign + '?_id=' + element.id + '\';">   <div class="campaign-card">' + '<img src="' + campaign_image + '" alt="" onerror=this.src="../images/card-images/campaign-list.jpg"  /> </div> <div class="campaign-card-txt">' +
                '<h3>' + element.name + '</h3>' +
                '<div class="camp-app-out">' +
                '<div class="camp-app">' +
                ' <span><h2>' + element.application_count + '</h2> <p>Applications</p></span>' +
                '</div>' +
                '<div class="camp-app">' +
                '<span><h2>' + element.collaboration_count + '</h2> <p>Collaborations</p></span>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';

            ++count;
            // }
        });
        appendHTML += '</div>';
    } else {
        appendHTML += '<div class="campaign-empty"><img src="../images/card-images/campaigns-empty.png"><div class="empty-txt"><h2>No campaigns</h2><p>Create campaigns to collaborate with sponsees.</p></div></div>';
    }


    $("#dvCampaignList").html(appendHTML);
    $("#count_campaigns").html(campaigns_number);

}

function addCampaign() {
    isError = false;
    if ($("#campaign_name").val() == "") {
        isError = true;
        $("#err-campaign").css('display', 'block');
    }
    if ($("#reward").val() == "") {
        isError = true;
        $("#err-reward").css('display', 'block');
    }

    if (isError == false) {
        var media = STORAGE.get(STORAGE.media);
        var media_arr = media.split(",")
            // console.log(typeof(media));
        var json = {
            "user_created_by": STORAGE.get(STORAGE.userId),
            "name": $("#campaign_name").val(),
            "reward": $("#reward").val(),
            "other_reward": $("#other_reward").val(),
            "timeframe": $("#timeframe").val(),
            "about": $("#about").val(),
            "requirements": $("#requirements").val(),
            "ideal_candidate": $("#ideal_candidate").val(),
            "media": media_arr,
        };

        __ajax_http("add_campaign", json, headers(), AJAX_CONF.apiType.POST, "POST addCampaign", __response_success);

        // var fileInput = document.getElementsByClassName('profile-img-tag');
        // var file = fileInput.files[0];
        // var formData = new FormData();
        // formData.append('image[]', files);

        // __ajax_http_upload("upload_image_campaign", formData, headers(), AJAX_CONF.apiType.POST, "POST imageUpload", __imageDetails);

    }
}

function editCampaign(id) {
    isError = false;
    if ($("#campaign_name").val() == "") {
        isError = true;
        $("#err-campaign").css('display', 'block');
    }
    if ($("#reward").val() == "") {
        isError = true;
        $("#err-reward").css('display', 'block');
    }


    if (isError == false) {
        // var media= STORAGE.get(STORAGE.media);
        //  var media_arr = media.split(",")
        var media_arr = uploaded_media;

        //	media_arr.splice("",1);
        var json = {
            "id": id,
            "user_created_by": STORAGE.get(STORAGE.userId),
            "name": $("#campaign_name").val(),
            "reward": $("#reward").val(),
            "other_reward": $("#other_reward").val(),
            "timeframe": $("#timeframe").val(),
            "about": $("#about").val(),
            "requirements": $("#requirements").val(),
            "ideal_candidate": $("#ideal_candidate").val(),
            "media": media_arr,
        };
        __ajax_http("add_campaign", json, headers(), AJAX_CONF.apiType.POST, "POST editCampaign", __response_success);
    }
}


function __response_success(response) {
    // goto(UI_URL.campaigns);
    if (response.code != 200) {
        __toaster.error("Info", response.errorMessage);
        return false;
    }

    if (response.code == 200) {
        var path = window.location;

        goto(path);

    }
}



// window.addEventListener('DOMContentLoaded', (event) => {
//     __is_user_logged_in();
//     var path = window.location
//     if(path.pathname==UI_URL.campaigns){
//         getCampaigns();
//     }
//     else{
//         var id=5; 
//         campaigndetails(id);
//     }
// }
//


function _success_delete() {
    goto(UI_URL.campaigns);
}

function __success_past_collab(response) {
    var appendHTML = "";

    count = response.past_collaborations.length;
    if (count > 0) {
        response.past_collaborations.forEach(function(element) {

            var image_name = "../images/card-images/campaign-1.jpg";

            if (element.other_user_details.image != '' && element.other_user_details.image != null) {
                image_name = MEDIA_URL + '../images/' + element.other_user_details.image
            }

            appendHTML += '<div class="campaign-field">' +
                '<div class="campaign-prof"><img src="' + image_name + '"></div>' +
                '<div class="campaign-txt">' +
                '<label class="">' + element.other_user_details.name + '</label>' +
                '<p>' + element.other_user_details.price_range + ' per shoutout</p>' +
                '</div>' +
                '</div>';

        });
    } else {
        appendHTML = '<div class="bookmarks-empty"><img src="../images/card-images/sponsor-listempty.png"><div class="empty-txt"><p>No Past Collaborations </p></div></div>';

    }
    $(".past_collaborations").html(appendHTML);

}

function __success_ongoing_collab(response) {
    var appendHTML = "";

    count = response.ongoing_collaborations.length;
    if (count > 0) {
        response.ongoing_collaborations.forEach(function(element) {

            var image_name = "../images/card-images/campaign-1.jpg";

            if (element.other_user_details.image != null) {
                image_name = MEDIA_URL + '../images/' + element.other_user_details.image
            }

            appendHTML += '<div class="campaign-field">' +
                '<div class="campaign-prof"><img src="' + image_name + '"></div>' +
                '<div class="campaign-txt">' +
                '<label class="">' + element.other_user_details.name + '</label>' +
                '<p>' + element.other_user_details.price_range + ' per shoutout</p>' +
                '</div>' +
                '</div>';

        });
    } else {

        appendHTML = '<div class="bookmarks-empty"><img src="../images/card-images/sponsor-listempty.png"><div class="empty-txt"><p>No Ongoing Collaborations </p></div></div>';
    }

    $(".ongoing_collaborations").html(appendHTML);

}

function applications(id) {
    __ajax_http("get_story_by_id?story_id=" + id, "", headers(), AJAX_CONF.apiType.GET, "GET stories", __success_applications);

}

function __success_applications(response) {

	if('name' in  response.details.campaign){
    $("#campaign_name_modal").html(response.details.campaign.name);
        }
        else{
                 $("#campaign_name_modal").html('');
        }
        $("#bookmarkId").val(response.details.id);
        if(response.details.bookmarked==1){
                $("#addtobookmark").addClass("sel-button");
        }
	else{
		$("#addtobookmark").removeClass("sel-button");
	}
    $("#title_modal").html(response.details.title);
    $("#video_link").html('<iframe id="ifr"  width="560" height="630" src="https://api.sponsee.sg/storage/stories/' + response.details.media_url + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
    $("#campaign_detail").html(response.details.description);
    $(".sponsee-name").html(response.details.creator.name);

}


function __success_campaignDetails(response) {
    $("#campaign-name").html(response.campaign.name);
    $(".campaign-content .campaign-about").html(response.campaign.about);
    $('.campaign-rewards').html(response.campaign.other_reward);
    $('span#price').html(response.campaign.reward);
    $('.campaign-requirements').html(response.campaign.requirements);
    $('.campaign-timeframe').html(response.campaign.timeframe);
    $('#past_Collaborations').html(response.campaign.past_collaborations);
    $('#ongoing_Collaborations').html(response.campaign.ongoing_collaborations);
    $('#application_count').html(response.campaign.stories.length + " Applications");

    var appendHtml = "";
    response.campaign.stories.forEach(function(element) {
        var media_url = element.media_url;

        appendHtml += '<div class="app-card" onclick="applications(' + element.id + ')" data-toggle="modal" data-target="#video-modal"><img src="../images/card-images/campaign-list.jpg" ></div>';
    });

    $("#applications").html(appendHtml);

    var campaign_image = "../images/card-images/campaign-list.jpg";
imgHTML='<div class="item"><img src='+ campaign_image+ '></div>';
    if (response.campaign.images.length != 0) {
        campaign_image = MEDIA_URL + '../images/' + response.campaign.images[0].image
	lngth = response.campaign.images.length;
	    imgHTML = '<div id="myCarousel" class="carousel slide main-carousel" data-ride="carousel">';
	    imgHTML+=' <ol class="carousel-indicators">';
	    for(i=0;i<lngth;i++){
		    if(i==0){ imgHTML+=' <li data-target="#myCarousel" data-slide-to='+i+' class="active"></li>'; }
		    else{imgHTML+=' <li data-target="#myCarousel" data-slide-to='+i+'></li>'; }
	    }
	    imgHTML+='</ol> <div class="carousel-inner">';
	    for(i=0;i<lngth;i++){
		    if(response.campaign.images[i].image==""){
			    campaign_image = "../images/card-images/campaign-list.jpg";
		    }else{
		    campaign_image =MEDIA_URL + '../images/' + response.campaign.images[i].image;}

		    if(i==0){imgHTML+='<div class="item active"><img src="'+campaign_image+'"></div>';}
		    else{imgHTML+='<div class="item"><img src="'+campaign_image+'"></div>';}
	    }
	    imgHTML+='</div> <a class="left carousel-control" href="#myCarousel" data-slide="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a><a class="right carousel-control" href="#myCarousel" data-slide="next">   <i class="fa fa-angle-right" aria-hidden="true"></i> </a></div>';
    
    }
    $(".item-image").html(imgHTML);


	
    $("#campaign_name").val(response.campaign.name);
    $("#reward").val(response.campaign.reward);
    $("#other_reward").val(response.campaign.other_reward);
    $("#timeframe").val(response.campaign.timeframe);
    $("#about").val(response.campaign.about);
    $("#requirements").val(response.campaign.requirements);
    $("#ideal_candidate").val(response.campaign.ideal_candidate);


    var count = response.campaign.images.length;
    var imagesHtml = "";
    var cnt = 1;
    response.campaign.images.forEach(function(element) {
        imagesHtml += '<div class="single-upload">' +
            '<input type="file" class="profile-img" id="imgIn' + cnt + '" onchange="readURL(this);"> <span><i class="fa fa-plus"></i></span>' +
            '<img class="profile-img-tag imgIn' + cnt + '"  src="' + MEDIA_URL + '../images/' + element.image + '"  width="200px" id="img' + cnt + '">' +
            '<img  class="profile-img-tag hide" width="200px" id="img11"></div>';
        cnt++;
        uploaded_media.push(element.image);
    });

    STORAGE.set(STORAGE.media, uploaded_media);

    if (count < 6) {
        for (i = count + 1; i <= 6; i++) {
            imagesHtml += '<div class="single-upload">' +
                '<input type="file" class="profile-img" id="imgIn' + i + '" onchange="readURL(this);"> <span>+</span>' +
                '<img class="profile-img-tag imgIn' + i + '"  src=""  width="200px" id="img' + i + '">' +
                '<img  class="profile-img-tag hide" width="200px" id="img11"></div>';
        }
    }
    $("#media-box").html(imagesHtml);

}

function addToBookmark(type, item_id) {
    var json = {
        "type": type,
        "item": item_id
    };
    __ajax_http("bookmarks/create", json, headers(), AJAX_CONF.apiType.POST, "POST add Bookmark", __success_bookmark);

}

function __success_bookmark(response) {
    //alert("Bookmark added");
}


function deleteBookmark(type, item_id) {
    var json = {
        "type": type,
        "item": item_id
    };
    __ajax_http("bookmarks/delete", json, headers(), AJAX_CONF.apiType.POST, "POST add Bookmark", __success_bookmark);

}

function __success_bookmark(response) {
    //alert("Bookmark added");
}

( function( w, d ) {
   'use strict';

    // Get the modal
    var modal = d.getElementById( 'video-modal' );

        var iframe =  d.getElementById( 'ifr' );

    // When the user clicks anywhere outside of the modal, close it
   w.onclick = function ( event ) {
      if ( event.target == modal ) {
           modal.style.display = 'none';
      }
   };
   d.getElementsByClassName( 'close' )[0].addEventListener( 'click',
      function() {
         modal.classList.add( 'hide' );
         iframe.src = '';
   }, false );

   d.getElementById( 'video-modal' ).addEventListener( 'click',
      function() {
         modal.classList.remove( 'hide' );
        $("#ifr").attr('src','');

   }, false );

 }( window, document ));

