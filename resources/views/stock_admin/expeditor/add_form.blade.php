@php
    $add_route = 'stock_admin.expeditor.add_post';
    $row_cols = [
        'name' => ['type' => 'text', 'width' => 120, 'label' => 'ФИО экспедитора'],
        'phone' => ['type' => 'text', 'width' => 200, 'label' => 'Номер телефона экспедитора'],
        'email' => ['type' => 'email', 'width' => 200, 'label' => 'Email экспедитора'],
    ];
    $add_row_cols = ['name', 'email', 'phone'];
@endphp
<form action="{{ route($add_route) }}" method="post" class="addFormExpeditor lllAjaxFormSubmit @isset($data){{ $data['lllAjaxFormSubmitName'] }}@endisset">
    @csrf
    @foreach($add_row_cols as $row_cols_name)
        @if(in_array($row_cols[$row_cols_name]['type'], ['text', 'email']))
            <div class="mb-3">
                <label for="addFormControlInput{{ ucfirst($row_cols_name) }}"
                       class="form-label">{{ $row_cols[$row_cols_name]['label'] }}</label>
                <input name="{{ $row_cols_name }}" type="{{ $row_cols[$row_cols_name]['type'] }}" class="form-control"
                       id="addFormControlInput{{ ucfirst($row_cols_name) }}">
            </div>
        @elseif($row_cols[$row_cols_name]['type'] == 'textarea')
            <div class="mb-3">
                <label for="addFormControlInput{{ ucfirst($row_cols_name) }}"
                       class="form-label">{{ $row_cols[$row_cols_name]['label'] }}</label>
                <textarea name="{{ $row_cols_name }}" class="form-control"
                          id="addFormControlInput{{ ucfirst($row_cols_name) }}"></textarea>
            </div>
        @endif
    @endforeach
</form>