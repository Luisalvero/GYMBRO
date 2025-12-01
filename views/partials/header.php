<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'GymBro' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    <style>
        :root {
            --bg-dark: #0a0a0a;
            --bg-darker: #050505;
            --bg-card: #151515;
            --primary: #ff4444;
            --primary-hover: #cc3333;
            --danger: #ff0055;
            --text: #ffffff;
            --text-muted: #888888;
            --border: #222222;
        }
        
        * {
            scrollbar-width: thin;
            scrollbar-color: var(--primary) var(--bg-darker);
        }
        
        *::-webkit-scrollbar {
            width: 8px;
        }
        
        *::-webkit-scrollbar-track {
            background: var(--bg-darker);
        }
        
        *::-webkit-scrollbar-thumb {
            background-color: var(--primary);
            border-radius: 4px;
        }
        
        body {
            background: var(--bg-dark);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        
        h1, h2, h3, h4, h5, h6, .navbar-brand {
            font-family: 'Rajdhani', sans-serif;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .navbar {
            background: var(--bg-darker) !important;
            border-bottom: 2px solid var(--border);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            color: var(--primary) !important;
            font-size: 1.8rem;
            font-weight: 700;
            text-shadow: 0 0 20px rgba(255, 68, 68, 0.3);
            transition: all 0.3s ease;
        }
        
        .navbar-brand:hover {
            text-shadow: 0 0 30px rgba(255, 68, 68, 0.5);
            transform: scale(1.05);
        }
        
        .nav-link {
            color: var(--text-muted) !important;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover {
            color: var(--primary) !important;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, #dd3636 100%);
            border: none;
            color: #000;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.6rem 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 68, 68, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-hover) 0%, #bb2828 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(255, 68, 68, 0.4);
            color: #000;
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            background: transparent;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary);
            color: #000;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 68, 68, 0.3);
        }
        
        .btn-outline-secondary {
            border: 2px solid var(--text-muted);
            color: var(--text-muted);
            background: transparent;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-secondary:hover {
            background: var(--text-muted);
            color: #000;
            border-color: var(--text-muted);
        }
        
        .btn-secondary {
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-secondary:hover {
            background: var(--border);
            border-color: var(--border);
            color: var(--text);
        }
        
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text);
            transition: all 0.3s ease;
        }
        
        .card-profile {
            box-shadow: 0 4px 20px rgba(255, 68, 68, 0.1);
            transition: all 0.3s ease;
            border: 1px solid var(--border);
        }
        
        .card-profile:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(255, 68, 68, 0.2);
            border-color: var(--primary);
        }
        
        .form-control, .form-select {
            background: var(--bg-darker);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            background: var(--bg-darker);
            border-color: var(--primary);
            color: var(--text);
            box-shadow: 0 0 0 0.2rem rgba(255, 68, 68, 0.1);
        }
        
        .form-control::placeholder {
            color: var(--text-muted);
        }
        
        .form-label {
            color: var(--text);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        
        .form-check-input {
            background-color: var(--bg-darker);
            border-color: var(--border);
        }
        
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .form-check-label {
            color: var(--text);
        }
        
        .badge-workout {
            background: linear-gradient(135deg, var(--primary) 0%, #dd3636 100%);
            color: #000;
            font-size: 0.75rem;
            padding: 0.4em 0.8em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 4px;
        }
        
        .badge {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .bg-secondary {
            background-color: var(--text-muted) !important;
        }
        
        .alert {
            border: none;
            border-left: 4px solid;
            background: var(--bg-card);
            color: var(--text);
        }
        
        .alert-success {
            border-left-color: var(--primary);
            background: rgba(255, 68, 68, 0.1);
        }
        
        .alert-danger {
            border-left-color: var(--danger);
            background: rgba(255, 0, 85, 0.1);
        }
        
        .alert-info {
            border-left-color: #00aaff;
            background: rgba(0, 170, 255, 0.1);
        }
        
        .text-muted {
            color: var(--text-muted) !important;
        }
        
        .text-primary {
            color: var(--primary) !important;
        }
        
        .text-danger {
            color: var(--danger) !important;
        }
        
        .display-1, .display-4 {
            color: var(--primary);
        }
        
        hr {
            border-color: var(--border);
            opacity: 1;
        }
        
        .shadow, .shadow-sm, .shadow-lg {
            box-shadow: 0 4px 20px rgba(255, 68, 68, 0.1) !important;
        }
        
        a {
            color: var(--primary);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        a:hover {
            color: var(--primary-hover);
        }
        
        .border-top {
            border-color: var(--border) !important;
        }
        
        footer {
            background: var(--bg-darker);
            border-top: 2px solid var(--border);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-lightning-charge-fill"></i> GYMBRO
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if (isLoggedIn()): ?>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/feed">
                                <i class="bi bi-house-door"></i> Feed
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/queue">
                                <i class="bi bi-fire"></i> Find Partners
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/matches">
                                <i class="bi bi-people"></i> Matches
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/profile">
                                <i class="bi bi-person-circle"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/logout">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    </ul>
                <?php else: ?>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/signup">Sign Up</a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="container">
        <?php
        $flash = getFlash();
        if ($flash):
        ?>
        <div class="alert alert-<?= escape($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= escape($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
