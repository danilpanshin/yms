@extends('layout.app')

@section('title', 'Пользователи')

@section('content')
    @php
        $add_route = 'admin.user.add_post';
        $edit_route = 'admin.user.edit_post';
        $delete_route = 'admin.user.delete_post';
        $view_route = 'admin.user.one';
        function delete_confirm($name, $id): void { echo "Вы действительно хотите удалть пользователя {$name} под номером {$id}"; }
        $list_arr = $users ?? ($list_arr ?? []);
        $row_cols = [
            'id' => ['type' => 'text', 'width' => 60, 'label' => 'ID'],
            'name' => ['type' => 'text', 'width' => 120, 'label' => 'Имя'],
            'email' => ['type' => 'email', 'width' => 400, 'label' => 'Email'],
            'password' => ['type' => 'text', 'width' => 0, 'label' => 'Пароль'],
        ];
        $add_row_cols = ['name', 'email', 'password'];
        $list_row_cols = ['id', 'name', 'email'];
        $edit_row_cols = ['name', 'email'];
        $view_row_cols = ['id', 'name', 'email'];
    @endphp
    <div class="admin_users_section">
        <div class="row justify-content-end">
            <div class="col-1 text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    Добавить
                </button>
            </div>

            <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="addModalLabel">Добавление</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route($add_route) }}" method="post" class="addForm">
                                @csrf
                                @foreach($add_row_cols as $row_cols_name)
                                    @if(in_array($row_cols[$row_cols_name]['type'], ['text', 'email']))
                                        <div class="mb-3">
                                            <label for="addFormControlInput{{ ucfirst($row_cols_name) }}" class="form-label">{{ $row_cols[$row_cols_name]['label'] }}</label>
                                            <input name="{{ $row_cols_name }}" type="{{ $row_cols[$row_cols_name]['type'] }}" class="form-control" id="addFormControlInput{{ ucfirst($row_cols_name) }}">
                                        </div>
                                    @elseif($row_cols[$row_cols_name]['type'] == 'textarea')
                                        <div class="mb-3">
                                            <label for="addFormControlInput{{ ucfirst($row_cols_name) }}" class="form-label">{{ $row_cols[$row_cols_name]['label'] }}</label>
                                            <textarea name="{{ $row_cols_name }}" class="form-control" id="addFormControlInput{{ ucfirst($row_cols_name) }}"></textarea>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="mb-3">
                                    <label for="addFormControlTextarea1" class="form-label">Группы</label>
                                    @foreach($roles as $row)
                                        <div><input class="form-check-input" type="checkbox" name="role[]" value="{{ $row['id'] }}" /> {{ $row['name'] }}</div>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-primary" onclick="$('.addForm').submit();">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <table class="table table-condensed table-striped-columns rounded-3 overflow-hidden">
                <thead>
                <tr>
                    @foreach($list_row_cols as $list_row_col_name)
                        <td>{{ $row_cols[$list_row_col_name]['label'] }}</td>
                    @endforeach
                    <td>Группы</td>
                    <td>Действия</td>
                </tr>
                </thead>
                <tbody>
                @foreach($list_arr as $row)
                    <tr>
                        @foreach($list_row_cols as $list_row_col_name)
                            <td style="width: {{ $row_cols[$list_row_col_name]['width'] }}px;">{{ $row[$list_row_col_name] }}</td>
                        @endforeach
                        <td>@foreach($row['roles'] as $role_row) <span class="badge rounded-pill bg-info">{{ $role_row['name'] }}</span> @endforeach</td>
                        <td style="width: 160px;">
                            <form action="{{ route($delete_route, $row['id']) }}" method="post" class="deleteForm">
                                @csrf
                            </form>
                            <a class="view_btn btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#viewModal" data-id="{{ $row['id'] }}"><i class="bi bi-eye"></i></a>
                            <a class="edit_btn btn btn-sm btn-outline-secondary mx-1" data-bs-toggle="modal" data-bs-target="#editModal" data-id="{{ $row['id'] }}"><i class="bi bi-pencil"></i></a>
                            <a class="btn btn-sm btn-outline-danger ms-4" onclick="if(confirm('@php delete_confirm($row['name'], $row['id']) @endphp')){ $('.deleteForm').submit(); }">
                                <i class="bi bi-trash3"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
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
        <div class="modal-dialog modal-dialog-centered">
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
                    '@foreach($view_row_cols as $view_row_col_name) <tr><td style="width: 180px">id</td><td>' + data.{{ $view_row_col_name }} + '</td></tr> @endforeach' +
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