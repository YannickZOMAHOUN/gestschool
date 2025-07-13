<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>School Manager</title>
     <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logoicon.png') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,700;1,500&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>

<body>
    <div class="login-wrapper">
        <!-- Logo -->
        <img src="{{ asset('images/logo.png') }}" alt="logo" class="logo-icone">

        <!-- Title -->
        <h1 class="text-white text-center mb-4 fs-22 font-bold">GESTION DES BULLETINS</h1>

        <!-- Login Content -->
        <div class="login-wrapper-content card shadow p-5 login-border">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS (optional, if you need Bootstrap components that require JavaScript) -->
    <script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
