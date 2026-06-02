export default function () {
    return {
        copied: false,
        input: '.copy-inner-text',
        delay: 2000,
        copy () {
            const input = document.querySelector(this.input)
            input.select()
            try {
                document.execCommand('copy');
            } catch(err) {
                console.log('Oops, unable to copy');
            }
            this.copied = true
            setTimeout(() => {
                this.copied = false
            }, this.delay)
        }
    }
}
