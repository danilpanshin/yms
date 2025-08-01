@extends('layout.app')

@section('title', 'Создание заявки');

@section('content')
    <link rel="stylesheet" href="/assets/css/select2.css">
    <div class="supplier_claim_add_section">
        <div class="row mb-3">
            <div class="col-12">
                <div class="errors"></div>
                <form action="{{ route('stock_admin.claim.add') }}" method="POST" id="stock_admin_claim_add_form">
                    @csrf
                    <div class="row">
                        <div class="mb-2 col-12">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <label for="claimAddFormControlInputSupplier" class="form-label">
                                        <i class="bi bi-asterisk text-danger fs-8"></i>
                                        Поставщик
                                    </label>
                                </div>
                                <div class="col-12 col-sm-6 text-end">
                                    <button class="btn btn-warning bi bi-trash" onclick="$('#claimAddFormControlInputSupplier').val(null).trigger('change');" type="button"></button>
                                    <button class="btn btn-light bi bi-plus disabled" data-bs-toggle="modal" data-bs-target="#addModalSupplier" type="button"></button>
                                    <a class="btn btn-secondary bi bi-view-list" href="{{ route('stock_admin.supplier') }}" target="_blank"></a>
                                </div>
                            </div>
{{--                            {{ route('stock_admin.supplier.ac') }}--}}
                            <select class="form-control basicAutoSelect" name="supplier_id" id="claimAddFormControlInputSupplier"
                                    data-url="" autocomplete="off" onselect="$('#claimAddFormControlInputDriver').removeAttr('disabled');"></select>
                        </div>

                        <div class="mb-2 col-12">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <label for="claimAddFormControlInputDriver" class="form-label">
                                        Водитель
                                    </label>
                                </div>
                                <div class="col-12 col-sm-6 text-end">
                                    <button class="btn btn-warning bi bi-trash" onclick="$('#claimAddFormControlInputDriver').val(null).trigger('change');" type="button"></button>
                                    <button class="btn btn-primary bi bi-plus" onclick="$('.lllAjaxFormSubmitClaimAddDriverAddModal_SID').remove(); $('.lllAjaxFormSubmitClaimAddDriverAddModal').prepend('<div class=\'lllAjaxFormSubmitClaimAddDriverAddModal_SID\'><input  type=\'hidden\' value=\'' + $('#claimAddFormControlInputSupplier').select2('data')[0].id + '\' name=\'sid\'/>Поставщик ' + $('#claimAddFormControlInputSupplier').select2('data')[0].text + '</div>')" data-bs-toggle="modal" data-bs-target="#addModalDriver" type="button"></button>
                                    <a class="btn btn-secondary bi bi-view-list" href="{{ route('stock_admin.driver') }}" target="_blank"></a>
                                </div>
                            </div>

                            <select disabled class="form-control basicAutoSelect" name="driver_id" id="claimAddFormControlInputDriver"
                                    data-url="{{ route('stock_admin.driver.ac') }}" autocomplete="off"></select>
                        </div>

                        <div class="mb-2 col-12">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <label for="claimAddFormControlInputExpeditor" class="form-label">Экспедитор</label>
                                </div>
                                <div class="col-12 col-sm-6 text-end">
                                    <button class="btn btn-warning bi bi-trash" onclick="$('#claimAddFormControlInputExpeditor').val(null).trigger('change');" type="button"></button>
                                    <button class="btn btn-primary bi bi-plus" onclick="$('.lllAjaxFormSubmitClaimAddExpeditorAddModal_SID').remove(); $('.lllAjaxFormSubmitClaimAddExpeditorAddModal').prepend('<div class=\'lllAjaxFormSubmitClaimAddExpeditorAddModal_SID\'><input  type=\'hidden\' value=\'' + $('#claimAddFormControlInputSupplier').select2('data')[0].id + '\' name=\'sid\'/>Поставщик ' + $('#claimAddFormControlInputSupplier').select2('data')[0].text + '</div>')" data-bs-toggle="modal" data-bs-target="#addModalExpeditor" type="button"></button>
                                    <a class="btn btn-secondary bi bi-view-list" href="{{ route('stock_admin.expeditor') }}" target="_blank"></a>
                                </div>
                            </div>

                            <select disabled class="form-control basicAutoSelect" name="expeditor_id" id="claimAddFormControlInputExpeditor"
                                    data-url="{{ route('stock_admin.expeditor.ac') }}" autocomplete="off"></select>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <label for="claimAddFormControlSelectCarNumber" class="form-label">
                                <i class="bi bi-asterisk text-danger fs-8"></i>
                                Номер ТС
                            </label>
                            <input type="text" name="car_number" class="form-control" id="claimAddFormControlSelectCarNumber" required/>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <label for="claimAddFormControlSelectSupplyType" class="form-label">Тип поставки</label>
                            <select name="supply_type" class="form-select" id="claimAddFormControlSelectSupplyType">
                                <option value="1" selected>Региональная</option>
                                @if(Auth::user()->can_choose_external_supply_type())<option value="2">Импортная</option>@endif
                            </select>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <label for="claimAddFormControlSelectCarType" class="form-label">
                                <i class="bi bi-asterisk text-danger fs-8"></i>
                                Тип ТС
                            </label>
                            <select name="car_type_id" class="form-select" id="claimAddFormControlSelectCarType" required>
                                <option value="1" selected>Фура</option>
                            </select>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <div class="place_count_block" style="display: none;">
                                <label for="claimAddFormControlSelectPlaceCount" class="form-label">Кол-во мест</label>
                                <input type="text" name="place_count" class="form-control" id="claimAddFormControlSelectPlaceCount"/>
                            </div>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <label for="claimAddFormControlSelectGBort" class="form-label">
                                <i class="bi bi-asterisk text-danger fs-8"></i>
                                Гидроборт
                            </label>
                            <select name="gbort" class="form-select" id="claimAddFormControlSelectGBort" required>
                                <option value="0" selected>Нет</option>
                                <option value="1">Да</option>
                            </select>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <label for="claimAddFormControlSelectPalletCount" class="form-label">
                                <i class="bi bi-asterisk text-danger fs-8"></i>
                                Кол-во паллет
                            </label>
                            <input type="number" name="pallets_count" class="form-control required" id="claimAddFormControlSelectPalletCount" required min="1" />
                        </div>



                        <div class="mb-2 col-12 col-sm-6">
                            <label for="claimAddFormControlWeight" class="form-label">
                                <i class="bi bi-asterisk text-danger fs-8"></i>
                                Масса кг
                            </label>
                            <input type="number" name="weight" class="form-control" id="claimAddFormControlWeight" required/>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <label for="claimAddFormControlApprovalType" class="form-label">
                                <i class="bi bi-asterisk text-danger fs-8"></i>
                                Тип приемки
                            </label>
                            <select class="form-select" id="claimAddFormControlApprovalType" required>
                                <option value="1" selected>По грузоместам</option>
                                <option value="2">Потоварно</option>
                            </select>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <label for="claimAddFormControlDate" class="form-label">
                                <i class="bi bi-asterisk text-danger fs-8"></i>
                                Дата
                            </label>
                            <input type="date" name="booking_date" class="form-control" id="claimAddFormControlDate" required
                                   min="{{ now()->format('Y-m-d') }}"/>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <label for="start_time" class="form-label">
                                <i class="bi bi-asterisk text-danger fs-8"></i>
                                Доступное время
                            </label>
                            <select name="start_time" class="form-select" id="start_time" required>
                                <option value="">Сначала выберите дату и количество паллет</option>
                            </select>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12 col-md-4"></div>
                            <div class="col-12 col-md-4 text-center">
                                <button type="submit" class="btn btn-primary">Забронировать</button>
                            </div>
                            <div class="col-12 col-md-4"></div>
                        </div>



                    </div>
                </form>
            </div>




            <div class="modal fade" id="addModalDriver" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="addModalLabel">Добавление</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="errors"></div>
                            <div class="spinner-border invisible" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            @include('stock_admin.driver.add_form', $data=['lllAjaxFormSubmitName' => 'lllAjaxFormSubmitClaimAddDriverAddModal'])
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-primary" onclick="$('.lllAjaxFormSubmitClaimAddDriverAddModal.addFormDriver').submit();">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="addModalExpeditor" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="addModalLabel">Добавление</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="errors"></div>
                            <div class="justify-content-center text-center">
                                <div class="spinner-border hide" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            @include('stock_admin.expeditor.add_form', $data=['lllAjaxFormSubmitName' => 'lllAjaxFormSubmitClaimAddExpeditorAddModal'])
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-primary" onclick="$('.lllAjaxFormSubmitClaimAddExpeditorAddModal.addFormExpeditor').submit();">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="/assets/js/select2.js"></script>
    <script>
        $(document).ready(function(){



            const $bookingDate = $('#claimAddFormControlDate');
            const $palletsCount = $('#claimAddFormControlSelectPalletCount');
            const $gbort = $('#claimAddFormControlSelectGBort');
            const $timeSlot = $('#start_time');

            $bookingDate.on('change', function(){
                console.log('claimAddFormControlDate change');
                findAvailableSlots();
            });

            $('#claimAddFormControlSelectSupplyType').on('change', function(){
                console.log('claimAddFormControlSelectSupplyType change');
                if($(this).find(":selected").val() === '2'){
                    $('.place_count_block').show();
                } else {
                    $('.place_count_block').hide();
                }
            });

            $palletsCount.on('change', function(){
                console.log('claimAddFormControlSelectPalletCount change');
                findAvailableSlots();
            });

            $gbort.on('change', function(){
                console.log('gbort change');
                findAvailableSlots();
            });

            // Функция для поиска доступных слотов
            function findAvailableSlots() {
                const date = $bookingDate.val();
                const palletsCount = $palletsCount.val();
                const gbort = $gbort.find(":selected").val();

                if (!date || !palletsCount) {
                    $timeSlot.prop('disabled', true).html('<option value="">Сначала укажите дату и количество паллет</option>');
                    return;
                }

                $timeSlot.prop('disabled', true).html('<option value="">Загрузка доступных слотов...</option>');

                $.ajax({
                    url: '{{ route('stock_admin.claim.slots') }}',
                    method: 'GET',
                    data: {
                        date: date,
                        pallets_count: palletsCount,
                        gbort: gbort
                    },
                    success: function(response) {
                        if (typeof response.data !== "undefined" && response.data.length > 0) {
                            let options = '<option value="">Выберите время</option>';

                            response.data.forEach(function(slot) {
                                options += `<option value="${slot.start}">${slot.start}-${slot.end} (${response.hours}ч)</option>`;
                            });

                            $timeSlot.html(options).prop('disabled', false);
                        } else {
                            $timeSlot.html('<option value="">Нет доступных слотов на выбранную дату</option>');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Ошибка при загрузке слотов';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        $timeSlot.html(`<option value="">${errorMsg}</option>`);
                    },
                    complete: function() {

                    }
                });
            }

            $('.spinner-border').hide();

            $('#stock_admin_claim_add_form').on('submit', function(e){
                console.log(1);
                e.preventDefault();
                const form = $(this)
                $.ajax({
                    url: '{{ route('stock_admin.claim.add') }}',
                    method: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        document.location.href = '{{ route('stock_admin.claim') }}';
                    },
                    error: function(xhr) {
                        let responce = xhr.responseJSON
                        $('.errors').html('<div class="alert alert-warning">'+responce.message+'</div>');
                        console.log(responce)
                    },
                    complete: function() {

                    }
                });
            });

            $('.lllAjaxFormSubmitClaimAddDriverAddModal').on('submit', function(e){
                const form = $(this)
                sendAjaxForm(e, form, function(res){
                    console.log('callback lllAjaxFormSubmitClaimAddDriverAddModal');
                    if(res) {
                        $(form).trigger('reset');
                        $(form).closest('.modal').find('.btn-close').click();
                        $('#claimAddFormControlInputDriver').select2('search', '' + Math.random());
                    }
                });
            });

            $('.lllAjaxFormSubmitClaimAddExpeditorAddModal').on('submit', function(e){
                const form = $(this)
                sendAjaxForm(e, form, function(res){
                    console.log('callback lllAjaxFormSubmitClaimAddExpeditorAddModal');
                    if(res) {
                        $(form).trigger('reset');
                        $(form).closest('.modal').find('.btn-close').click();
                    }
                });
            });

            $('#claimAddFormControlInputDriver').select2({
                ajax: {
                    url: '{{ route('stock_admin.driver.ac') }}',
                    dataType: 'json',
                    data: function (params) {
                        let query = {
                            term: params.term,
                            sid: $('#claimAddFormControlInputSupplier').select2('data')[0].id
                        }

                        // Query parameters will be ?search=[term]&type=public
                        return query;
                    }
                    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                },
                theme: "bootstrap-5",
                placeholder: ''
            });

            $('#claimAddFormControlInputSupplier').select2({
                ajax: {
                    url: '{{ route('stock_admin.supplier.ac') }}',
                    dataType: 'json'
                    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                },
                theme: "bootstrap-5",
                placeholder: ''
            }).on('change', function (e) {
                let val = $('#claimAddFormControlInputSupplier').select2('data')[0].id;
                if(parseInt(val) > 0) {
                    console.log(val);
                    $('#claimAddFormControlInputDriver').data('sid', val);
                    $('#claimAddFormControlInputDriver').prop('disabled', false);
                    $('#claimAddFormControlInputExpeditor').data('sid', val);
                    $('#claimAddFormControlInputExpeditor').prop('disabled', false);
                } else {
                    $('#claimAddFormControlInputDriver').prop('disabled', true);
                    $('#claimAddFormControlInputExpeditor').prop('disabled', true);
                }
            });

            $('#claimAddFormControlInputExpeditor').select2({
                ajax: {
                    url: '{{ route('stock_admin.expeditor.ac') }}',
                    dataType: 'json',
                    data: function (params) {
                        let query = {
                            term: params.term,
                            sid: $('#claimAddFormControlInputSupplier').select2('data')[0].id
                        }

                        // Query parameters will be ?search=[term]&type=public
                        return query;
                    }
                    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                },
                theme: "bootstrap-5",
                placeholder: ''
            });
        });
    </script>
@endsection