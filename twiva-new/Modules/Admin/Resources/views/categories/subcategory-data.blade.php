@foreach ($subcategories as $subcategory)
    <tr>
        <td>{{ $subcategory->id }}</td>
        <td>{{ $subcategory->name }}</td>
        <td class="t-action">
            <label class="switch">
                <input data-id="{{ $subcategory->id }}" class="toggle-activation" type="checkbox"
                    {{ $subcategory->is_active ? 'checked' : '' }}>
                <span class="slider round"></span>
            </label>
        </td>
        <td class="t-action">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.subcategory.edit', $subcategory->id) }}"><img
                        src="{{ asset('admin/images/icons/edit-icon.svg') }}" alt=""></a>
            </div>
        </td>
    </tr>
@endforeach
<tr>
    <td colspan="100">
        <div class="pagination">
            <div class="col-sm-5 p-0">
                <div class="col-sm-7 p-0">
                    <div class="paging_simple_numbers" id="example_paginate">
                        {{ $subcategories->links('admin::layouts.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
