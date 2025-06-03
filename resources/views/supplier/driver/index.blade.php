@extends('layout.app')

@section('title', 'Водители')

@section('content')
    @php
        $add_route = 'supplier.driver.add_post';
        $edit_route = 'supplier.driver.edit_post';
        $delete_route = 'supplier.driver.delete_post';
        $restore_route = 'supplier.driver.restore_post';
        $view_route = 'supplier.driver.one';
        function delete_confirm($name, $id): void { echo "Вы действительно хотите перенести в архив водителя {$name} под номером {$id}"; }
        function restore_confirm($name, $id): void { echo "Вы действительно хотите восстановить водителя {$name} под номером {$id}"; }
        $list_arr = $users ?? ($list_arr ?? []);
        $row_cols = [
            'id' => ['type' => 'text', 'width' => 60, 'label' => 'Номер'],
            'name' => ['type' => 'text', 'width' => false, 'label' => 'ФИО водителя'],
            'license_id' => ['type' => 'text', 'width' => 200, 'label' => 'Номер вод. прав'],
            'phone' => ['type' => 'text', 'width' => 250, 'label' => 'Номер тел. водителя'],
            'email' => ['type' => 'email', 'width' => 400, 'label' => 'Email водителя'],
        ];
        $add_row_cols = ['name', 'email', 'license_id', 'phone'];
        $list_row_cols = ['id', 'name', 'email', 'license_id', 'phone'];
        $edit_row_cols = ['name', 'email', 'license_id', 'phone'];
        $view_row_cols = ['id', 'name', 'email', 'license_id', 'phone'];
    @endphp
    <div class="admin_users_section">
        <div class="row">
            <div class="col-12 col-sm-6 text-start">
                @if(Route::currentRouteName() == 'supplier.driver.with_trashed')
                    <a href="{{ route('supplier.driver') }}" class="btn btn-success">Показать активных</a>

                @else
                    <a href="{{ route('supplier.driver.with_trashed') }}" class="btn btn-warning">Показать архив</a>

                @endif
                <div>
                    <span class="badge bg-warning text-black">В архиве {{ $count['inactive'] }}</span>
                    <span class="badge bg-success">Активных {{ $count['active'] }}</span>
                </div>
            </div>
            <div class="col-12 col-sm-6 text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    Добавить
                </button>
            </div>

            <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="addModalLabel">Добавление</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @include('supplier.driver.add_form')
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-primary" onclick="$('.addFormDriver').submit();">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-1">
            {{ $list_arr->links('pagination::bootstrap-5') }}
        </div>

        <div class="row mt-2">
            <table class="table table-condensed table-striped rounded-3 overflow-hidden">
                <thead>
                <tr>
                    @foreach($list_row_cols as $list_row_col_name)
                        <td>{{ $row_cols[$list_row_col_name]['label'] }}</td>
                    @endforeach
{{--                    <td>Группы</td>--}}
                    <td style="width: 160px;">Действия</td>
                </tr>
                </thead>
                <tbody>
                @foreach($list_arr as $row)
                    <tr class="@if($row['deleted_at']) bg-warning @endif">
                        @foreach($list_row_cols as $list_row_col_name)
                            <td style="{{ $row_cols[$list_row_col_name]['width'] ? 'width:' . $row_cols[$list_row_col_name]['width'] . 'px' : '' }};">{{ $row[$list_row_col_name] }}</td>
                        @endforeach
{{--                        <td>@foreach($row['roles'] as $role_row) <span class="badge rounded-pill bg-info">{{ $role_row['name'] }}</span> @endforeach</td>--}}
                        <td>
                            <form action="{{ route($delete_route, $row['id']) }}" method="post" class="driver_deleteForm_{{ $row['id'] }}">
                                @csrf
                            </form>
                            <form action="{{ route($restore_route, $row['id']) }}" method="post" class="driver_restoreForm_{{ $row['id'] }}">
                                @csrf
                            </form>
                            <a title="Показать одну запиь" class="view_btn btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#viewModal" data-id="{{ $row['id'] }}"><i class="bi bi-eye"></i></a>
                            <a title="Редактировать" class="edit_btn btn btn-sm btn-secondary mx-1" data-bs-toggle="modal" data-bs-target="#editModal" data-id="{{ $row['id'] }}"><i class="bi bi-pencil"></i></a>
                            @if($row['deleted_at'])
                                <a title="Восстановить" class="btn btn-sm btn-danger ms-4" onclick="if(confirm('@php restore_confirm($row['name'], $row['id']) @endphp')){ $('.driver_restoreForm_{{ $row['id'] }}').submit(); }">
                                    <i class="bi bi-repeat"></i>
                                </a>
                            @else
                                <a title="В архив" class="btn btn-sm btn-success ms-4" onclick="if(confirm('@php delete_confirm($row['name'], $row['id']) @endphp')){ $('.driver_deleteForm_{{ $row['id'] }}').submit(); }">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="row mt-1">
            {{ $list_arr->links('pagination::bootstrap-5') }}
        </div>

    </div>
