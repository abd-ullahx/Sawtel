@php use Carbon\Carbon; @endphp

@php use App\Models\Customer; @endphp

@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">

                <div class="card-header d-sm-flex align-items-center justify-content-between">
                    <span class="panel-title">{{ _lang('Sale Products Report') }}</span>
                </div>

                <div class="card-body">

                    @php $date_format = get_date_format(); @endphp

                    <div id="report">

                        <div class="report-header">
                            <h4>{{ request()->activeBusiness->name }}</h4>
                            <p>{{ _lang('Sale Report') }}</p>
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <th>#</th>
                                <th>Product Ref.No</th>
                                <th>Product Name</th>
                                <th>Customer Id</th>
                                <th>Customer Name</th>
                                <th>Quantity</th>
                                <th>Date</th>
                                </thead>
                                <tbody>

                                @foreach($invoiceItems as $invoiceItem)
                                    <tr>
                                        <td>#{{ $invoiceItem->id ?? '' }}</td>
                                        <td>#{{ $invoiceItem->product->item_code ?? '' }}</td>
                                        <td>{{ $invoiceItem->product_name ?? '' }}</td>
                                        <td>{{ $invoiceItem->invoice->customer->id ?? '' }}</td>
                                        <td>{{ $invoiceItem->invoice->customer->name ?? '' }}</td>
                                        <td>{{ number_format($invoiceItem->quantity ?? '', 0) }}</td>
                                        <td>{{ Carbon::parse($invoiceItem->created_at)->isoFormat('LLL') }}</td>
                                    </tr>
                                @endforeach


                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 d-flex justify-content-center">
                            {{ $invoiceItems->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection