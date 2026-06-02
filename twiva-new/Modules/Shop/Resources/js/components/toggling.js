import * as common from '../common'


function removeFromCard(product_id) {
    console.log($(".deleteBtn[data-productid=" + product_id + ']').data('is-directly-clicked'))
    $(".deleteBtn[data-productid=" + product_id + ']').data('is-directly-clicked','false')
    console.log($(".deleteBtn[data-productid=" + product_id + ']').data('is-directly-clicked'))
    $(".deleteBtn[data-productid=" + product_id + ']').click()

}

export default function (isInCard = false, productId) {
    return {
        isInCard: isInCard,
        show: false,
        toggle() {
            if (typeof productId == 'undefined')
            {
                console.log("productId=>", productId)
                this.show = !this.show
                return;
            }
            if (this.isInCard)
            {
                console.log("productId=>", productId)
                console.log("pressed")
                removeFromCard(productId)
            }
            else
            {
                common.addToCart(productId)
            }
            this.show = !this.show
            this.isInCard = !this.isInCard

        }
    }
}

