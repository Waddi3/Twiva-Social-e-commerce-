export default function () {
    return {
        fixed: false,
        fix () {
            this.fixed = !this.fixed
        }
    }
}
