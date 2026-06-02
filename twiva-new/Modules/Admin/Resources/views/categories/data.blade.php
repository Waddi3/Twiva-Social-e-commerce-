@foreach ($categories as $category)
    <tr>
        <td>{{ $category->id }}</td>
        <td>{{ $category->name }}</td>
        <td class="t-action">
            <label class="switch">
                <input data-id="{{ $category->id }}" class="toggle-activation" type="checkbox"
                    {{ $category->is_active ? 'checked' : '' }}>
                <span class="slider round"></span>
            </label>
        </td>
        <td>
            <label class="switch">
                <input data-id="{{ $category->id }}" class="toggle-featured" type="checkbox"
                    {{ $category->is_featured ? 'checked' : '' }}>
                <span class="slider round"></span>
            </label>
        </td>
        <td class="t-action">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.category.edit', ['id' => $category->id]) }}"><img
                        src="{{ asset('admin/images/icons/edit-icon.svg') }}" alt=""></a>
            </div>
        </td>
        {{-- <td>{{ $category->created_at->format('d/m/y') }}</td> --}}
    </tr>
@endforeach
<tr>
    <td colspan="100">
        <div class="pagination">
            <div class="col-sm-5 p-0">
                <div class="col-sm-7 p-0">
                    <div class="paging_simple_numbers" id="example_paginate">
                        {{ $categories->links('admin::layouts.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
