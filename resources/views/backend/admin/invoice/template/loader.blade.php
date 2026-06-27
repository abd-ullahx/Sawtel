@php $type = isset($type) ? $type : 'preview'; @endphp

@if($invoice->template_type == 0)
    @include('backend.admin.invoice.template.'.$invoice->template, ['type' => $type])
@elseif($invoice->template_type == 1 && $invoice->invoice_template->name != null)
    @include('backend.admin.invoice.template.custom', ['type' => $type])
@else
    @include('backend.admin.invoice.template.default', ['type' => $type])
@endif