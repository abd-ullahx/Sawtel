<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span class="panel-title">{{ _lang('Purchase Order') }} #{{ $purchase->bill_no }} </span>
        <div class="d-flex">
            @if($purchase->delivery_status == 'on_the_way')
                <form action="{{ route('purchase.order-received', $purchase->id) }}" method="post">
                    @csrf
                    <button class="btn btn-primary btn-xs mr-2" type="submit"
                        onclick="confirm('Are you sure you want to complete this order. This can\'t be undo)">
                        Order Received
                    </button>
                </form>
            @endif

            <div class="dropdown">
                <button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown"
                        aria-expanded="false">
                    <i class="fas fa-cog mr-1"></i>{{ _lang('Actions') }}
                </button>
                <div class="dropdown-menu">
                    <a href="{{ route('purchases.add_payment', $purchase->id) }}" class="dropdown-item ajax-modal"
                       data-title="{{ _lang('Add Payment') }}"><i
                                class="far fa-credit-card mr-2"></i>{{ _lang('Add Payment') }}</a>
                    <a href="{{ route('purchases.duplicate', $purchase->id) }}" class="dropdown-item"><i
                                class="far fa-copy mr-2"></i>{{ _lang('Duplicate') }}</a>
                    <a href="#" class="dropdown-item print" data-print="invoice"><i
                                class="fas fa-print mr-2"></i>{{ _lang('Print Invoice') }}</a>
                    <div class="dropdown-divider"></div>
                    @if($purchase->status != 2)
                        <a href="{{ route('purchases.edit', $purchase->id) }}" class="dropdown-item"><i
                                    class="far fa-edit mr-2"></i>{{ _lang('Edit') }}</a>
                    @endif
                    <form action="{{ route('purchases.destroy', $purchase->id) }}" method="post">
                        @csrf
                        <input name="_method" type="hidden" value="DELETE">
                        <button class="dropdown-item btn-remove" type="submit"><i
                                    class="far fa-trash-alt mr-2"></i>{{ _lang('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="d-md-flex justify-content-between border border-secondary rounded p-3">
            <div>
                <span>{{ _lang('Status') }}:</span>
                <span>{!! xss_clean(purchase_status($purchase)) !!}</span>
            </div>
            <div>
                <span>{{ _lang('Order Status') }}:</span>
                <span>{!! xss_clean(purchase_order_status($purchase)) !!}</span>
            </div>
            <div>
                <span>{{ _lang('Vendor') }}:</span>
                <span>{{ $purchase->vendor->name }}</span>
            </div>
            <div>
                <span>{{ _lang('Grand Total') }}:</span>
                <span>{{ formatAmount($purchase->grand_total, currency_symbol($purchase->business->currency), $purchase->business_id) }}</span>
            </div>
            <div>
                <span>{{ _lang('Amount Due') }}:</span>
                <span>{{ formatAmount($purchase->grand_total - $purchase->paid, currency_symbol($purchase->business->currency), $purchase->business_id) }}</span>
            </div>
        </div>

        @if($purchase->transactions->count() > 0)
            <table class="table table-bordered mt-4">
                <thead>
                <th>{{ _lang('Date') }}</th>
                <th>{{ _lang('Method') }}</th>
                <th class="text-right">{{ _lang('Amount') }}</th>
                <th class="text-right">{{ _lang('Purchase Amount') }}</th>
                </thead>
                <tbody>
                @foreach($purchase->transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->trans_date }}</td>
                        <td>{{ $transaction->method }}</td>
                        <td class="text-right">{{ formatAmount($transaction->amount, currency_symbol($transaction->account->currency)) }}</td>
                        <td class="text-right">{{ formatAmount($transaction->ref_amount, currency_symbol($purchase->business->currency)) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>