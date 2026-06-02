$(document).ready(function () {
    function fetch_data(page, sort_type, sort_by, status, subscription, type, eshop_status, query) {
        $.ajax({
            url: "/pagination/fetch_data?page=" + page + "&sortby=" +
                sort_by + "&sorttype=" + sort_type + "&query=" + query + "&status=" + status + "&subscription=" + subscription + "&type=" + type + "&eshopStatus=" + eshop_status,
            success: function (data) {
                $('tbody').html('');
                $('tbody').html(data);
            }
        })
    }

    function clear_icon() {
        $('#id_icon').html('');
        $('#email_icon').html('');
    }

    $(document).on('keyup', '#keywords', function () {
        let query = $('#keywords').val();
        let column_name = $('#hidden_column_name').val();
        let type = $('#hidden_type').val();
        let eshop_status = $('#hidden_eshop_status').val();
        let sort_type = $('#hidden_sort_type').val();
        let page = $('#hidden_page').val();
        let status = $('#hidden_status').val();
        let subscription = $('#hidden_subscription').val();
        fetch_data(page, sort_type, column_name, status, subscription, type, eshop_status, query);
    })

    $(document).on('click', '.sorting', function () {
        let column_name = $(this).data('column_name');
        let order_type = $(this).data('sorting_type');
        let reverse_order = '';
        if (order_type === 'asc')
        {
            $(this).data('sorting_type', 'desc');
            reverse_order = 'desc';
            clear_icon();
            // $('#'+column_name+'_icon').html('<span class="fa fa-angle-down"></span>');
        }
        if (order_type === 'desc')
        {
            $(this).data('sorting_type', 'asc');
            reverse_order = 'asc';
            clear_icon();
            // $('#'+column_name+'_icon').html('<span class="fa fa-angle-up"></span>');
        }
        $('#hidden_column_name').val(column_name);
        $('#hidden_sort_type').val(reverse_order);
        $('#hidden_sort_by').val(reverse_order);
        let type = $('#hidden_type').val();
        let page = $('#hidden_page').val();
        let eshop_status = $('#hidden_eshop_status').val();
        let query = $('#keywords').val();
        let status = $('#hidden_status').val();
        let subscription = $('#hidden_subscription').val();
        fetch_data(page, reverse_order, column_name, status, subscription, type, eshop_status, query);
    });
    $(document).on('click', '.order-by', function () {
        let column_name = $("#hidden_column_name").val("product_count");
        let order_type = $(this).data('sorting_type');
        let reverse_order = '';
        if (order_type === 'asc')
        {
            $(this).data('sorting_type', 'desc');
            reverse_order = 'desc';
        }
        if (order_type === 'desc')
        {
            $(this).data('sorting_type', 'asc');
            reverse_order = 'asc';
        }
        $('#hidden_column_name').val(column_name);
        $('#hidden_sort_type').val(reverse_order);
        $('#hidden_sort_by').val(reverse_order);
        let type = $('#hidden_type').val();
        let page = $('#hidden_page').val();
        let eshop_status = $('#hidden_eshop_status').val();
        let query = $('#keywords').val();
        let status = $('#hidden_status').val();
        let subscription = $('#hidden_subscription').val();
        fetch_data(page, reverse_order, column_name, status, subscription, type, eshop_status, query);
    });

    $(document).on('click', '.pagination a', function (event) {
        event.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        $('#hidden_page').val(page);
        let eshop_status = $('#hidden_eshop_status').val();
        let type = $('#hidden_type').val();
        let column_name = $('#hidden_column_name').val();
        let sort_type = $('#hidden_sort_type').val();
        let query = $('#keywords').val();
        let status = $('#hidden_status').val();
        let subscription = $('#hidden_subscription').val();

        $('li').removeClass('active');
        $(this).parent().addClass('active');
        fetch_data(page, sort_type, column_name, status, subscription, type, eshop_status, query);
    });

    $(document).on('change', '#status', function (event) {
        event.preventDefault();
        let status = $("#status").val();
        $('#hidden_status').val(status);
        let column_name = $('#hidden_column_name').val();
        let sort_type = $('#hidden_sort_type').val();
        let eshop_status = $('#hidden_eshop_status').val();
        let query = $('#keywords').val();
        if ($('#keywords').val() === '')
        {
            query = "";
        }
        let page = $('#hidden_page').val();
        let type = $('#hidden_type').val();
        let subscription = $('#hidden_subscription').val();
        // console.log(page, sort_type, column_name, status, query)
        fetch_data(page, sort_type, column_name, status, subscription, type, eshop_status, query);
    })

    $(document).on('change', '#subscription', function (event) {
        event.preventDefault();
        let subscription = $("#subscription").val();
        $('#hidden_subscription').val(subscription);
        let column_name = $('#hidden_column_name').val();
        let sort_type = $('#hidden_sort_type').val();
        let eshop_status = $('#hidden_eshop_status').val();
        let type = $('#hidden_type').val();
        let query = $('#keywords').val();
        let status = $('#hidden_status').val();
        let page = $('#hidden_page').val();

        console.log(page, sort_type, column_name, status, subscription, type, eshop_status, query)
        fetch_data(page, sort_type, column_name, status, subscription, type, eshop_status, query);
    })

    $(document).on('change', '#eshop_status', function (event) {
        event.preventDefault();
        let eshop_status = $("#eshop_status").val();
        $('#hidden_eshop_status').val(eshop_status);
        let column_name = $('#hidden_column_name').val();
        let sort_type = $('#hidden_sort_type').val();
        let subscription = $('#hidden_subscription').val();
        let type = $('#hidden_type').val();
        let query = $('#keywords').val();
        let status = $('#hidden_status').val();
        let page = $('#hidden_page').val();

        console.log(page, sort_type, column_name, status, subscription, type, eshop_status, query)
        fetch_data(page, sort_type, column_name, status, subscription, type, eshop_status, query);
    })
})
