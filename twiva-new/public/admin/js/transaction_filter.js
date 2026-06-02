$(document).ready(function() {
    function order_filter(page,page_name,sort_type,sort_by,query,transaction_type,transaction_status) {
        $.ajax({
            url : "/transactions/filter?page="+page+"&pagename="+page_name+"&sortby="+
            sort_by+"&sorttype="+sort_type+"&query="+query+"&transaction_type="+transaction_type+"&transaction_status="+transaction_status,
            success : function(data) {
                $('tbody').html('');
                $('tbody').html(data);
            }
        })
    }

    $(document).on('click', '.pagination a', function(event) {
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        $('#hidden_page').val(page);
        var page_name = $('#hidden_page_name').val();
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();
        var query = $('#keywords').val();
        var transaction_type = $("#transaction_type").val();
        var transaction_status = $("#transaction_status").val();
        $('li').removeClass('active');
        $(this).parent().addClass('active');
        order_filter(page,page_name,sort_type,column_name,query,transaction_type,transaction_status);
    });

    $(document).on('click', '.sorting', function() {
        var column_name = $(this).data('column_name');
        var sort_type = $(this).data('sorting_type');
        var reverse = '';
        if(sort_type == 'asc') {
            $(this).data('sorting_type', 'desc');
            reverse = 'desc';
        }
        if(sort_type == 'desc') {
            $(this).data('sorting_type', 'asc');
            reverse = 'asc';
        }
        $('#hidden_column_name').val(column_name);
        $('#hidden_sort_type').val(reverse);
        var page = $('#hidden_page').val();
        var page_name = $('#hidden_page_name').val();
        var query = $('#keywords').val();
        var transaction_type = $("#transaction_type").val();
        var transaction_status = $("#transaction_status").val();
        order_filter(page,page_name,reverse,column_name,query,transaction_type,transaction_status);
    });

    $(document).on('keyup', '#keywords', function() {
        var query = $('#keywords').val();
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();
        var page = $('#hidden_page').val();
        var page_name = $('#hidden_page_name').val();
        var transaction_type = $("#transaction_type").val();
        var transaction_status = $("#transaction_status").val();
        order_filter(page,page_name,sort_type,column_name,query,transaction_type,transaction_status);
    });

    $(document).on('change', '#transaction_type', function(event) {
        event.preventDefault();
        var transaction_type = $("#transaction_type").val();
        $('#hidden_transaction_type').val(transaction_type);
        var query = $('#keywords').val();
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();
        var page = $('#hidden_page').val();
        var page_name = $('#hidden_page_name').val();
        var transaction_status = $("#transaction_status").val();
        if($('#keywords').val() == '') {
            query = "";
        }
        order_filter(page,page_name,sort_type,column_name,query,transaction_type,transaction_status);
    });

    $(document).on('change', '#transaction_status', function(event) {
        event.preventDefault();
        var transaction_status = $("#transaction_status").val();
        $('#hidden_transaction_status').val(transaction_type);
        var query = $('#keywords').val();
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();
        var page = $('#hidden_page').val();
        var page_name = $('#hidden_page_name').val();
        var transaction_type = $("#transaction_type").val();
        if($('#keywords').val() == '') {
            query = "";
        }
        order_filter(page,page_name,sort_type,column_name,query,transaction_type,transaction_status);
    });
})
