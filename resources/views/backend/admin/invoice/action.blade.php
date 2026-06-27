@if($invoice->is_recurring == 0)
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span class="panel-title">{{ _lang('Invoice') }} #{{ $invoice->invoice_number }} </span>
            <div>
                <div class="dropdown">
                    <button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-expanded="false">
                        <i class="fas fa-cog mr-1"></i>{{ _lang('Actions') }}
                    </button>
                    <div class="dropdown-menu">
                        @if($invoice->status != 0 && $invoice->status != 99)
                            <a href="{{ route('invoices.add_payment', $invoice->id) }}" class="dropdown-item ajax-modal"
                               data-title="{{ _lang('Add Invoice Payment') }}"><i
                                        class="far fa-credit-card mr-2"></i>{{ _lang('Add Payment') }}</a>
                        @endif
                        {{--                    <a href="{{ route('invoices.send_email', $invoice->id) }}" class="dropdown-item ajax-modal" data-title="{{ _lang('Send Email') }}"><i class="far fa-paper-plane mr-2"></i>{{ _lang('Send Email') }}</a>--}}
                        @if($invoice->status != 99)
                            <a href="{{ route('invoices.duplicate', $invoice->id) }}" class="dropdown-item"><i
                                        class="far fa-copy mr-2"></i>{{ _lang('Duplicate') }}</a>
                            {{--                    <a href="{{ route('recurring_invoices.convert_recurring', $invoice->id) }}" class="dropdown-item"><i class="fas fa-recycle mr-2"></i>{{ _lang('Convert to Recurring') }}</a>--}}
                        @endif

                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item print" data-print="invoice"><i
                                    class="fas fa-print mr-2"></i>{{ _lang('Print Invoice') }}</a>
                        <a href="{{ route('invoices.export_pdf', $invoice->id) }}" class="dropdown-item"><i
                                    class="far fa-file-pdf mr-2"></i>{{ _lang('Export PDF') }}</a>
                        <a href="{{ route('invoices.get_invoice_link', $invoice->id) }}"
                           data-title="{{ _lang('Get share link') }}" class="dropdown-item ajax-modal"><i
                                    class="fas fa-share-alt mr-2"></i>{{ _lang('Share Invoice') }}</a>
                        <div class="dropdown-divider"></div>

                        @if($invoice->status != 2 || $invoice->status != 99)
                            <a href="{{ route('invoices.edit', $invoice->id) }}" class="dropdown-item"><i
                                        class="far fa-edit mr-2"></i>{{ _lang('Edit') }}</a>
                        @endif

                        @if($invoice->status != 99)
                            <a href="{{ route('invoices.mark_as_cancelled', $invoice->id) }}"
                               data-message="{{ _lang('If you cancel this invoice then product stock will be revert and all associated payment will be removed') }}"
                               class="dropdown-item process-alert">
                                <i class="fas fa-ban mr-2"></i>
                                {{ _lang('Mark as Cancelled') }}
                            </a>
                        @endif

                        <form action="{{ route('invoices.destroy', $invoice['id']) }}" method="post">
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
            <div class="d-md-flex justify-content-between border border-secondary rounded p-3 mb-3">
                <div>
                    <span>{{ _lang('Status') }}:</span>
                    <span>{!! xss_clean(invoice_status($invoice)) !!}</span>
                </div>
                <div>
                    <span>{{ _lang('Customer') }}:</span>
                    <span>{{ $invoice->customer->name }}</span>
                </div>
                <div>
                    <span>{{ _lang('Grand Total') }}:</span>
                    <span>{{ formatAmount($invoice->grand_total, currency_symbol($invoice->business->currency), $invoice->business_id) }}</span>
                </div>
                <div>
                    <span>{{ _lang('Amount Due') }}:</span>
                    <span>{{ formatAmount($invoice->grand_total - $invoice->paid, currency_symbol($invoice->business->currency), $invoice->business_id) }}</span>
                </div>
            </div>

            {{--        <div class="d-md-flex justify-content-between align-items-center border border-secondary rounded p-3">--}}
            {{--            <div>--}}
            {{--                <span>{{ _lang('Email Status') }}:</span>--}}
            {{--                <span>{!! $invoice->email_send == 1 ? xss_clean(show_status(_lang('Sent'), 'success')) : xss_clean(show_status(_lang('Not Sent'), 'danger')) !!}</span>--}}
            {{--            </div>--}}
            {{--            <div>--}}
            {{--                @if($invoice->email_send == 1)--}}
            {{--                <span>{{ _lang('Sent At') }}:</span>--}}
            {{--                <span>{{ $invoice->email_send_at }}</span>--}}
            {{--                @else--}}
            {{--                <a href="{{ route('invoices.send_email', $invoice->id) }}" class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Send Email') }}"><i class="far fa-paper-plane mr-2"></i>{{ _lang('Send Email') }}</a>--}}
            {{--                @endif--}}
            {{--            </div>--}}
            {{--        </div>--}}

            @if($invoice->status == 0)
                <div class="alert alert-warning d-flex align-items-center justify-content-between mt-4">
                    <span><strong><i class="ti-info-alt mr-2"></i>{{ _lang('You need to approve this draft invoice before further action.') }}</strong></span>
                    <a href="{{ route('invoices.approve', $invoice->id) }}" class="btn btn-primary btn-xs"><i
                                class="fas fa-check-circle mr-2"></i>{{ _lang('Approve') }}</a>
                </div>
            @endif

            @if($invoice->transactions->count() > 0)
                <table class="table border mt-4">
                    <thead>
                    <th>{{ _lang('Date') }}</th>
                    <th>{{ _lang('Method') }}</th>
                    <th class="text-right">{{ _lang('Amount') }}</th>
                    <th class="text-right">{{ _lang('Invoice Amount') }}</th>
                    </thead>
                    <tbody>
                    @foreach($invoice->transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->trans_date }}</td>
                            <td>{{ $transaction->method }}</td>
                            <td class="text-right">{{ formatAmount($transaction->amount, currency_symbol($transaction->account->currency)) }}</td>
                            <td class="text-right">{{ formatAmount($transaction->ref_amount, currency_symbol($invoice->business->currency)) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

        </div>
    </div>

@elseif($invoice->is_recurring == 1)
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span class="panel-title">{{ _lang('Recurring invoice') }}</span>
            <div>
                <div class="dropdown">
                    <button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-expanded="false">
                        <i class="fas fa-cog mr-1"></i>{{ _lang('Actions') }}
                    </button>
                    <div class="dropdown-menu">
                        <a href="{{ route('recurring_invoices.duplicate', $invoice->id) }}" class="dropdown-item"><i
                                    class="far fa-copy mr-2"></i>{{ _lang('Duplicate') }}</a>
                        <a href="{{ route('recurring_invoices.end_recurring', $invoice->id) }}" class="dropdown-item"><i
                                    class="far fa-stop-circle mr-2"></i>{{ _lang('End Recurring') }}</a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('recurring_invoices.edit', $invoice->id) }}" class="dropdown-item"><i
                                    class="far fa-edit mr-2"></i>{{ _lang('Edit') }}</a>
                        <form action="{{ route('recurring_invoices.destroy', $invoice->id) }}" method="post">
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
                    <span>{!! xss_clean(recurring_invoice_status($invoice->status)) !!}</span>
                </div>
                <div>
                    <span>{{ _lang('Customer') }}:</span>
                    <span>{{ $invoice->customer->name }}</span>
                </div>
                <div>
                    <span>{{ _lang('Recurring') }}:</span>
                    <span class="text-primary"><b>{{ _lang('Every') . ' ' . $invoice->recurring_value . ' ' . $invoice->recurring_type }}</b></span>
                </div>
                <div>
                    <span>{{ _lang('Grand Total') }}:</span>
                    <span>{{ formatAmount($invoice->grand_total, currency_symbol($invoice->business->currency), $invoice->business_id) }}</span>
                </div>
            </div>

            @if($invoice->status == 0)
                <div class="alert alert-warning d-flex align-items-center justify-content-between mt-4">
                    <span><strong><i class="ti-info-alt mr-2"></i>{{ _lang('You need to approve this draft invoice before further action.') }}</strong></span>
                    <a href="{{ route('recurring_invoices.approve', $invoice->id) }}" class="btn btn-primary btn-xs"><i
                                class="fas fa-check-circle mr-2"></i>{{ _lang('Approve') }}</a>
                </div>
            @endif
        </div>
    </div>
@endif