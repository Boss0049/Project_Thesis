<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- <title>{{ config('app.name', 'Elearning') }}</title> -->
    <link rel="shortcut icon" href="http://image.free.in.th/v/2013/ii/200419021953.png">
    <title>PROJECT</title>    
    <!-- Styles -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Athiti|Sarabun&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/design.css') }}" rel="stylesheet">
    <link href="{{ asset('css/.css') }}" rel="stylesheet">
    <link href="{{ asset('css/loading.css') }}" rel="stylesheet">
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
    <script>
        window.Laravel = {
            'baseUrl': "{{ url('/') }}"
        }
    </script>
    <style>
        html body {
            margin: 0;
            background: #f8f8f8;
            cursor: default;
        }

        @media only screen and (max-width: 767px) {
            html body {
                background: #fff;
            }
        }
    </style>
</head>

<body>
    <div id="app">
    </div>
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
</body>