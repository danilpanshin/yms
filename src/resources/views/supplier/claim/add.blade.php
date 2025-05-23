@extends('layout.app')

@section('title', 'Создание заявки');

@section('content')
    <link rel="stylesheet" href="/assets/css/select2.css">
    <div class="supplier_claim_add_section">
        <div class="row mb-3">
            <div class="col-12">
                <form action="#" method="POST" id="supplier_claim_add_form row">
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
                                    <label for="claimAddFormControlInputDriver" class="form-label">Водитель</label>
                                </div>
                                <div class="col-12 col-sm-6 text-end">
                                    <button class="btn btn-warning bi bi-trash" onclick="$('#claimAddFormControlInputDriver').val(null).trigger('change');" type="button"></button>
                                    <button class="btn btn-primary bi bi-plus" data-bs-toggle="modal" data-bs-target="#addModalDriver" type="button"></button>
                                    <a class="btn btn-secondary bi bi-view-list" href="{{ route('supplier.driver') }}" target="_blank"></a>
                                </div>
                            </div>

                            <select class="form-control basicAutoSelect" name="simple_select" id="claimAddFormControlInputDriver"
                                    data-url="{{ route('supplier.driver.ac') }}" autocomplete="off"></select>
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

                            <select class="form-control basicAutoSelect" name="simple_select" id="claimAddFormControlInputExpeditor"
                                    data-url="{{ route('supplier.expeditor.ac') }}" autocomplete="off"></select>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <label for="claimAddFormControlSelectCarNumber" class="form-label">Номер ТС</label>
                            <input type="text" name="car_number" class="form-control" id="claimAddFormControlSelectCarNumber"/>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <label for="claimAddFormControlSelectSupplyType" class="form-label">Тип поставки</label>
                            <select name="supply_type" class="form-select" id="claimAddFormControlSelectSupplyType">
                                <option value="1" selected>Региональная</option>
                                @if(Auth::user()->can_choose_external_supply_type())<option value="2" >Импортная</option>@endif
                            </select>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <label for="claimAddFormControlSelectCarType" class="form-label">Тип ТС</label>
                            <select name="car_type" class="form-select" id="claimAddFormControlSelectCarType">
                                <option value="1" selected>Фура</option>
                            </select>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <label for="claimAddFormControlSelectGBort" class="form-label">Гидроборт</label>
                            <select class="form-select" id="claimAddFormControlSelectGBort">
                                <option value="0" selected>Нет</option>
                                <option value="1">Да</option>
                            </select>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <label for="claimAddFormControlSelectPalletCount" class="form-label">Кол-во паллет</label>
                            <input type="number" name="pallet_count" class="form-control" id="claimAddFormControlSelectPalletCount" required min="1" />
                        </div>

    {{--                    <hr>--}}
    {{--                    <div class="mb-3 hidden">--}}
    {{--                        <label for="claimAddFormControlSelectPlaceCount" class="form-label">Кол-во мест</label>--}}
    {{--                        <input type="text" name="place_count" class="form-control" id="claimAddFormControlSelectPlaceCount"/>--}}
    {{--                    </div>--}}

                        <div class="mb-2 col-12 col-sm-6">
                            <label for="claimAddFormControlWeight" class="form-label">Масса кг</label>
                            <input type="number" name="weight" class="form-control" id="claimAddFormControlWeight"/>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <label for="claimAddFormControlApprovalType" class="form-label">Тип приемки</label>
                            <select class="form-select" id="claimAddFormControlApprovalType">
                                <option value="1" selected>По грузоместам</option>
                                <option value="2">Потоварно</option>
                            </select>
                        </div>

                        <div class="mb-2 col-12 col-sm-6">
                            <div class="row">
                                <div class="col-6">
                                    <label for="claimAddFormControlDate" class="form-label">Дата</label>
                                    <input type="date" name="date" class="form-control" id="claimAddFormControlDate" required
{{--                                           min="{{ now()->format('Y-m-d') }}"/>--}}/>
                                </div>
                                <div class="col-6">
                                    <label for="claimAddFormControlDateTime" class="form-label">Время</label>
                                    <input type="text" name="time" class="form-control" id="claimAddFormControlDateTime"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="form-group">
                <label for="start_time">Доступное время</label>
                <select name="start_time" id="start_time" class="form-control" required>
                    <option value="">Сначала выберите дату и количество паллет</option>
                </select>
            </div>

            <div class="badge bg-warning mt-4 fs-4">Функция создания временно не доступна. Но выбор тайм слотов работает</div>

{{--            <button type="submit" class="btn btn-primary">Забронировать</button>--}}

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
            $('#claimAddFormControlDate').on('change', function(){
                console.log('claimAddFormControlDate change');
                updateTimeSlots();
            });

            $('#claimAddFormControlSelectPalletCount').on('change', function(){
                console.log('claimAddFormControlSelectPalletCount change');
                updateTimeSlots();
            });


            const $bookingDate = $('#claimAddFormControlDate');
            const $palletsCount = $('#claimAddFormControlSelectPalletCount');
            const $gbort = $('#claimAddFormControlSelectGBort');
            const $timeSlot = $('#start_time');

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
                        if (response.length > 0) {
                            let options = '<option value="">Выберите время</option>';

                            response.forEach(function(slot) {
                                options += `<option value="${slot.value}" data-gate-id="${slot.gate_id}">${slot.text}</option>`;
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

            // Обработчики изменений полей
            $bookingDate.on('change', findAvailableSlots);
            $palletsCount.on('change', findAvailableSlots);
            $gbort.on('change', findAvailableSlots);



            $('.spinner-border').hide();

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