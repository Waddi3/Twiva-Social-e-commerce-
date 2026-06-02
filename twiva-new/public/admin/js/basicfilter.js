$(document).ready(function() {
    function basic_filter(page, sort_type, sort_by, status, page_name, query, id) {
        $.ajax({
            url : "/pagination/basic_filter?page="+page+"&sortby="+
                    sort_by+"&sorttype="+sort_type+"&query="+query+"&status="+status+"&pagename="+page_name+"&id="+id,
            success : function(data) {
                $('tbody').html('');
                $('tbody').html(data);
            }
        })
    }

    function clear_icon()
    {
        $('#id_icon').html('');
        $('#email_icon').html('');
    }

    $(document).on('keyup', '#keywords', function() {
        var query = $('#keywords').val();
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();
        var page = $('#hidden_page').val();
        var status = $('#hidden_status').val();
        var page_name = $('#hidden_page_name').val();
        var id = $('#hidden_id').val();
        basic_filter(page, sort_type, column_name, status, page_name, query, id);
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
        var id = $('#hidden_id').val();
        basic_filter(page, reverse_order, column_name, status, page_name, query, id);
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
        var id = $('#hidden_id').val();
        basic_filter(page, sort_type, column_name, status, page_name, query, id);
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
        var id = $('#hidden_id').val();
        // console.log(page, sort_type, column_name, status, query)
        basic_filter(page, sort_type, column_name, status, page_name,  query, id);
    })

})
