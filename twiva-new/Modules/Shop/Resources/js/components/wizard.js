export default function () {
    return {
        loading: false,
        loadingTitle: null,
        steps: [],
        current: null,
        prev: null,
        init () {
            this.$watch('current', (value) => {
                const index = this.steps.findIndex(value)
                this.prev = index > 0 ? this.steps[(index - 1)] : null
            })
        },
        goTo (step) {
            this.current = step
        },
        login () {

        },
    }
}
