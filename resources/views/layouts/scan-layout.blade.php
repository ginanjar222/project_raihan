<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Guru/Staff - SMA YKBBB LELES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DM Sans', sans-serif;
            background: #ffffff;
            min-height: 100vh;
            color: #4b5563;
        }

        .navbar-modern {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            height: 70px;
            flex-shrink: 0;
            position: relative;
            z-index: 1001;
        }

        .navbar-brand {
            font-family: 'DM Sans', sans-serif;
            font-weight: 700;
            font-size: 1.4rem;
            color: #64b6f7 !important;
        }

        .app-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        .main-content {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow: hidden;
            position: relative;
        }

        .content-wrapper {
            flex: 1;
            padding: 2rem;
            background: #f9f9f9;
            animation: fadeInUp 0.5s ease-out;
            overflow-y: auto;
            overflow-x: hidden;
            height: 100%;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .footer-fixed {
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
            padding: 1rem 2rem;
            text-align: center;
            color: #6b7280;
            font-size: 0.9rem;
            box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.1);
        }

        .navbar-time {
            background: rgba(100, 182, 247, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 6px;
            color: #64b6f7;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .content-wrapper::-webkit-scrollbar {
            width: 4px;
        }

        .content-wrapper::-webkit-scrollbar-track {
            background: rgba(100, 182, 247, 0.1);
            border-radius: 2px;
        }

        .content-wrapper::-webkit-scrollbar-thumb {
            background: rgba(100, 182, 247, 0.3);
            border-radius: 2px;
        }

        .content-wrapper::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 182, 247, 0.5);
        }

        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <nav class="navbar navbar-expand-lg navbar-modern">
            <div class="container-fluid">
                <span class="navbar-brand ms-3">
                    <i class="fas fa-graduation-cap me-2"></i>
                    SMA YKBBB LELES
                </span>
                <div class="ms-auto">
                    <div class="navbar-time">
                        <i class="fas fa-clock me-2"></i>
                        <span id="currentTime"></span>
                    </div>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="content">
                <div class="content-wrapper">
                    @yield('content')
                </div>
            </div>
        </div>

        <footer class="footer-fixed">
            <div>
                <i class="fas fa-heart text-primary me-1" style="color: #64b6f7 !important;"></i>
                Dibuat dengan penuh dedikasi untuk SMA YKBBB LELES. | &copy;Copyright DKN28 {{ date('Y') }} All Rights Reserved.
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('currentTime').textContent = timeString;
        }
        
        setInterval(updateTime, 1000);
        updateTime();
    </script>
    @yield('scripts')
</body>
</html>
