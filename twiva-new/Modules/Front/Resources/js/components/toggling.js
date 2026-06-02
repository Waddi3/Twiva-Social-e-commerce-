import * as common from '../common'


function removeFromCard(product_id, userType) {
    console.log($(".deleteBtn[data-productid=" + product_id + ']').data('is-directly-clicked'))
    $(".deleteBtn[data-productid=" + product_id + ']').data('is-directly-clicked','false');
    $(".deleteBtn[data-productid=" + product_id + ']').data('usertype', userType);
    console.log($(".deleteBtn[data-productid=" + product_id + ']').data('is-directly-clicked'))
    $(".deleteBtn[data-productid=" + product_id + ']').click()

}

export default function (isInCard = false, productId, userType) {
    return {
        isInCard: isInCard,
        show: false,
        userType: userType,
        toggle() {
            console.log(userType);
            if (typeof productId == 'undefined')
            {
                console.log("productId=>", productId, this.userType)
                this.show = !this.show
                return;
            }
            if (this.isInCard)
            {
                console.log("productId=>", productId, this.userType)
                console.log("pressed")
                removeFromCard(productId, userType)
            }
            else
            {
                common.addToCart(productId, this.userType)
            }
            this.show = !this.show
            this.isInCard = !this.isInCard

        }
    }
}

