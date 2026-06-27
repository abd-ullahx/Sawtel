@isset($assets)
@if(in_array("datatable", $assets))
<link href="{{ asset('public/backend/plugins/datatable/datatables.min.css') }}" rel="stylesheet" type="text/css" /> 
@endif

@if(in_array("summernote", $assets))
<link href="{{ asset('public/backend/plugins/summernote/summernote-bs4.min.css') }}" rel="stylesheet" type="text/css" />
@endif
@endisset