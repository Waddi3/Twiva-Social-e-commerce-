
@if ($paginator->hasPages())
<ul class="pagination">
{{-- <ul class="pager"> --}}

    @if ($paginator->onFirstPage())
        {{-- <li class="disabled"><span>← Previous</span></li> --}}
        <li class="paginate_button previous disabled" id="example_previous">
            <a href="#" aria-controls="example" data-dt-idx="0" tabindex="0">
                <img src="{{ asset('admin/images/icons/left-arrow.svg') }}" alt="" style="width:32px; height:32px">
            </a>
        </li>
    @else
        {{-- <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">← Previous</a></li> --}}
        <li class="paginate_button previous" id="example_previous">
            <a href="{{ $paginator->previousPageUrl() }}" aria-controls="example" data-dt-idx="0" tabindex="0">
                <img src="{{ asset('admin/images/icons/left-gray-arrow.png') }}" alt=""  style="width:32px; height:32px">
            </a>
        </li>
    @endif



    @foreach ($elements as $element)

        @if (is_string($element))
            <li class="disabled"><span>{{ $element }}</span></li>

        @endif



        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    {{-- <li class="active my-active"><span>{{ $page }}</span></li> --}}
                    <li class="paginate_button active my-active">
                        <a href="#" aria-controls="example" data-dt-idx="1" tabindex="0">{{ $page }}</a>
                    </li>
                @else
                    {{-- <li><a href="{{ $url }}">{{ $page }}</a></li> --}}
                    <li class="paginate_button">
                        <a href="{{ $url }}" aria-controls="example" data-dt-idx="2" tabindex="0">{{ $page }}</a>
                    </li>
                @endif
            @endforeach
        @endif
    @endforeach



    @if ($paginator->hasMorePages())
        {{-- <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">Next →</a></li> --}}
        <li class="paginate_button next" id="example_next">
            <a href="{{ $paginator->nextPageUrl() }}" aria-controls="example" data-dt-idx="7" tabindex="0">
                <img src="{{ asset('admin/images/icons/right-arrow.svg') }}" alt="" style="width:32px; height:32px">
            </a>
        </li>
    @else
        {{-- <li class="disabled"><span>Next →</span></li> --}}
        <li class="paginate_button next disabled" id="example_next">
            <a href="#" aria-controls="example" data-dt-idx="7" tabindex="0">
                <img src="{{ asset('admin/images/icons/right-arrow.svg') }}" alt="" style="width:32px; height:32px">
            </a>
        </li>
    @endif
</ul>
@endif
