$(document).ready(function () {
    function fetch_data(page, sort_type, sort_by, status, subscription, type, eshop_status, query) {
        $.get({
            url: "/pagination/fetch_data",
            data: {
                page: page,
                sortby: sort_by,
                sorttype: sort_type,
                query: query,
                status: status,
                subscription: subscription,
                type: type,
                eshopStatus: eshop_status,
            },
            success: function (data) {
                $('tbody').html('');
                $('tbody').html(data);
            }
        })
    }

    function orderBy() {
        let column_name = $("#hidden_column_name").val();
        let order_type = $("#hidden_sort_type").val();
        let type = $('#hidden_type').val();
        let page = $('#hidden_page').val();
        let eshop_status = $('#hidden_eshop_status').val();
        let query = $('#keywords').val();
        let status = $('#hidden_status').val();
        let subscription = $('#hidden_subscription').val();
        fetch_data(page, order_type, column_name, status, subscription, type, eshop_status, query);
    }
    $("#sort-by-product").on('click', function () {
        $("#sort-by-product-count-down").show();
        $("#sort-by-product").hide();
        $("#sort-by-product-count-down").click();
    });
    $("#sort-by-product-count-down").on('click', function () {
        $("#hidden_column_name").val("product_count")
        $("#hidden_sort_type").val("desc");
        console.log("sort-by-product-count-down =>", $("#hidden_sort_type").val())
        $("#sort-by-product-count-down").hide();
        $("#sort-by-product-count-up").show();
        orderBy();
    });
    $("#sort-by-product-count-up").on('click', function () {
        $("#hidden_column_name").val("product_count");
        $("#hidden_sort_type").val("asc");
        $("#sort-by-product-count-down").show();
        $("#sort-by-product-count-up").hide();
        console.log("sort-by-product-count-up =>", $("#hidden_column_name").val())
        orderBy();
    });
})