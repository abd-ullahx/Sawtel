<div class="card">
    <div class="card-header">
        <span class="panel-title">{{ _lang('Invoices') }}</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table border-bottom">
                <thead>
                    <tr>
                        <th>{{ _lang('Date') }}</th>
                        <th>{{ _lang('Due Date') }}</th>
                        <th>{{ _lang('Invoice Number') }}</th>
                        <th class="text-right">{{ _lang('Grand Total') }}</th>
                        <th class="text-right">{{ _lang('Amount Due') }}</th>
                        <th class="text-center">{{ _lang('Status') }}</th>
                        <th class="text-center">{{ _lang('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_date }}</td>
                            <td>{{ $invoice->due_date }}</td>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td class="text-right">{{ formatAmount($invoice->grand_total, currency_symbol(request()->activeBusiness->currency)) }}</td>
                            <td class="text-right">{{ formatAmount($invoice->grand_total - $invoice->paid, currency_symbol(request()->activeBusiness->currency)) }}</td>
                            <td class="text-center">{!! xss_clean(invoice_status($invoice)) !!}</td>
                            <td class="text-center">
                                <a class="btn btn-xs btn-outline-primary" href="{{ route('invoices.show', $invoice['id']) }}"><i class="far fa-eye mr-1"></i>{{ _lang('Preview') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="float-right">
            {{ $invoices->links() }}
        </div>
    </div>
</div>