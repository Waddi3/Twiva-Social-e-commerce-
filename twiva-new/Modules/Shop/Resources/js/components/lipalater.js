export default function () {
    return {
        show: false,
        code: false,
        valid: {
            sendCode: false,
            form: false
        },
        loading: false,
        form: {
            id: null,
            period: null,
            mobile: null,
            code: null
        },
        init () {
            this.$watch('form', (value) => {
                this.valid.sendCode = value.id && value.period && !!value.mobile
            })
            this.$watch('form', (value) => {
                this.valid.form = value.id && value.period && value.mobile && value.code && value.code.toString().length > 6
            })
        },
        toggle () {
            this.show = !this.show
        },
        sendCode () {

        },
        save () {

        }
    }
}
