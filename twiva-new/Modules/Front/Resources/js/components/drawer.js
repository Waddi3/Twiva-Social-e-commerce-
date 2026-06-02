import toggling from './toggling';

export default function () {
    return {
        ...toggling(),
        init () {
            this.id = this.$id('drawer')
            document.addEventListener('alpine:initialized', () => {
                const drawer = document.getElementById(this.id)
                if (drawer) {
                    drawer.classList.remove('hidden')
                }
            })
        }
    }
}
