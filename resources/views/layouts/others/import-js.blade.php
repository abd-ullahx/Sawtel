@isset($assets)

@if(in_array("datatable", $assets))
<script src="{{ asset('public/backend/plugins/datatable/datatables.min.js') }}"></script>
@endif

@if(in_array("slimscroll", $assets))
<script src="{{ asset('public/backend/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
@endif

@if(in_array("tinymce", $assets))
<script src="{{ asset('public/backend/plugins/tinymce/tinymce.min.js') }}"></script>
@endif

@if(in_array("summernote", $assets))
<script src="{{ asset('public/backend/plugins/summernote/summernote-bs4.min.js') }}"></script>
@endif

@endisset