import toggling from './toggling';
import feather from "feather-icons";

let localSite = window.location.origin + '/';

export default function () {
    return {
        ...toggling(),
        init() {
            this.id = this.$id('dropdown')

            this.$nextTick(() => {
                this.btn = document.getElementById(this.id)
                document.addEventListener('click', (event) => {
                    if (this.show)
                    {
                        const isClickInsideBtn = this.btn.contains(event.target);
                        if (!isClickInsideBtn)
                        {
                            this.show = false
                        }
                    }
                });
            })
        },
        id: null,
        btn: null,
        selected: null,
        items: [],
        get currentItem() {
            return this.selected ? this.items[this.selected] || 'UnKnow' : 'UnKnow'
        },
        changeItem(key) {
            this.selected = key;
            axios.post(localSite + 'products/order-by',
                {
                    sortBy: key,
                    keyWord: $("#searchInput").val(),
                },
            ).then((data) => {
                $("#productSection").fadeOut()
                setTimeout(() => {
                    $("#productSection").html(data.data.products).fadeIn()
                    feather.replace()
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $("#productSection").offset().top
                    }, 500);
                }, 500)
            }).catch((response) => {

            });
        }
    }
}
