import * as common from '../common'


export default function (count, productID, submit) {
    return {
        count: count,
        productID: productID,
        minus() {
            if (this.count > 1)
            {
                this.count--
                if (submit === 'true')
                {
                    common.manageCartQuantity(productID, false)
                }
                else
                {
                    $("#counter-parent").data('count', this.count)
                }
            }

        },
        plus() {

            this.count++

            if (submit === 'true')
            {
                common.manageCartQuantity(productID, true)
            }
            else
            {
                let productID = productID
                $("#counter-parent").data('count', this.count)
            }

        }
    }
}

