<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SMA YKBBB LELES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, #64b6f7 0%, #007acc 50%, #0056b3 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        /* Menambahkan background pattern untuk tema sekolah */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255,255,255,0.05) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 
                0 32px 64px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
            padding: 0;
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, #64b6f7 0%, #007acc 100%);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
        }
        
        /* Mengubah header untuk menampilkan nama sekolah yang jelas */
        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        
        .school-info {
            position: relative;
            z-index: 2;
        }
        
        .logo-container {
            margin-bottom: 1.5rem;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo-placeholder {
            width: 90px;
            height: 90px;
            background: rgba(255,255,255,0.15);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            border: 2px dashed rgba(255,255,255,0.4);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .logo-placeholder:hover {
            background: rgba(255,255,255,0.2);
            transform: scale(1.05);
        }
        
        .logo-img {
            max-width: 90px;
            max-height: 90px;
            width: auto;
            height: auto;
            object-fit: contain;
            border-radius: 16px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        
        .school-name {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }
        
        .school-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            font-weight: 500;
        }
        
        .login-body {
            padding: 2.5rem 2rem;
            background: white;
        }
        
        .system-title {
            text-align: center;
            margin-bottom: 2rem;
            color: #64b6f7;
        }
        
        .system-title h5 {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #007acc;
        }
        
        .system-title p {
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .form-floating {
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.2rem 1rem;
            transition: all 0.3s ease;
            font-size: 1rem;
            background: #f9fafb;
        }
        
        .form-control:focus {
            border-color: #64b6f7;
            box-shadow: 0 0 0 0.2rem rgba(100, 182, 247, 0.15);
            background: white;
            transform: translateY(-1px);
        }
        
        .form-floating label {
            color: #6b7280;
            font-weight: 500;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #64b6f7 0%, #007acc 100%);
            border: none;
            border-radius: 12px;
            padding: 1.2rem;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(100, 182, 247, 0.4);
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }
        
        .invalid-feedback {
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .security-note {
            background: rgba(100, 182, 247, 0.1);
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            margin-top: 1.5rem;
            border: 1px solid rgba(100, 182, 247, 0.2);
        }
        
        .security-note small {
            color: #64b6f7;
            font-weight: 500;
        }
        
        @media (max-width: 576px) {
            body {
                padding: 1rem;
            }
            
            .login-container {
                margin: 0;
                max-width: 100%;
                border-radius: 20px;
            }
            
            .login-header {
                padding: 2rem 1.5rem;
            }
            
            .login-body {
                padding: 2rem 1.5rem;
            }
            
            .logo-container {
                height: 80px;
                margin-bottom: 1rem;
            }
            
            .logo-placeholder,
            .logo-img {
                width: 70px;
                height: 70px;
                max-width: 70px;
                max-height: 70px;
            }
            
            .logo-placeholder {
                font-size: 2rem;
            }
            
            .school-name {
                font-size: 1.2rem;
            }
            
            .school-subtitle {
                font-size: 0.85rem;
            }
        }
        
        @media (max-width: 400px) {
            .school-name {
                font-size: 1.1rem;
            }
            
            .login-header {
                padding: 1.5rem 1rem;
            }
            
            .login-body {
                padding: 1.5rem 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="school-info">
                <div class="logo-container">
                    <!-- Logo sekolah akan ditambahkan di sini dengan ukuran yang responsive -->
                    <img src="{{ asset('images/logo-ykbbb.png') }}" 
                         alt="Logo SMA YKBBB LELES" 
                         class="logo-img"
                         onload="this.nextElementSibling.style.display='none';"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <!-- Fallback jika logo tidak ditemukan -->
                    <div class="logo-placeholder" style="display: none;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
                <h4 class="school-name">SMA YKBBB LELES</h4>
                <p class="school-subtitle mb-0">Sistem Absensi Guru & Staff</p>
            </div>
        </div>
        
        <div class="login-body">
            <div class="system-title">
                <h5><i class="fas fa-user-shield me-2"></i>Login Administrator</h5>
                <p>Masuk ke sistem manajemen absensi</p>
            </div>
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-floating">
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           placeholder="Email Administrator"
                           value="{{ old('email') }}"
                           required>
                    <label for="email">
                        <i class="fas fa-envelope me-2"></i>Email Administrator
                    </label>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-floating">
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           placeholder="Password"
                           required>
                    <label for="password">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Masuk ke Sistem
                </button>
            </form>
            
            <div class="security-note">
                <small>
                    <i class="fas fa-shield-alt me-1"></i>
                    Akses terbatas untuk administrator sekolah
                </small>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
