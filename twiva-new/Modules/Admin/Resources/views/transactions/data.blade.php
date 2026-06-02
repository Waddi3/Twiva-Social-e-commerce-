@foreach($transactions as $transaction)
    <tr>
        <td>{{$transaction->id}}</td>
        <td>{{$transaction->requestTypeName}}</td>
        <td>KEH {{$transaction->transaction_amount}}</td>
        <td>Received</td>
        <td>{{$transaction->userData ?? "NA"}}</td>
        <td>{{$transaction->created_at->format('d-m-Y')}}</td>
        <td>{{$transaction->created_at->format('H:m')}}</td>
        <td class="{{$transaction->transactionStatus['class']}}">
            <p>{{$transaction->transactionStatus['title'] }}</p>
        </td>
    </tr>
@endforeach
<tr>
    <td colspan="100">
        <div class="pagination">
            <div class="col-sm-5">
                <div class="col-sm-7">
                    <div class="paging_simple_numbers" id="example_paginate">
                        {{ $transactions->links('admin::layouts.pagination')}}
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>