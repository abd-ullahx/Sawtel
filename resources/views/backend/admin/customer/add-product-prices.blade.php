@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-12">
            <form method="post" class="validate" autocomplete="off"
                  action="{{ route('client.store-product-prices', $client->id) }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span class="panel-title">{{ _lang('Add prices') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-9 col-md-6 col-sm-12 mt-2">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Country') }}</label>
                                    <select class="form-control auto-select select2"
                                            data-selected="{{ old('country') }}"
                                            name="product_id" required>
                                        <option value="">{{ _lang('Select Product') }}</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-12 mt-2">
                                <div class="form-group">
                                    <label class="control-label text-white">{{ _lang('submit') }}</label>
                                    <button type="submit" class="btn d-block btn-primary w-100">
                                        {{ _lang('Add product') }}
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($client->customerProductPrices->count())
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span class="panel-title">{{ _lang('Prices detail') }}</span>
                    </div>

                    <div class="card-body">
                        @foreach($client->customerProductPrices as $customerProductPrice)
                            <form method="post" class="validate" autocomplete="off"
                                  action="{{ route('client.store-single-product-price', [$client->id, $customerProductPrice->id]) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-1 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label">{{ _lang('Item Code') }}</label>
                                            <input type="text" class="form-control" name="name"
                                                   value="{{ $customerProductPrice->product->item_code ?? '--' }}"
                                                   disabled>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label">{{ _lang('Product Name') }}</label>
                                            <input type="text" class="form-control" name="name"
                                                   value="{{ $customerProductPrice->product->name ?? '--' }}"
                                                   disabled>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label">{{ _lang('Selling Price') }}</label>
                                            <input type="number" class="form-control" name="selling_price"
                                                   value="{{ number_format($customerProductPrice->selling_price, 2) }}"
                                                   required>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label">{{ _lang('Customer Item Code') }}</label>
                                            <input type="text" class="form-control" name="customer_item_code"
                                                   value="{{ $customerProductPrice->customer_item_code }}" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label text-white d-block">{{ _lang('Save') }}</label>
                                            <button type="submit" class="btn btn-primary">
                                                {{ _lang('Save') }}
                                            </button>
                                            <button type="button" class="btn btn-danger delete_product"
                                                    data-action="{{ route('client.delete-single-product-price', [$client->id, $customerProductPrice->id]) }}">
                                                {{ _lang('Delete') }}
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <hr>
                                    </div>

                                </div>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('js-script')
    <script>
        $(document).ready(function () {
            $('.delete_product').on('click', function () {
                let deleteUrl = $(this).data('action');

                if (confirm('Are you sure you want to delete this product?')) {
                    let form = $('<form>', {
                        'action': deleteUrl,
                        'method': 'POST'
                    });

                    let csrfToken = $('meta[name="csrf-token"]').attr('content');
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': csrfToken
                    }));

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_method',
                        'value': 'DELETE'
                    }));

                    $('body').append(form);
                    form.submit();
                }
            });
        });
    </script>
@endsection