@endsection

@section('script')
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="viewModalLabel">Просмотр записи <b class="view_id"></b></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="view_loader_error"></div>
                    <div class="view_loader justify-content-center text-center m-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="view_data"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editModalLabel">Редактирование записи <b class="edit_id"></b></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="edit_loader_error"></div>
                    <div class="edit_loader justify-content-center text-center m-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <form action="{{ route($edit_route) }}" id="editModal" method="post" class="editForm">
                        @csrf
                        <input type="hidden" name="id" value="" class="edit_field_id" />
                        @foreach($edit_row_cols as $edit_row_name)
                            @if(in_array($row_cols[$edit_row_name]['type'], ['text', 'email']))
                                <div class="mb-3">
                                    <label for="editFormControlInput{{ $edit_row_name }}" class="form-label">{{ $row_cols[$edit_row_name]['label'] }}</label>
                                    <input name="{{ $edit_row_name }}" type="text" class="edit_field_{{ $edit_row_name }} form-control" id="editFormControlInput{{ $edit_row_name }}">
                                </div>
                            @elseif($row_cols[$edit_row_name]['type'] == 'textarea')
                                <div class="mb-3">
                                    <label for="editFormControlInput{{ ucfirst($edit_row_name) }}" class="form-label">{{ $row_cols[$edit_row_name]['label'] }}</label>
                                    <textarea name="{{ $edit_row_name }}" class="form-control" id="editFormControlInput{{ ucfirst($edit_row_name) }}"></textarea>
                                </div>
                            @endif
                        @endforeach
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary" onclick="$('.editForm').submit();">Сохранить</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('.view_btn').on('click', function(){
            let id = $(this).data('id');
            $('.view_id').text(id);
            let ff = $('#viewModal');
            $(ff).find('.view_loader').show();
            $(ff).find('.view_data').text('');
            $(ff).find('.view_data').hide();
            $.ajax({
                url: '{{ route($view_route) }}/' + id,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            }).done(function(data) {
                let ff = $('#viewModal');
                $(ff).find('.view_data').html('<table class="table table-bordered">' +
                    '@foreach($view_row_cols as $view_row_col_name) <tr><td style="width: 280px">{{ $row_cols[$view_row_col_name]['label'] }}</td><td>' + data.{{ $view_row_col_name }} + '</td></tr> @endforeach' +
                    '</table>');
                $(ff).find('.view_loader').hide();
                $(ff).find('.view_data').show();
            }).fail(function(){
                $(ff).find('.view_loader').hide();
                $(ff).find('.view_loader_error').html('<b class="badge text-danger">Ошибка при при обработке запроса</b>');
            });
        });
        $('.edit_btn').on('click', function(){
            let ff = $('#editModal');
            $(ff).find('form').hide();
            $(ff).find('.edit_loader').show();
            $(ff).find('.edit_loader_error').text('');
            let id = $(this).data('id');
            $('.edit_id').text(id);
            $.ajax({
                url: '{{ route($view_route) }}/' + id,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            }).done(function(data) {
                let ff = $('#editModal');
                $(ff).find('.edit_field_id').val(data.id);
                @foreach($edit_row_cols as $edit_row_col_name)
                @if(in_array($row_cols[$edit_row_col_name]['type'], ['text', 'email']))
                $(ff).find('.edit_field_{{ $edit_row_col_name }}').val(data.{{ $edit_row_col_name }});
                @elseif($row_cols[$edit_row_col_name]['type'] == 'textarea')
                $(ff).find('.edit_field_{{ $edit_row_col_name }}').text(data.{{ $edit_row_col_name }});
                @endif
                @endforeach
                $(ff).find('.edit_loader').hide();
                $(ff).find('form').show();
            }).fail(function(){
                $(ff).find('.edit_loader').hide();
                $(ff).find('.edit_loader_error').html('<b class="badge text-danger">Ошибка при при обработке запроса</b>');
            });
        });
    </script>
@endsection