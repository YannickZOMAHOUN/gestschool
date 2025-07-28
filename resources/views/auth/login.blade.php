<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion - Espace Privé</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4bb543;
            --error-color: #ff3333;
            --info-color: #4895ef;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .background-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .background-shapes div {
            position: absolute;
            border-radius: 50%;
            background: rgba(67, 97, 238, 0.1);
        }

        .background-shapes div:nth-child(1) {
            width: 600px;
            height: 600px;
            top: -200px;
            left: -200px;
        }

        .background-shapes div:nth-child(2) {
            width: 400px;
            height: 400px;
            bottom: -100px;
            right: -100px;
        }

        .background-shapes div:nth-child(3) {
            width: 300px;
            height: 300px;
            top: 50%;
            right: 20%;
        }

        .login-container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            margin: 20px;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
            z-index: 1;
        }

        .login-container:hover {
            transform: translateY(-5px);
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: var(--primary-color);
            font-size: 2.2rem;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .login-header p {
            color: #666;
            font-size: 0.95rem;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            color: var(--dark-color);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .input-with-icon {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-with-icon i {
            position: absolute;
            left: 15px;
            color: var(--primary-color);
            font-size: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            background-color: white;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input {
            accent-color: var(--primary-color);
            cursor: pointer;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .login-btn {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .dashboard-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            padding: 12px;
            background: linear-gradient(135deg, var(--info-color), var(--accent-color));
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dashboard-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .dashboard-link i {
            margin-right: 8px;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
            color: #ccc;
            font-size: 0.8rem;
        }

        .divider::before, .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #eee;
        }

        .divider::before {
            margin-right: 10px;
        }

        .divider::after {
            margin-left: 10px;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }

            .login-header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="background-shapes">
        <div></div><div></div><div></div>
    </div>

    <div class="login-container">
        <div class="login-header">
            <h1>Bienvenue</h1>
            <p>Connectez-vous à votre compte pour continuer</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf

            <div class="form-group">
                <label for="email">Adresse Email</label>
                <div class="input-with-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-control" placeholder="votre@email.com" value="{{ old('email') }}" required autofocus>
                </div>
                @error('email')
                    <span style="color: var(--error-color); font-size: 0.8rem; margin-top: 5px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                @error('password')
                    <span style="color: var(--error-color); font-size: 0.8rem; margin-top: 5px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="remember-forgot">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Se souvenir de moi</label>
                </div>
                <div class="forgot-password">
                    <a href="{{ route('password.request') }}">Mot de passe oublié?</a>
                </div>
            </div>

            <button type="submit" class="login-btn">Se connecter</button>

            <div class="divider">OU</div>

            <a href="{{ route('parents.dashboard') }}" class="dashboard-link">
                <i class="fas fa-chart-line"></i> Consulter un résultat
            </a>
        </form>
    </div>
</body>
</html>
