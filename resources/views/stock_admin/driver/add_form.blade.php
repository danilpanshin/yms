@php
    use App\Models\DriverLicenseCategory;

    $driver_license_categories = DriverLicenseCategory::all();

    $add_route = 'stock_admin.driver.add_post';
    $row_cols = [
        'name' => ['type' => 'text', 'width' => 120, 'label' => 'ФИО водителя'],
        'license_id' => ['type' => 'text', 'width' => 200, 'label' => 'Номер водительских прав'],
        'phone' => ['type' => 'text', 'width' => 200, 'label' => 'Номер телефона водителя'],
        'additional_phone' => ['type' => 'text', 'width' => 200, 'label' => 'Дополнительный номер телефона водителя'],
        'email' => ['type' => 'email', 'width' => 200, 'label' => 'Email водителя'],
    ];
    $add_row_cols = ['name', 'email', 'license_id', 'phone', 'additional_phone'];
@endphp
<form action="{{ secure_url(route($add_route, [], false)) }}" method="post" class="addFormDriver lllAjaxFormSubmit @isset($data){{ $data['lllAjaxFormSubmitName'] }}@endisset">
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
    <div class="mb-3">
        <label for="addFormControlTextareaDriveCategory" class="form-label">Категории прав</label>
        <div class="row">
            @foreach($driver_license_categories as $row)
                <div class="col-12 col-lg-6">
                    <input class="form-check-input" type="checkbox" name="DriveCategory[]" value="{{ $row['id'] }}"/>
                    <div class="d-inline-block badge bg-success"
                         style="width: 40px;">{{ $row['literal'] }}</div> {{ $row['name'] }}
                    <div class="fs-7">{{ $row['description'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</form>