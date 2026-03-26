<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')::Flexicell Phone Shop</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('mdb/css/mdb.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d10024' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .auth-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
        }

        .auth-logo {
            display: block;
            margin: 0 auto 1.5rem;
            max-width: 350px;
        }

        .auth-title {
            color: #5e5873;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .auth-subtitle {
            color: #6e6b7b;
            font-size: 0.95rem;
        }

        .form-label {
            font-weight: 500;
            color: #5e5873;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            padding: 0.8rem 1rem;
            border-radius: 6px;
        }

        .btn-primary {
            background-color: #D10024 !important;
            border-color: #D10024 !important;
            box-shadow: 0 8px 25px -8px #D10024 !important;
            padding: 0.8rem !important;
            font-weight: 600 !important;
            text-transform: none !important;
            font-size: 1rem !important;
        }

        .btn-primary:hover {
            background-color: #A3001C !important;
        }

        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #b9b9c3;
            z-index: 10;
        }

        .input-group-merge {
            position: relative;
        }

        .auth-footer {
            margin-top: 1.5rem;
            color: #6e6b7b;
        }

        .auth-footer a {
            color: #D10024;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div id="app" class="h-100">
        <main class="h-100">
            @yield('content')
        </main>
    </div>
    <script src="{{ asset('mdb/js/mdb.umd.min.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>

    <!-- Laravel Javascript Validation -->
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
</body>

</html>
