import * as common from '../common'


export default function (count, productID, submit, userType) {
    return {
        count: count,
        productID: productID,
        userType: userType,
        minus() {
            if (this.count > 1)
            {
                this.count--
                if (submit === 'true')
                {
                    common.manageCartQuantity(productID, false, this.userType)
                }
                else
                {
                    $("#counter-parent").data('count', this.count)
                }
            }

        },
        plus() {

            console.log('plus usertype:', this.userType);

            this.count++

            if (submit === 'true')
            {
                common.manageCartQuantity(productID, true, this.userType)
            }
            else
            {
                let productID = productID
                $("#counter-parent").data('count', this.count)
            }

        }
    }
}

