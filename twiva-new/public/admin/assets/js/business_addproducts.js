function AddProduct(json){

    __ajax_httpproduct("add-product", json, headers(), AJAX_CONF.apiType.POST, "", __success_product);      
}


//category

 function addproduct() {
    var json = 
        {"name":$("#title").val(),
        "price":$("#price").val(),
        "category":$("#category").val(),
        "stock":$("#stock").val(),
        "size_ids":$("#size").val(),
        "color_ids":$("#color").val(),
        "sku":$("#specification").val(),
        "specification":$("#specification").val(),
        "product_images":[
        {
            "image": STORAGE.get(STORAGE.image),
            "is_cover_pic":1
        }
    ],
        "description":$("#description").val(),
    };
    console.log(json);
        AddProduct(json);
}


function __success_product(response) {

  console.log(response);
    if (response.status == true) {
       

     console.log("sucesss");

     alert("product Added");
     setTimeout(function() {
          goto(UI_URL.onboarding7);
       }, 1000);
}
}




//validations
$(document).ready(function(){
    $('#titlecheck').hide();
    $('#pricecheck').hide();
    $('#descriptioncheck').hide();
    $('#specificationcheck').hide();

    var title_err = true;
    var proce_err = true;
    var description_err = true;
    var specification_err = true;



    $('#specification').keyup(function(){
        specification_check();
        
    });

    function specification_check(){
        var specification_val = $('#specification').val();
        if(specification_val.length == ''){
            $('#specificationcheck').show();
            $('#specificationcheck').html("Please fill the specification");
            $('#specificationcheck').focus();
            $('#specificationcheck').css("color","red");
            specification_err = false;
            return false;



        }
        else{
            $('#specificationcheck').hide();

        }

    }


     $('#description').keyup(function(){
        description_check();
        
    });

    function description_check(){
        var description_val = $('#description').val();
        if(description_val.length == ''){
            $('#descriptioncheck').show();
            $('#descriptioncheck').html("Please fill the description");
            $('#descriptioncheck').focus();
            $('#descriptioncheck').css("color","red");
            description_err = false;
            return false;



        }
        else{
            $('#descriptioncheck').hide();

        }

    }

    $('#title').keyup(function(){
        title_check();
        
    });

    function title_check(){
        var title_val = $('#title').val();
        if(title_val.length == ''){
            $('#titlecheck').show();
            $('#titlecheck').html("Please fill the title");
            $('#titlecheck').focus();
            $('#titlecheck').css("color","red");
            title_err = false;
            return false;



        }
        else{
            $('#titlecheck').hide();

        }

    }

    $('#price').keyup(function(){
        price_check();
        
    });

    function price_check(){
        var price_val = $('#price').val();
        if(price_val.length == ''){
            $('#pricecheck').show();
            $('#pricecheck').html("Please fill the price");
            $('#pricecheck').focus();
            $('#pricecheck').css("color","red");
            price_err = false;
            return false;
        


        }
        else{
            $('#pricecheck').hide();

        }

    }

    $('#submitbtn').click(function(){
         title_err = true;
         price_err = true;
         specification_err = true;
         description_err = true;
        title_check();
        price_check();
        specification_check();
        description_check();
        if((title_err == true) && (price_err == true)  && (specification_err == true)  && (description_err == true)){
           addproduct();
        }
        else{
            return false;
        }



    })

});