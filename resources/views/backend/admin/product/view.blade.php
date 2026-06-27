@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-header">
                    <span class="panel-title">{{ _lang('Product Details') }}</span>
                </div>

                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <td colspan="2" class="text-center">
                                <img
                                        src="{{ ($product->image) ? asset('public/uploads/media/'.$product->image) : asset('public/uploads/media/default.png') }}"
                                        class="rounded product-img" alt="{{ $product->name }}" width="100px">
                            </td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Name') }}</td>
                            <td>{{ $product->name }}</td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Item Code') }}</td>
                            <td>{{ $product->item_code }}</td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Type') }}</td>
                            <td>{{ ucwords($product->type) }}</td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Product Unit') }}</td>
                            <td>{{ $product->product_unit->unit }}</td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Purchase Cost') }}</td>
                            <td>{{ formatAmount($product->purchase_cost, currency_symbol(request()->activeBusiness->currency)) }}</td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Selling Price') }}</td>
                            <td>{{ formatAmount($product->selling_price, currency_symbol(request()->activeBusiness->currency)) }}</td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Descriptions') }}</td>
                            <td>{{ $product->descriptions }}</td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Stock') }}</td>
                            <td>{{ $product->stock }}</td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Allow For Selling') }}</td>
                            <td>{!! $product->allow_for_selling == 1 ? xss_clean(show_status(_lang('Yes'), 'success')) : xss_clean(show_status(_lang('No'), 'danger')) !!}</td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Allow For Purchasing') }}</td>
                            <td>{!! $product->allow_for_purchasing == 1 ? xss_clean(show_status(_lang('Yes'), 'success')) : xss_clean(show_status(_lang('No'), 'danger')) !!}</td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Income Category') }}</td>
                            <td>{{ $product->income_category->name }}</td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Expense Category') }}</td>
                            <td>{{ $product->expense_category->name }}</td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Status') }}</td>
                            <td>{!! xss_clean(status($product->status)) !!}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


