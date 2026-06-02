import feather from "feather-icons";
import * as common from './common'
import {updateCountQuantityOfProduct} from "./common";

var $body = $('body');

$body.on('click', '.deleteBtn', function () {
    common.deleteProductFromCart($(this))
});
$body.on('click', '#addFromShowBtn', function () {
    common.addFromShow()
});
$body.on('click', '#buyFromShowBtn', function () {
    common.BuyFromShowBtn($(this))
});
$body.on('change', '.checkout_product_size', function () {
    common.updateSizeAndColorFromCheckOut($(this), 'size')
});
$body.on('change', '.checkout_product_color', function () {
    common.updateSizeAndColorFromCheckOut($(this), 'color')
});

$body.on('keyup change', '.update_count_quantity_of_product', function () {
    let value = $(this).val();
    if (value.length !== 0) {
        if ($(this).val() < 1) {
            value = 1;
        }
        $('.update_count_quantity_of_product').val(value);
        common.updateCountQuantityOfProduct($(this), value, $(this).data('product_id'))
    }
});

$("input[name='address_id']/*, input[name='address_type']*/").click(function() {
    common.updateAddress($("input[name='address_id']:checked").val());
    //common.getUserCart();
});

$("#city_id").on('change', function () {
    common.updateAddress( $("#city_id").val(), true, $('#partner').val() );
});

$("#partner").on('change', function () {
    console.log('address type', $("input[name='address_type']:checked").val());
    console.log("address_id", $("input[name='address_id']:checked").val());
    console.log("partner", $("#partner").val());
    if( $("#address_type").val() === "exists" ) {
        common.updateAddress($("input[name='address_id']:checked").val(), false, $("#partner").val());
    }
    else {
        common.updateAddress($("input[name='address_id']:checked").val(), true, $("#partner").val());
    }

});

function updatePartner() {
    if( $("#address_type").val() === "exists" ) {
        common.updateAddress($("input[name='address_id']:checked").val(), false, $("#partner").val());
    }
    else {
        common.updateAddress($("input[name='address_id']:checked").val(), true, $("#partner").val());
    }
}

window.updatePartner = updatePartner;


// ####################################
feather.replace()
common.getUserCart();

