$(document).ready(function() {
    function product_filter(page, sort_type, sort_by, status, page_name, query, rating, category) {
        $.ajax({
            url : "/pagination/product_filter?page="+page+"&sortby="+
                    sort_by+"&sorttype="+sort_type+"&query="+query+"&status="+status+"&pagename="+page_name+"&rating="+rating+"&category="+category,
            success : function(data) {
                $('tbody').html('');
                $('tbody').html(data);
            }
        })
    }

    function clear_icon()
    {
        $('#id_icon').html('');
        $('#amount_icon').html('');
    }

    $(document).on('keyup', '#keywords', function() {
        var query = $('#keywords').val();
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();
        var page = $('#hidden_page').val();
        var status = $('#hidden_status').val();
        var page_name = $('#hidden_page_name').val();
        var rating = $('#hidden_rating').val();
        var category = $('#hidden_category').val();
        product_filter(page, sort_type, column_name, status, page_name, query, rating, category);
    })

    $(document).on('click', '.sorting', function() {
        var column_name = $(this).data('column_name');
        var order_type = $(this).data('sorting_type');
        var reverse_order = '';
        if(order_type == 'asc') {
            $(this).data('sorting_type', 'desc');
            reverse_order = 'desc';
            clear_icon();
            // $('#'+column_name+'_icon').html('<span class="fa fa-angle-down"></span>');
        }
        if(order_type == 'desc') {
            $(this).data('sorting_type', 'asc');
            reverse_order = 'asc';
            clear_icon();
            // $('#'+column_name+'_icon').html('<span class="fa fa-angle-up"></span>');
        }
        $('#hidden_column_name').val(column_name);
        $('#hidden_sort_type').val(reverse_order);
        var page = $('#hidden_page').val();
        var query = $('#keywords').val();
        var status = $('#hidden_status').val();
        var page_name = $('#hidden_page_name').val();
        var rating = $('#hidden_rating').val();
        var category = $('#hidden_category').val();
        product_filter(page, reverse_order, column_name, status, page_name, query, rating, category);
    });

    $(document).on('click', '.pagination a', function(event) {
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        $('#hidden_page').val(page);
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();
        var query = $('#keywords').val();
        var status = $('#hidden_status').val();

        $('li').removeClass('active');
        $(this).parent().addClass('active');
        var page_name = $('#hidden_page_name').val();
        var rating = $('#hidden_rating').val();
        var category = $('#hidden_category').val();

        product_filter(page, sort_type, column_name, status, page_name, query, rating, category);
    });

    $(document).on('change', '#status', function(event) {
        event.preventDefault();
        var status = $("#status").val();
        $('#hidden_status').val(status);
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();
        var query = $('#keywords').val();
        if($('#keywords').val() == '') {
            query = "";
        }
        var page = $('#hidden_page').val();
        var page_name = $('#hidden_page_name').val();
        var category = $('#hidden_category').val();
        var rating = $('#hidden_rating').val();

        product_filter(page, sort_type, column_name, status, page_name, query, rating, category);
    })

    $(document).on('change', '#rating', function(event) {
        event.preventDefault();
        var rating = $("#rating").val();
        $('#hidden_rating').val(rating);
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();
        var query = $('#keywords').val();
        if($('#keywords').val() == '') {
            query = "";
        }
        var page = $('#hidden_page').val();
        var page_name = $('#hidden_page_name').val();
        var category = $('#hidden_category').val();
        var status = $('#hidden_status').val();

        product_filter(page, sort_type, column_name, status, page_name, query, rating, category);
    })

    $(document).on('change', '#category', function(event) {
        event.preventDefault();
        var category = $("#category").val();
        $('#hidden_category').val(category);
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();
        var query = $('#keywords').val();
        if($('#keywords').val() == '') {
            query = "";
        }
        var page = $('#hidden_page').val();
        var page_name = $('#hidden_page_name').val();
        var status = $('#hidden_status').val();
        var rating = $('#hidden_rating').val();

        product_filter(page, sort_type, column_name, status, page_name, query, rating, category);
    })

})
