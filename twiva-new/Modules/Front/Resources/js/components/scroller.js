export default function () {
    return {
        activeClass: null,
        scroll (e) {
            const element = e.target
            let target = element.getAttribute('data-target')
            target = document.querySelector(`#${target}`)

            if (this.activeClass) {
                for (let child of element.parentElement.children) {
                    child.classList.remove(this.activeClass)
                }
                element.classList.add(this.activeClass)
            }

            scroll({
                top: target.offsetTop - 10,
                behavior: 'smooth',
            })
        }
    }
}
