export default function () {
    return {
        sections: [],
        active: null,
        init () {
            this.$nextTick(() => {
                for (const section of this.sections) {
                    const el = document.getElementById(section)
                    el.style.maxHeight = `${el.scrollHeight + 20}px`
                }
            })
        },
        change (section) {
            this.active = section
        }
    }
}
