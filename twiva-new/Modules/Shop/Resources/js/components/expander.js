import toggling from "./toggling";

export default function () {
    return {
        ...toggling(),
        init () {
            this.id = this.$id('expander')
            this.$nextTick(() => {
                const text = document.getElementById(this.id)
                text.style.maxHeight = `${text.scrollHeight}px`
            })
        },
        id: null
    }
}
