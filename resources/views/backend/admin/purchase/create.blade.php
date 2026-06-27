@php use App\Models\Tax; @endphp
@extends('layouts.app')

@section('content')
    <form method="post" class="validate" autocomplete="off" action="{{ route('purchases.store') }}"
          enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-xl-9 col-lg-8">
                <div class="card invoice-form">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="invoice-logo">
                                    <img src="{{ asset('public/uploads/media/' . request()->activeBusiness->logo) }}" alt="logo">
                                </div>
                            </div>

                            <div class="col-lg-4 offset-lg-4">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-lg" name="title"
                                                   value="{{ old('title', get_business_option('purchase_title', 'Purchase Order')) }}"
                                                   placeholder="{{ _lang('Purchase Order Title') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="bill_no"
                                                   value="{{ get_business_option('bill_no', old('bill_no')) }}" placeholder="{{ _lang('Bill No') }}"
                                                   required>
                                        </div>
                                    </div>



                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="po_so_number"
                                                   value="{{ old('po_so_number') }}"
                                                   placeholder="{{ _lang('PO / SO Number') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row my-4">
                            <div class="col-12">
                                <div class="divider"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group select-customer">
                                    <select class="form-control" data-selected="{{ old('vendor_id') }}" name="vendor_id"
                                            data-value="id" data-display="name"
                                            data-href="{{ route('vendors.create') }}"
                                            data-title="{{ _lang('Create New Vendor') }}" data-table="vendors"
                                            data-where="3" data-placeholder="{{ _lang('Choose Vendor') }}" required>
                                        <option value="">{{ _lang('Select Vendor') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6 offset-lg-2">
                                <div class="form-group row">
                                    <label class="col-xl-4 col-form-label">{{ _lang('Purchase Date') }}</label>
                                    <div class="col-xl-8">
                                        <input type="text" class="form-control datepicker" name="purchase_date"
                                               value="{{ old('purchase_date') }}" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-4 col-form-label">{{ _lang('Due Date') }}</label>
                                    <div class="col-xl-8">
                                        <input type="text" class="form-control datepicker" name="due_date"
                                               value="{{ old('due_date') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-group">
                                    <select class="form-control" id="products" data-type="purchase" data-value="id"
                                            data-display="name" data-placeholder="{{ _lang('Select an Item') }}"
                                            data-modal="ajax-modal"
                                            data-href="{{ route('products.create') }}?type=sell"
                                            data-title="{{ _lang('Add New Item') }}"
                                            data-table="products" data-where="7">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table" id="invoice-table">
                                        <thead>
                                        <tr>
                                            <th class="input-lg">{{ _lang('Name') }}</th>
                                            <th class="input-md">{{ _lang('Item Taxes') }}</th>
                                            <th class="input-xs text-center">{{ _lang('Quantity') }}</th>
                                            <th class="input-sm text-right">{{ _lang('Price') }}</th>
                                            <th class="input-sm text-right">{{ _lang('Amount') }}</th>
                                            <th class="text-center"><i class="fas fa-minus-circle"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(old('product_id') != null)
                                            @foreach(old('product_id') as $index => $product_id)
                                                <tr class="line-item">
                                                    <td class="input-lg">
                                                        <input type="hidden" class="product_id" name="product_id[]"
                                                               value="{{ $product_id }}">
                                                        <input type="hidden" class="product_type" name="product_type[]"
                                                               value="{{ old('product_type')[$index] }}">
                                                        <input type="text" class="form-control product_name"
                                                               name="product_name[]"
                                                               value="{{ old('product_name')[$index] }}"><br>
                                                        <textarea class="form-control description" name="description[]"
                                                                  placeholder="{{ _lang('Descriptions') }}">{{ old('description')[0] }}</textarea>
                                                    </td>
                                                    <td class="input-md">
                                                        <select name="taxes[{{ $product_id }}][]"
                                                                class="multi-selector product_taxes auto-multiple-select"
                                                                data-selected="[{{ isset(old('taxes')[$product_id]) != null ? implode(',', old('taxes')[$product_id]) : '' }}]"
                                                                data-placeholder="{{ _lang('Select Taxes') }}" multiple>
                                                            @foreach(Tax::all() as $tax)
                                                                <option value="{{ $tax->id }}"
                                                                        data-tax-rate="{{ $tax->rate }}"
                                                                        data-tax-name="{{ $tax->name }} {{ $tax->rate }} %">{{ $tax->name }} {{ $tax->rate }}
                                                                    %
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="input-xs text-center"><input type="number"
                                                                                            class="form-control quantity"
                                                                                            name="quantity[]"
                                                                                            value="{{ old('quantity')[0] }}"
                                                                                            min="1"></td>
                                                    <td class="input-sm"><input type="text"
                                                                                class="form-control text-right unit_cost"
                                                                                name="unit_cost[]"
                                                                                value="{{ old('unit_cost')[0] }}"></td>
                                                    <td class="input-sm"><input type="text"
                                                                                class="form-control text-right sub_total"
                                                                                name="sub_total[]"
                                                                                value="{{ old('sub_total')[0] }}"
                                                                                readonly></td>
                                                    <td class="input-xxs text-center">
                                                        <button type="button"
                                                                class="btn btn-outline-danger btn-xs mt-1 btn-remove-row">
                                                            <i class="fas fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row my-4">
                            <div class="col-12">
                                <div class="divider"></div>
                            </div>
                        </div>

                        <div class="row text-md-right">
                            <div class="col-xl-6 offset-xl-6">
                                <div class="form-group row" id="before-tax">
                                    <label class="col-md-6 col-form-label">{{ _lang('Sub Total') }}</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control text-md-right" name="sub_total"
                                               id="sub_total" value="{{ old('sub_total') }}" readonly>
                                    </div>
                                </div>

                                <div class="form-group row" id="after-tax">
                                    <label class="col-md-6 col-form-label">{{ _lang('Discount Amount') }}</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control text-md-right" name="discount"
                                               id="discount" value="{{ old('discount') }}" readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-6 col-form-label">{{ _lang('Grand Total') }}</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control text-md-right" name="grand_total"
                                               id="grand_total" value="{{ old('grand_total') }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row my-4">
                            <div class="col-12">
                                <div class="divider"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Notes') }}</label>
                                    <textarea class="form-control" name="note">{{ old('note') }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Footer') }}</label>
                                    <textarea class="form-control"
                                              name="footer">{!! xss_clean(get_business_option('invoice_footer', old('footer'))) !!}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary"><i
                                                class="ti-check-box mr-1"></i>{{ _lang('Save Invoice') }}</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4">
                <div class="card sticky-card">
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Upload CSV') }}</label>
                                    <div class="input-group">
                                        <input type="file" class="form-control" id="csvFileInput">
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block" id="uploadForm">
                                        {{ _lang('Upload CSV') }}
                                    </button>
                                </div>
                            </div>

                            <hr class="d-block w-100">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Discount Value') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <select class="form-control discount_type" id="discount_type"
                                                    name="discount_type" value="{{ old('discount_type') }}">
                                                <option value="0">%</option>
                                                <option value="1">{{ currency_symbol(request()->activeBusiness->currency) }}</option>
                                            </select>
                                        </div>
                                        <input type="number" class="form-control" name="discount_value"
                                               id="discount_value" min="0" value="{{ old('discount_value',0) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block"><i
                                                class="ti-check-box mr-1"></i>{{ _lang('Save Invoice') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <table class="d-none">
        <tr class="line-item" id="copy-line">
            <td class="input-lg">
                <input type="hidden" class="product_id" name="product_id[]">
                <input type="hidden" class="product_type" name="product_type[]">
                <input type="text" class="form-control product_name" name="product_name[]"><br>
                <textarea class="form-control description" name="description[]"
                          placeholder="{{ _lang('Descriptions') }}"></textarea>
            </td>
            <td class="input-md">
                <select name="taxes[][]" class="multi-selector product_taxes"
                        data-placeholder="{{ _lang('Select Taxes') }}" multiple>
                    @foreach(Tax::all() as $tax)
                        <option value="{{ $tax->id }}" data-tax-rate="{{ $tax->rate }}"
                                data-tax-name="{{ $tax->name }} {{ $tax->rate }} %">{{ $tax->name }} {{ $tax->rate }} %
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="input-xs text-center"><input type="number" class="form-control quantity" name="quantity[]"
                                                    min="1"></td>
            <td class="input-sm"><input type="text" class="form-control text-right unit_cost" name="unit_cost[]"></td>
            <td class="input-sm"><input type="text" class="form-control text-right sub_total" name="sub_total[]"
                                        readonly></td>
            <td class="input-xxs text-center">
                <button type="button" class="btn btn-outline-danger btn-xs mt-1 btn-remove-row"><i
                            class="fas fa-minus-circle"></i></button>
            </td>
        </tr>
    </table>
@endsection

@section('js-script')
    <script src="{{ asset('public/backend/assets/js/invoice.js?v=1.2') }}"></script>

{{--    <script>--}}
{{--        get_purchase_product(2, 'purchase', 4);--}}
{{--    </script>--}}

    <script>
        $(document).ready(function () {
            $('#uploadForm').on('click', function (event) {
                event.preventDefault();

                // Get the file input element
                var fileInput = $('#csvFileInput')[0];

                // Check if a file is selected
                if (fileInput.files.length === 0) {
                    $('#message').html('<p>Please select a CSV file first.</p>');
                    return;
                }

                // Prepare the form data (this includes the file)
                let formData = new FormData();
                formData.append('csv_file', fileInput.files[0]);

                // Send the file via AJAX to the server
                $.ajax({
                    url: '{{ route("purchase.get-csv-data") }}', // Adjust route as needed
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            response.data.forEach(function (item) {
                                // Check if item_code exists and quantity exists and quantity is not 0
                                if (item.hasOwnProperty('item_code') && item.hasOwnProperty('quantity')) {
                                    if (item.quantity != 0) {
                                        get_purchase_product(item.item_code, 'purchase', item.quantity);
                                    } else {
                                        console.log("Invalid item, quantity is 0: ", item);
                                    }
                                } else {
                                    console.log("Invalid item, missing item_code or quantity: ", item);
                                }
                            });
                        }
                    },
                    error: function (xhr) {
                        alert('Error reading the file.')
                    }
                });
            });
        });
    </script>
@endsection


