export default function () {
    return {
        valid: false,
        loading: false,
        form: {
            mobile: null,
            prePhone: 254
        },
        init() {
            this.$watch('form', (value) => {
                this.valid = value.prePhone && !!value.mobile
            })
        },
        save() {
        }
    }
}
