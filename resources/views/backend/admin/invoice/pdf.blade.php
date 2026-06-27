<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ get_option('site_title', 'Invoice') }}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ public_path('backend/plugins/bootstrap/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ public_path('backend/assets/css/styles.css') }}">
        <link rel="stylesheet" href="{{ public_path('backend/assets/css/default-css.css') }}">
        <link rel="stylesheet" href="{{ public_path('backend/assets/css/invoice.css') }}">
        @include('layouts.others.invoice-css')
    </head>
    <body>
        @include('backend.admin.invoice.template.loader', ['type' => 'pdf'])
    </body>
</html>	


