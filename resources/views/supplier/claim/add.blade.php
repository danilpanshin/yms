@extends('layout.app')

@section('title', 'Создание заявки');

@section('content')
    <link rel="stylesheet" href="/assets/css/select2.css">
    <div class="supplier_claim_add_section">
        <div class="row mb-3">
            <div class="col-12">
                <form action="{{ route('supplier.claim.add') }}" method="POST" id="supplier_claim_add_form row">
                    @csrf
                    <div class="row">
                        <div class="mb-2 col-12">
                            <label for="claimAddFormControlSelectSupplier" class="form-label">Поставщик</label>
                            @if(Auth::user()->can_set_claim_supplier())
                                <select class="form-select" id="claimAddFormControlSelectSupplier">

                                </select>
                            @else
                                <h4>{{ Auth::user()->name }}</h4>
                                <input type="hidden" name="supplier_id" value="{{ Auth::user()->id }}" />
                            @endif
                        </div>

                        <div class="mb-2 col-12">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <label for="claimAddFormControlInputDriver" class="form-label">
                                        <i class="bi bi-asterisk text-danger fs-8"></i>
                                        Водитель
                                    </label>
                                </div>
                                <div class="col-12 col-sm-6 text-end">
                                    <button class="btn btn-warning bi bi-trash" onclick="$('#claimAddFormControlInputDriver').val(null).trigger('change');" type="button"></button>
                                    <button class="btn btn-primary bi bi-plus" data-bs-toggle="modal" data-bs-target="#addModalDriver" type="button"></button>
                                    <a class="btn btn-secondary bi bi-view-list" href="{{ route('supplier.driver') }}" target="_blank"></a>
                                </div>
                            </div>

                            <select class="form-control basicAutoSelect" name="driver_id" id="claimAddFormControlInputDriver"
                                    data-url="{{ route('supplier.driver.ac') }}" autocomplete="off" required></select>
                        </div>

                        <div class="mb-2 col-12">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <label for="claimAddFormControlInputExpeditor" class="form-label">Экспедитор</label>
                                </div>
                                <div class="col-12 col-sm-6 text-end">
                                    <button class="btn btn-warning bi bi-trash" onclick="$('#claimAddFormControlInputExpeditor').val(null).trigger('change');" type="button"></button>
                                    <button class="btn btn-primary bi bi-plus" data-bs-toggle="modal" data-bs-target="#addModalExpeditor" type="button"></button>
                                    <a class="btn btn-secondary bi bi-view-list" href="{{ route('supplier.expeditor') }}" target="_blank"></a>
                                </div>
                            </div>

                            <select class="form-control basicAutoSelect" name="expeditor_id" id="claimAddFormControlInputExpeditor"
                                    data-url="{{ route('supplier.expeditor.ac') }}" autocomplete="off"></select>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <label for="claimAddFormControlSelectCarNumber" class="form-label">
                                <i class="bi bi-asterisk text-danger fs-8"></i>
                                Номер ТС
                            </label>
                            <input type="text" name="car_number" class="form-control" id="claimAddFormControlSelectCarNumber" required/>
                        </div>

{{--                        <div class="mb-2 col-12 col-sm-6">--}}
{{--                            <label for="claimAddFormControlSelectSupplyType" class="form-label">Тип поставки</label>--}}
{{--                            <select name="supply_type" class="form-select" id="claimAddFormControlSelectSupplyType">--}}
{{--                                <option value="1" selected>Региональная</option>--}}
{{--                                @if(Auth::user()->can_choose_external_supply_type())<option value="2" >Импортная</option>@endif--}}
{{--                            </select>--}}
{{--                        </div>--}}

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

    {{--                    <hr>--}}
    {{--                    <div class="mb-3 hidden">--}}
    {{--                        <label for="claimAddFormControlSelectPlaceCount" class="form-label">Кол-во мест</label>--}}
    {{--                        <input type="text" name="place_count" class="form-control" id="claimAddFormControlSelectPlaceCount"/>--}}
    {{--                    </div>--}}

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
                                <button type="submit" class="btn btn-primary" onclick="$('#supplier_claim_add_form').submit();">Забронировать</button>
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
                            @include('supplier.driver.add_form', $data=['lllAjaxFormSubmitName' => 'lllAjaxFormSubmitClaimAddDriverAddModal'])
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
                            @include('supplier.expeditor.add_form', $data=['lllAjaxFormSubmitName' => 'lllAjaxFormSubmitClaimAddExpeditorAddModal'])
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
                    url: '{{ route('supplier.claim.slots') }}',
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

            $('#supplier_claim_add_form').on('submit', function(e){
                e.preventDefault();
                const form = $(this)
                $.ajax({
                    url: '{{ route('supplier.claim.add') }}',
                    method: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        console.log(response)
                    },
                    error: function(xhr) {
                        let responce = xhr.responseJSON
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
                    url: '{{ route('supplier.driver.ac') }}',
                    dataType: 'json'
                    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                },
                theme: "bootstrap-5",
                placeholder: ''
            });
            $('#claimAddFormControlInputExpeditor').select2({
                ajax: {
                    url: '{{ route('supplier.expeditor.ac') }}',
                    dataType: 'json'
                    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                },
                theme: "bootstrap-5",
                placeholder: ''
            });
        });
    </script>
@endsection