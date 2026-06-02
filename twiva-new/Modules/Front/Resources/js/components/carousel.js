import Flickity from 'flickity/js/index'

export default function () {
    return {
        init() {
            const flickity = new Flickity(this.$refs.carousel, {
                autoPlay: true,
                // contain: true,
                wrapAround: true,
                prevNextButtons: false,
                pageDots: false,
                imagesLoaded: true
            });
        }
    }
}
