function fetch_products(){

    __ajaxregister_http("fetch-products", "", headers(), AJAX_CONF.apiType.GET, "", __success_products);
       
}


function delete_products(id){

    __ajaxregister_http("product/"+id, "", headers(), AJAX_CONF.apiType.DELETE, "", __success_delete);
       
}

function product_details(id){

    __ajaxregister_http("product/"+id, "", headers(), AJAX_CONF.apiType.GET, "", __success_details);
       
}

// product details 


function product_detail(id)
{
   STORAGE.set(STORAGE.pid, id);
    goto(UI_URL.onboarding8);
}

function __success_details(response)
{
console.log(response);
var name = response.data.name;
var price = response.data.price;
var stock = response.data.stock;
var size = response.data.size_details[0].name;
var color = response.data.color_details[0].name;
var description = response.data.description;
var specification = response.data.specification;
var image = response.data.product_images[0].image;
var rating = response.data.rating;
var description = response.data.description;





$('#product-de').append(`

    <div class="product-detail-cont">
    <div class="browse-box">
        <img src="http://twiva-api.apparrant.com/storage/images/products/${image}">
    </div>

    <div class="browse-items">
        <span>+</span>
        <div class="browse-ss">
            <img src="../images/card-images/member-1.jpg">
        </div>
        <div class="browse-ss">
            <img src="../images/card-images/member-1.jpg">
        </div>
        <div class="browse-ss">
            <img src="../images/card-images/member-1.jpg">
        </div>
        <div class="browse-ss">
            <img src="../images/card-images/member-1.jpg">
        </div>
        <div class="browse-ss">
            <img src="../images/card-images/member-1.jpg">
        </div>
    </div>
</div>

<div class="product-specification" id="detailsproduct">
<h2>${name}</h2>
<h3><label>Price: </label><b>$${price}</b></h3>

<div class="spec-row">
    <label>Qty:</label>
    <div><h3>${stock} piece</h3> <button>In Stock</button></div>
</div>
<div class="spec-row">
    <label>Size:</label>
    <div>
        <span>${size}</span>
       
    </div>
</div>


<div class="spec-row">
    <label>Color:</label>
    <div>
        <div class="${color}-volor"></div>
      
    </div>
</div>

<div class="prod-desc">
    <label>Description</label>
    <p>${description}</p>
</div>

<div class="prod-spec">
    <label>Specification</label>
    <ul>
        <li>${specification}</li>
        <li>Consectetur adipiscing elit</li>
        <li>Sit adipiscing morbi</li>
    </ul>
</div>

<div class="prod-spec">
    <label>Review & Rating</label>
    <div class="review-author">
        <span><img src="../images/icons/verified.svg"></span> Jane D.Smith
    </div>
    <div class="date"><img src="../images/icons/star-rate.svg">April 2019</div>
   <p>I was looking for a bright light for the kitchen but wanted some item more modern than a strip light. this one is perfect, very bright and looks great. I can't comment on interlation as I had an electrition instal it. </p>
</div>

<div class="form-field-full">
    <div class="common-button">
    <button class=" white-bttn">Edit</button>
    <button class="purple-btn">Save</button>
    </div>
</div>
</div>


`)

}

// $(response.data.size_details).each(function(index,key){
//     $('#size').append(`

// <div class="spec-row">
//     <label>Size:</label>
//     <div>
//         <span>${key.name}</span>
       
//     </div>
// </div>
// `)
// })



//delete prooducts
function delete_product(){
    var id = 80;
    delete_products(id);
}

function __success_delete(response)
{
    console.log(response);
}


//fetch products

function __success_products(response){
    // console.log(response);
    $('#products').html('');
    $(response.data).each(function(index,key){

        $('#products').append(`
            

           <tr> 
                <td><img src="http://twiva-api.apparrant.com/storage/images/products/${key.product_images[0].image}"></td>
<<<<<<< HEAD
                <td>${key.id}</td>
=======
                
>>>>>>> a2e88abe349c6067f7d7184940b1087ac261e181
                <td>${key.name}</td>
                <td>${key.rating}<img src="../images/icons/star.svg"></td>
                <td>${key.stock}</td>
                <td>$${key.price}</td>
                <td class="green-txt"><button>In Stock</button></td>
                <td>
                    <div class="actions">
                     <a id="${key.id}" onclick="product_detail(this.id)"><img src="../images/icons/edit-profile.svg"></a>
                        <a id="${key.id}" onclick="delete_products(this.id)"><img src="../images/icons/trash-red.svg"></a>
                    </div> 
                </td>
              </tr>

             `
           
            )
         

    })
}
