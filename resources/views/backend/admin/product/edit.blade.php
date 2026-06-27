@php use App\Models\ProductUnit; @endphp

@php use App\Models\TransactionCategory; @endphp

@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-header">
                    <span class="panel-title">{{ _lang('Update Product') }}</span>
                </div>
                <div class="card-body">
                    <form method="post" class="validate" autocomplete="off"
                          action="{{ route('products.update', $id) }}" enctype="multipart/form-data">

                        {{ csrf_field()}}

                        <input name="_method" type="hidden" value="PATCH">

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Name') }}</label>
                                    <input type="text" class="form-control" name="name" value="{{ $product->name }}"
                                           required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Ref.No / Item Code') }}</label>
                                    <input type="text" class="form-control" name="item_code"
                                           value="{{ $product->item_code }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('HS Code / Bar Code') }}</label>
                                    <input type="text" class="form-control" name="bar_code" id="bar_code"
                                           value="{{ $product->bar_code }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Deutsch') }}</label>
                                    <input type="text" class="form-control" name="deutsch" id="deutsch"
                                           value="{{ old('deutsch', $product->deutsch) }}" >
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Weight Per Item') }}</label>
                                    <input type="text" class="form-control" name="weight_per_item" id="weight_per_item"
                                           value="{{ old('weight_per_item', $product->weight_per_item) }}" >
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Type') }}</label>
                                    <select class="form-control auto-select" data-selected="{{ $product->type }}"
                                            name="type" required>
                                        <option value="product">{{ _lang('Product') }}</option>
                                        <option value="service">{{ _lang('Service') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Product Unit') }}</label>

                                    <select class="form-control select2-ajax auto-select"
                                            name="product_unit_id"
                                            data-selected="{{ $product->product_unit_id }}"
                                            data-value="id"
                                            data-display="unit"
                                            data-table="product_units"
                                            data-title="{{ _lang('New Product Unit') }}"
                                            data-href="{{ route('product_units.create') }}"
                                            data-where="3">

                                        <option value="">{{ _lang('Select One') }}</option>
                                        @foreach(ProductUnit::all() as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->unit }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Allow For Selling') }} ?</label>
                                    <select class="form-control auto-select c-select"
                                            data-selected="{{ $product->allow_for_selling }}"
                                            data-show="income-category" data-condition="1" name="allow_for_selling">
                                        <option value="1">{{ _lang('Yes') }}</option>
                                        <option value="0">{{ _lang('No') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Allow For Purchasing') }} ?</label>
                                    <select class="form-control auto-select c-select"
                                            data-selected="{{ $product->allow_for_purchasing }}"
                                            data-show="expense-category" data-condition="1" name="allow_for_purchasing">
                                        <option value="1">{{ _lang('Yes') }}</option>
                                        <option value="0">{{ _lang('No') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 {{ $product->allow_for_selling != 1 ? 'd-none' : '' }} income-category">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Selling Price').' ('.currency_symbol(request()->activeBusiness->currency).')' }}
                                        <span class="required"> *</span></label>
                                    <input type="text" class="form-control float-field no-msg" name="selling_price"
                                           value="{{ $product->selling_price }}">
                                </div>
                            </div>

                            <div class="col-md-6 {{ $product->allow_for_selling != 1 ? 'd-none' : '' }} income-category">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Income Category') }} <span class="required"> *</span></label>
                                    <select class="form-control select2-ajax no-msg auto-select"
                                            data-selected="{{ $product->income_category_id }}"
                                            name="income_category_id" data-value="id" data-display="name"
                                            data-table="transaction_categories"
                                            data-title="{{ _lang('New Category') }}"
                                            data-href="{{ route('transaction_categories.create') }}?type=income"
                                            data-where="4">
                                        <option value="">{{ _lang('Select One') }}</option>
                                        @foreach(TransactionCategory::income()->get() as $income_category)
                                            <option value="{{ $income_category->id }}">{{ $income_category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 {{ $product->allow_for_purchasing != 1 ? 'd-none' : '' }} expense-category">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Purchase Cost').' ('.currency_symbol(request()->activeBusiness->currency).')' }}
                                        <span class="required"> *</span></label>
                                    <input type="text" class="form-control float-field no-msg" name="purchase_cost"
                                           value="{{ $product->purchase_cost }}">
                                </div>
                            </div>

                            <div class="col-md-6 {{ $product->allow_for_purchasing != 1 ? 'd-none' : '' }} expense-category">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Expense Category') }} <span class="required"> *</span></label>
                                    <select class="form-control select2-ajax no-msg auto-select"
                                            data-selected="{{ $product->expense_category_id }}"
                                            name="expense_category_id" data-value="id" data-display="name"
                                            data-table="transaction_categories"
                                            data-title="{{ _lang('New Category') }}"
                                            data-href="{{ route('transaction_categories.create') }}?type=expense"
                                            data-where="5">
                                        <option value="">{{ _lang('Select One') }}</option>
                                        @foreach(TransactionCategory::expense()->get() as $expense_category)
                                            <option value="{{ $expense_category->id }}">{{ $expense_category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Image') }}</label>
                                    <input type="file" class="form-control dropify" name="image"
                                           data-default-file="{{ asset('public/uploads/media/'.$product->image) }}"
                                           data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Descriptions') }}</label>
                                    <textarea class="form-control"
                                              name="descriptions">{{ $product->descriptions }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Status') }}</label>
                                    <select class="form-control auto-select" data-selected="{{ $product->status }}"
                                            name="status" required>
                                        <option value="1">{{ _lang('Active') }}</option>
                                        <option value="0">{{ _lang('Disabled') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Stock Management') }}</label>
                                    <select class="form-control auto-select"
                                            data-selected="{{ $product->stock_management }}" name="stock_management"
                                            required>
                                        <option value="1">{{ _lang('Yes') }}</option>
                                        <option value="0">{{ _lang('No') }}</option>
                                    </select>
                                    <small class="text-danger"><i
                                                class="fas fa-exclamation-circle mr-1"></i>{{ _lang('Works for product only!') }}
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Available Stock') }}</label>
                                    <input type="text" class="form-control" name="stock" value="{{ $product->stock }}"
                                           required>
                                    <small class="text-danger"><i
                                                class="fas fa-exclamation-circle mr-1"></i>{{ _lang('Works for product only!') }}
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti-check-box mr-1"></i>{{ _lang('Save Changes') }}
                                    </button>
                                </div>
                            </div>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-script')
    <script>
        $(document).ready(function () {
            let barCode = $('#bar_code');

            $(barCode).on("keypress", function (event) {
                let keyCode = event.which;
                if (!(keyCode >= 48 && keyCode <= 57) && !(keyCode === 8 || keyCode === 9 || keyCode === 46)) {
                    event.preventDefault();
                }
            });

            $(barCode).on("paste", function (event) {
                let pastedData = event.originalEvent.clipboardData.getData('text');
                let numericData = pastedData.replace(/[^0-9]/g, '');
                event.preventDefault();
                document.execCommand("insertText", false, numericData);
            });
        });
    </script>
@endsection