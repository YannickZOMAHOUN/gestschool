<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Manager</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logoicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f72585;
            --gray: #6c757d;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: var(--dark);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 20px 0;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            height: 30px;
        }

        .user-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-weight: bold;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        .content-wrapper {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            margin: 30px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            min-height: 400px;
        }

        h1 {
            color: var(--primary);
            font-family: 'Montserrat', sans-serif;
            margin-top: 0;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: var(--gray);
            font-size: 14px;
            margin-top: 50px;
        }

        /* Dropdown menu pour mobile */
        @media (max-width: 768px) {
            .user-actions {
                position: relative;
            }

            .logout-btn {
                padding: 6px 10px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo">
                <img src="{{ asset('images/logoicon.png') }}" alt="Logo EduTrack">
                SCHOOL MANAGER
            </div>
            <div class="user-actions">
                <div class="user-info">
                    <span>Parent de {{ $student->surname ?? '[Prénom Enfant]' }} {{ $student->name ?? '[Prénom Enfant]' }}</span>
                    <div class="user-avatar">P</div>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <button class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    Déconnexion
                </button>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="content-wrapper">
            @yield('content')
        </div>
    </main>

    <footer>
        <p>Plateforme EduTrack - Suivi scolaire © {{ date('Y') }}</p>
        <p>Pour toute question, contactez-nous à contact@edutrack.fr</p>
    </footer>

    <script>
        // Animation pour le bouton de déconnexion
        document.querySelector('.logout-btn').addEventListener('mouseenter', function() {
            this.innerHTML = '<i class="fas fa-sign-out-alt"></i> Se déconnecter';
        });

        document.querySelector('.logout-btn').addEventListener('mouseleave', function() {
            this.innerHTML = '<i class="fas fa-sign-out-alt"></i> Déconnexion';
        });
    </script>
</body>
</html>
