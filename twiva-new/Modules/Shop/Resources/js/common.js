import feather from "feather-icons";

require('./bootstrap')

let localSite = window.location.origin + '/';

export function updateAddress(addressId, isCity = false, partnerId) {
    $(".shipping_fees").text("Calculating...");

    var parameters = {};
    if( isCity ) {
        parameters = {
            city_id: addressId,
            partner_id: partnerId
        };
    }
    else {
        parameters = {
            address_id: addressId,
            partner_id: partnerId
        };
    }

    axios
        .post(localSite + 'update-shipping-address', parameters)
        .then((data) => {
            if( data.data.status ) {
                getUserCart();
            }
        }).catch(() => {
    });
}

export function getUserCart() {
    $(".shipping_fees").text("Calculating...");
    axios.post(localSite + 'get-card-items').then((data) => {
        $("#cardDivContent").html(data.data.products)
      //  $("#basketCount").html(data.data.count)
        $(".basketCount").html(data.data.count)
        $("#total_price").text("KSH " + data.data.products_price)
        $(".total_with_shipping_fees").text("KSH " + data.data.total_with_shipping_fees)
        // $("#total_items").text(data.data.count + " items")
        // $("#total_items_price").text("KSH " + data.data.products_price);
        $(".total_items_price").text("KSH " + data.data.products_price);
        // $("#total_items2").text(data.data.count)
        $(".total_items2").text(data.data.count);
        // $(".total_items2").val(data.data.count);
        $(".transaction_fees").text("KSH " + data.data.transaction_fees)
        if( data.data.shipping_fees == 0  || data.data.shipping_fees == null) {
            $(".shipping_fees").closest('.flex').hide();
        }
        else {
            $(".shipping_fees").closest('.flex').show();
            $(".shipping_fees").text("KSH " + data.data.shipping_fees)
        }
        // $(".shipping_fees").text("KSH " + data.data.shipping_fees)
        // console.log("Shipping fees: " + data.data.shipping_fees);
        $(".total_with_shipping_fees_and_transaction_fees").text("KSH " + data.data.total_with_shipping_fees_and_transaction_fees)

        if(isNumeric(data.data.coupon_with_shipping_fees))
        {
            $(".total_with_shipping_fees").addClass('line-through');
            $(".coupon_with_shipping_fees").text("KSH " + data.data.coupon_with_shipping_fees);
            $(".with-coupon").show();
        } else {
            $(".total_with_shipping_fees").removeClass('line-through');
            $(".coupon_with_shipping_fees").hide;
            $(".with-coupon").hide();
        }
        feather.replace()
    }).catch(() => {
        feather.replace()

    });
    feather.replace()

}

export function deleteProductFromCart(cardDiv) {
    let productID = cardDiv.data('productid')
    let isDirectlyClicked = cardDiv.data('is-directly-clicked');
    axios.post(localSite + 'delete-from-card',
        {product_id: productID},
    ).then(() => {
        if (isDirectlyClicked === true) {
            $(".delete-toggle-btn[data-product=" + $(".deleteBtn[data-productid=" + productID + ']').data('productid') + ']').click()
        }
        cardDiv.parent().parent().parent().remove()
        $(".image[data-product=" + productID + ']').remove()

      //  $("#basketCount").text(parseInt($("#basketCount").text()) - 1)
        $(".basketCount").text(parseInt($(".basketCount").text()[0]) - 1)
        getUserCart()
    }).catch(() => {
    });

}


export function manageCartQuantity(productID, flag) {

    axios.post(localSite + 'update-card-quantity',
        {
            product_id: productID,
            increase: flag,
            shop_domain: window.location.host.split('.')[0],
        })
        .then(($response) => {
            getUserCart()
            updateQuantityOfProduct($response.data.cart)
        })
}

export function addToCart(product_id) {

    axios.post(localSite + 'add-to-card',
        {
            product_id: product_id,
            shop_domain: window.location.host.split('.')[0],
            quantity: 1,
            should_redirect: false
        },)
        .then(() => {
          //  $("#basketCount").text(parseInt($("#basketCount").text()) + 1)
            $(".basketCount").text(parseInt($(".basketCount").text()[0]) + 1)
            getUserCart()
        });
}


export function addFromShow() {
    let countParent = $("#counter-parent");
    let count = countParent.data('count')
    let productId = countParent.data('product_id')
    let productSizeId = $("#productSize").val()
    let productColorId = $("#productColor").val()
    if (count > 0) {
        axios.post(localSite + 'add-to-card',
            {
                product_id: productId,
                color_id: productColorId ?? null,
                size_id: productSizeId ?? null,
                shop_domain: window.location.host.split('.')[0],
                quantity: count,
                should_redirect: false
            },)
            .then(() => {
                getUserCart()
            });
    }

}

export function BuyFromShowBtn(el) {
    let countParent = $("#counter-parent");
    let count = countParent.data('count')
    let productId = countParent.data('product_id')
    let productSizeId = $("#productSize").val()
    let productColorId = $("#productColor").val()
    if (count > 0) {
        axios.post(localSite + 'add-to-card',
            {
                product_id: productId,
                color_id: productColorId ?? null,
                size_id: productSizeId ?? null,
                shop_domain: window.location.host.split('.')[0],
                quantity: count,
                should_redirect: true
            })
            .then((data) => {
                window.location.replace(data.data.redirect_url)
            });
    }
}


export function updateSizeAndColorFromCheckOut(el, updatedKey) {


    if (updatedKey === 'size') {
        let productSizeId = el.val()
        let productId = el.data('productid')
        axios.post(localSite + 'update-card-quantity',
            {
                product_id: productId,
                size_id: productSizeId ?? null,
                shop_domain: window.location.host.split('.')[0],
            });
    }
    if (updatedKey === 'color') {
        let productColorId = el.val()
        let productId = el.data('productid')
        axios.post(localSite + 'update-card-quantity',
            {
                product_id: productId,
                color_id: productColorId ?? null,
                shop_domain: window.location.host.split('.')[0],
            });
    }
}

export function updateCountQuantityOfProduct(el, increment_value, product_id) {
    axios.post(localSite + 'update-card-quantity',
        {
            product_id: product_id,
            increase_multiple: increment_value ?? null,
            shop_domain: window.location.host.split('.')[0],
        })
        .then(($response) => {
            getUserCart()
            updateQuantityOfProduct($response.data.cart)
        })
        .catch(function (error) {
            console.log(error);
        });
}

export function updateQuantityOfProduct(cart) {
    $.each(cart, function (product_id, item) {
        $(`#quantity_product_${item.product_id}`).val(item.quantity)
        $(`.quantity_product_${item.product_id}`).val(item.quantity)
    });
}
