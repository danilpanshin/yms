@extends('layout.app')

@section('title', 'Создание поставщика');

@section('content')
    <link rel="stylesheet" href="/assets/css/select2.css">
    <div class="supplier_claim_add_section">
        <div class="row">
            <div class="col-12">
                <select type="text" id="claimAddFormControlInputRsSupplier"></select>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="/assets/js/select2.js"></script>
    <script>
        $(document).ready(function(){
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

            $('#claimAddFormControlInputRsSupplier').select2({
                ajax: {
                    url: '{{ route('stock_admin.rs_supplier.ac') }}',
                    dataType: 'json'
                    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                },
                theme: "bootstrap-5",
                placeholder: ''
            }).on('change', function(e){
                let data = $('#claimAddFormControlInputRsSupplier').select2('data')[0].id;
                console.log(data);
            });
        });
    </script>
@endsection