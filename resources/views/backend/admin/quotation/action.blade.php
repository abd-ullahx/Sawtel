<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span class="panel-title">{{ _lang('Quotation') }} #{{ $quotation->quotation_number }} </span>
        <div>          
            <div class="dropdown">
                <button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-cog mr-1"></i>{{ _lang('Actions') }}
                </button>
                <div class="dropdown-menu">
                    <a href="{{ route('quotations.duplicate', $quotation['id']) }}" class="dropdown-item"><i class="far fa-copy mr-2"></i>{{ _lang('Duplicate') }}</a>
                    <a href="{{ route('quotations.convert_to_invoice', $quotation['id']) }}" class="dropdown-item"><i class="fas fa-recycle mr-2"></i>{{ _lang('Convert to Invoice') }}</a>
                    <a href="{{ route('quotations.send_email', $quotation->id) }}" class="dropdown-item ajax-modal" data-title="{{ _lang('Send Email') }}"><i class="far fa-paper-plane mr-2"></i>{{ _lang('Send Email') }}</a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item print" data-print="invoice"><i class="fas fa-print mr-2"></i>{{ _lang('Print Quotation') }}</a>
                    <a href="{{ route('quotations.export_pdf', $quotation->id) }}" class="dropdown-item"><i class="far fa-file-pdf mr-2"></i>{{ _lang('Export PDF') }}</a>
                    <a href="{{ route('quotations.get_quotation_link', $quotation->id) }}" data-title="{{ _lang('Get share link') }}" class="dropdown-item ajax-modal"><i class="fas fa-share-alt mr-2"></i>{{ _lang('Share Quotation') }}</a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('quotations.edit', $quotation->id) }}" class="dropdown-item"><i class="far fa-edit mr-2"></i>{{ _lang('Edit') }}</a>
                    <form action="{{ route('quotations.destroy', $quotation['id']) }}" method="post">
                    @csrf
                    <input name="_method" type="hidden" value="DELETE">
                    <button class="dropdown-item btn-remove" type="submit"><i class="far fa-trash-alt mr-2"></i>{{ _lang('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="d-md-flex justify-content-between border border-secondary rounded p-3">
            <div>
                <span>{{ _lang('Customer') }}:</span>
                <span>{{ $quotation->customer->name }}</span>
            </div>
            <div>
                <span>{{ _lang('Grand Total') }}:</span>
                <span>{{ formatAmount($quotation->grand_total, currency_symbol($quotation->business->currency), $quotation->business_id) }}</span>
            </div>
            <div>
                <span>{{ _lang('Expired At') }}:</span>
                <span>{{ $quotation->expired_date }}</span>
            </div>
        </div>
    </div>
</div>