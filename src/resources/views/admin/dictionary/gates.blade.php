@extends('layout.app')

@section('title', 'Ворота')

@section('content')
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
                        <form action="{{ route('admin.dictionary.gate.add_post') }}" method="post" class="addForm">
                            @csrf
                            <div class="mb-3">
                                <label for="addFormControlInput1" class="form-label">Склад</label>
                                <input name="wh_number" type="text" class="form-control" id="addFormControlInput1">
                            </div>
                            <div class="mb-3">
                                <label for="addFormControlInput1" class="form-label">Ворота</label>
                                <input name="number" type="text" class="form-control" id="addFormControlInput1">
                            </div>
                            <div class="mb-3">
                                <label for="addFormControlInput2" class="form-label">Наименование</label>
                                <input name="name" type="text" class="form-control" id="addFormControlInput2">
                            </div>
                            <div class="mb-3">
                                <label for="addFormControlTextarea1" class="form-label">Описание</label>
                                <textarea name="comment" class="form-control" id="addFormControlTextarea1" rows="3"></textarea>
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
                    <td>id</td>
                    <td>Склад</td>
                    <td>Ворота</td>
                    <td>Наименование</td>
                    <td>Описание</td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
                @foreach($gates as $row)
                    <tr>
                        <td style="width: 60px;">{{ $row['id'] }}</td>
                        <td style="width: 120px;">{{ $row['wh_number'] }}</td>
                        <td style="width: 120px;">{{ $row['number'] }}</td>

                        <td style="width: 400px;">{{ $row['name'] }}</td>
                        <td>{{ $row['comment'] }}</td>
                        <td style="width: 160px;">
                            <form action="{{ route('admin.dictionary.gate.delete_post', $row['id']) }}" method="post" class="deleteForm">
                                @csrf
                            </form>
                            <a class="view_btn btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#viewModal" data-id="{{ $row['id'] }}"><i class="bi bi-eye"></i></a>
                            <a class="edit_btn btn btn-sm btn-outline-secondary mx-1" data-bs-toggle="modal" data-bs-target="#editModal" data-id="{{ $row['id'] }}"><i class="bi bi-pencil"></i></a>
                            <a class="btn btn-sm btn-outline-danger ms-4" onclick="if(confirm('Вы действительно хотите удалть ворота {{ $row['name'] }} под номером {{ $row['number'] }}')){ $('.deleteForm').submit(); }">
                                <i class="bi bi-trash3"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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
                    <form action="{{ route('admin.dictionary.gate.edit_post') }}" id="editModal" method="post" class="editForm">
                        @csrf
                        <input type="hidden" name="id" value="" class="edit_field_id" />
                        <div class="mb-3">
                            <label for="addFormControlInput" class="form-label">Склад</label>
                            <input name="wh_number" type="text" class="edit_field_wh_number form-control" id="addFormControlInput">
                        </div>
                        <div class="mb-3">
                            <label for="addFormControlInput1" class="form-label">Ворота</label>
                            <input name="number" type="text" class="edit_field_number form-control" id="addFormControlInput1">
                        </div>
                        <div class="mb-3">
                            <label for="addFormControlInput2" class="form-label">Наименование</label>
                            <input name="name" type="text" class="edit_field_name form-control" id="addFormControlInput2">
                        </div>
                        <div class="mb-3">
                            <label for="addFormControlTextarea1" class="form-label">Описание</label>
                            <textarea name="comment" class="edit_field_comment form-control" id="addFormControlTextarea1" rows="3"></textarea>
                        </div>
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
                url: '{{ route('admin.dictionary.gate.one') }}/' + id,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            }).done(function(data) {
                let ff = $('#viewModal');
                $(ff).find('.view_data').html('<table class="table table-bordered">' +
                    '<tr><td style="width: 180px">id</td><td>' + data.id + '</td></tr>' +
                    '<tr><td>Склад</td><td>' + data.wh_number + '</td></tr>' +
                    '<tr><td>Ворота</td><td>' + data.number + '</td></tr>' +
                    '<tr><td>Наименование</td><td>' + data.name + '</td></tr>' +
                    '<tr><td>Описание</td><td>' + data.comment + '</td></tr>' +
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
                url: '{{ route('admin.dictionary.gate.one') }}/' + id,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            }).done(function(data) {
                let ff = $('#editModal');
                $(ff).find('.edit_field_id').val(data.id);
                $(ff).find('.edit_field_wh_number').val(data.wh_number);
                $(ff).find('.edit_field_number').val(data.number);
                $(ff).find('.edit_field_name').val(data.name);
                $(ff).find('.edit_field_comment').text(data.comment);
                $(ff).find('.edit_loader').hide();
                $(ff).find('form').show();
            }).fail(function(){
                $(ff).find('.edit_loader').hide();
                $(ff).find('.edit_loader_error').html('<b class="badge text-danger">Ошибка при при обработке запроса</b>');
            });
        });
    </script>
@endsection