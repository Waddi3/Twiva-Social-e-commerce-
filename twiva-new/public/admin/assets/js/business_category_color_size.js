function Get_Category(json){

    __ajax_httpproduct("categories", json, headers(), AJAX_CONF.apiType.Get, "", __success_category);      
}


function __success_category(response) {

<<<<<<< HEAD
  console.log(response);$(response.data).each(function(index,key){
=======
  $(response.data).each(function(index,key){
>>>>>>> a2e88abe349c6067f7d7184940b1087ac261e181
        

        $('#category').append(`
            

            <option value="${key.id}"> ${key.name}</option>

       `)
         

    })



}