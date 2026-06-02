export default function () {
    return {
        status: false,
        loading: true,
        init() {
            setInterval(() => {
                if (this.loading)
                {
                    axios.get(window.location.origin + '/checkout/check-status').then(({data}) => {
                        if (data.transaction_status === 1)
                        {
                            this.status = true
                            this.loading = false
                            this.$dispatch('checkout-loading-hide')
                        }
                        if (data.transaction_status === -1)
                        {
                            this.status = false
                            this.loading = false
                            this.$dispatch('checkout-loading-hide')
                        }
                    }).then(() => {
                        feather.replace()
                    })
                }}, 5000)

        },
    }
}
