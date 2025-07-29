@extends('layout.app')

@section('title', 'Личный кабинет');

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Отправка приглашений поставщикам</h4>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form id="inviteForm" method="POST" action="{{ route('manager.send_invite') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="supplierSelect" class="form-label fw-bold">Выберите поставщика:</label>
                                <select class="form-select" id="supplierSelect" name="supplier_id" style="width: 100%">
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">
                                            {{ $supplier->name }} ({{ $supplier->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-envelope-check me-2"></i> Отправить приглашение
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link href="{{ asset('/assets/css/select2.css') }}" rel="stylesheet">
    <style>
        .select2-container .select2-selection--single {
            height: 46px;
            padding: 10px 16px;
            font-size: 18px;
            line-height: 1.5;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px;
        }
    </style>
@endsection

@section('script')
    <script src="{{ asset('/assets/js/select2.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#supplierSelect').select2({
                placeholder: "Начните вводить имя или email",
                allowClear: true,
                minimumInputLength: 2,
                language: {
                    noResults: function() {
                        return "Поставщики не найдены";
                    }
                }
            });
        });
    </script>

@endsection