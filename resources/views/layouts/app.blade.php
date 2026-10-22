<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Added CSRF token meta tag for AJAX requests -->
    <title>Presensi Guru/Staff - SMA YKBBB LELES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Menggunakan CDN Tailwind CSS untuk menghindari error Vite -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['DM Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        'sidebar': '#64b6f7',
                        'sidebar-foreground': '#ffffff',
                        'elegant-white': '#ffffff',
                        'elegant-gray': '#4b5563',
                        'card': '#f9f9f9',
                        'border-elegant': '#e5e7eb',
                        'primary': '#64b6f7',
                        'secondary': '#007acc',
                    }
                }
            }
        }
    </script>
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

        .toggle-btn {
            background: #64b6f7;
            border: none;
            color: white;
            padding: 10px 14px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .toggle-btn:hover {
            background: #007acc;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(100, 182, 247, 0.3);
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

        .sidebar {
            width: 280px;
            min-width: 280px;
            background: #64b6f7;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 2px 0 15px rgba(100, 182, 247, 0.15);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .sidebar.collapsed {
            width: 0;
            min-width: 0;
            box-shadow: none;
            transform: translateX(-100%);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            white-space: nowrap;
            flex-shrink: 0;
            background: rgba(0, 122, 204, 0.1);
        }

        .sidebar-header h5 {
            font-family: 'DM Sans', sans-serif;
            color: #ffffff;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: #ffffff;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-weight: 500;
            white-space: nowrap;
            font-size: 0.95rem;
            margin: 0.2rem 0;
        }

        .sidebar-menu a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-left-color: #ffffff;
            transform: translateX(4px);
        }

        .sidebar-menu a.active {
            background: rgba(0, 122, 204, 0.3);
            color: #ffffff;
            border-left-color: #ffffff;
            font-weight: 600;
        }

        .sidebar-menu a i {
            margin-right: 12px;
            width: 18px;
            text-align: center;
            font-size: 1rem;
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

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                height: calc(100vh - 70px);
                z-index: 1000;
                top: 70px;
                left: 0;
                width: 280px;
                min-width: 280px;
                transform: translateX(-100%);
            }
            
            .sidebar:not(.collapsed) {
                transform: translateX(0);
            }
            
            .sidebar.collapsed {
                transform: translateX(-100%);
                width: 280px;
                min-width: 280px;
            }
            
            .content-wrapper {
                padding: 1.5rem;
            }
            
            .sidebar:not(.collapsed)::after {
                content: '';
                position: fixed;
                top: 0;
                left: 280px;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.3);
                z-index: -1;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .content-wrapper {
                padding: 1rem;
            }
            
            .footer-fixed {
                padding: 0.8rem;
                font-size: 0.85rem;
            }
        }

        .content-wrapper::-webkit-scrollbar,
        .sidebar-menu::-webkit-scrollbar {
            width: 4px;
        }

        .content-wrapper::-webkit-scrollbar-track,
        .sidebar-menu::-webkit-scrollbar-track {
            background: rgba(100, 182, 247, 0.1);
            border-radius: 2px;
        }

        .content-wrapper::-webkit-scrollbar-thumb,
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(100, 182, 247, 0.3);
            border-radius: 2px;
        }

        .content-wrapper::-webkit-scrollbar-thumb:hover,
        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 182, 247, 0.5);
        }

        html {
            scroll-behavior: smooth;
        }

        .navbar-time {
            background: rgba(100, 182, 247, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 6px;
            color: #64b6f7;
            font-weight: 500;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <nav class="navbar navbar-expand-lg navbar-modern">
            <div class="container-fluid">
                <button class="toggle-btn" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="navbar-brand ms-3">
                    <i class="fas fa-graduation-cap me-2"></i>
                    SMA YKBBB LELES
                </span>
                <div class="ms-auto">
                    <div class="d-flex align-items-center gap-3">
                        <div class="navbar-time">
                            <i class="fas fa-clock me-2"></i>
                            <span id="currentTime"></span>
                        </div>
                        <!-- Menambahkan tombol logout untuk admin -->
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: #64b6f7; color: #64b6f7;">
                                <i class="fas fa-user-circle me-2"></i>
                                {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                        <i class="fas fa-user-cog me-2"></i>
                                        Akun
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <nav id="sidebar" class="sidebar">
                <div class="sidebar-header">
                    <h5><i class="fas fa-list me-2"></i>Menu Utama</h5>
                </div>
                <div class="sidebar-menu">
                    <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                    {{-- Menghapus menu "Scan Kartu" dari sidebar karena hanya untuk halaman awal --}}
                    <a href="{{ url('/absensi/manual') }}" class="{{ request()->is('absensi/manual*') ? 'active' : '' }}">
                        <i class="fas fa-edit"></i>
                        Input Manual
                    </a>
                    <a href="{{ url('/absensi/list') }}" class="{{ request()->is('absensi/list*') || request()->is('absensi/*/edit-keterangan') ? 'active' : '' }}">
                        <i class="fas fa-list-alt"></i>
                        Daftar Presensi
                    </a>
                    <a href="{{ url('/absensi/evidence-list') }}" class="{{ request()->is('absensi/evidence-list*') ? 'active' : '' }}">
                        <i class="fas fa-file-image"></i>
                        Kelola Bukti
                    </a>
                    <a href="{{ url('/guru') }}" class="{{ request()->is('guru*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        Data Guru
                    </a>
                    <a href="{{ url('/rekap/mingguan') }}" class="{{ request()->is('rekap/mingguan') ? 'active' : '' }}">
                        <i class="fas fa-calendar-week"></i>
                        Rekap Mingguan
                    </a>
                    <a href="{{ url('/rekap/bulanan') }}" class="{{ request()->is('rekap/bulanan') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        Rekap Bulanan
                    </a>
                </div>
            </nav>

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

    <!-- Modal Ganti Password -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">
                        <i class="fas fa-key me-2"></i>Ganti Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="changePasswordForm" method="POST" action="{{ route('change-password') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Lama</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye" id="current_password_icon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="current_password_error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                    <i class="fas fa-eye" id="new_password_icon"></i>
                                </button>
                            </div>
                            <div class="form-text">Password minimal 6 karakter</div>
                            <div class="invalid-feedback" id="new_password_error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check"></i></span>
                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                    <i class="fas fa-eye" id="new_password_confirmation_icon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="new_password_confirmation_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const isCollapsed = sidebar.classList.contains('collapsed');
            
            if (isCollapsed) {
                sidebar.classList.remove('collapsed');
                this.innerHTML = '<i class="fas fa-times"></i>';
            } else {
                sidebar.classList.add('collapsed');
                this.innerHTML = '<i class="fas fa-bars"></i>';
            }
        });

        function handleResize() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');
            
            if (window.innerWidth <= 768) {
                sidebar.classList.add('collapsed');
                toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            } else {
                sidebar.classList.remove('collapsed');
                toggleBtn.innerHTML = '<i class="fas fa-times"></i>';
            }
        }

        handleResize();
        window.addEventListener('resize', handleResize);

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

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '_icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Validasi konfirmasi password
        document.getElementById('new_password_confirmation').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            const errorDiv = document.getElementById('new_password_confirmation_error');
            
            if (confirmPassword && newPassword !== confirmPassword) {
                this.classList.add('is-invalid');
                errorDiv.textContent = 'Konfirmasi password tidak sesuai';
                errorDiv.style.display = 'block';
            } else {
                this.classList.remove('is-invalid');
                errorDiv.style.display = 'none';
            }
        });

        // Handle form submission
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
            
            // Clear previous errors
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const modal = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
                    modal.hide();
                    
                    // Reset form
                    this.reset();
                    
                    // Show success alert
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
                    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                    alertDiv.innerHTML = `<i class="fas fa-check-circle me-2"></i>Password berhasil diubah!<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                    document.body.appendChild(alertDiv);
                    
                    // Auto remove alert after 3 seconds
                    setTimeout(() => {
                        if (alertDiv.parentNode) {
                            alertDiv.remove();
                        }
                    }, 3000);
                } else {
                    // Show validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const input = document.getElementById(field);
                            const errorDiv = document.getElementById(field + '_error');
                            if (input && errorDiv) {
                                input.classList.add('is-invalid');
                                errorDiv.textContent = data.errors[field][0];
                                errorDiv.style.display = 'block';
                            }
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });

        class MenuKeyboardShortcutHandler {
            constructor() {
                this.keySequence = []
                this.requiredSequence = ['Control', 'c', 'b']
                this.timeout = null
                this.isNavigating = false
                this.lastKeyTime = 0
                
                document.addEventListener('keydown', (e) => this.handleKeyDown(e), true)
                document.addEventListener('keyup', (e) => this.handleKeyUp(e), true)
                document.addEventListener('visibilitychange', () => this.handleVisibilityChange())
                
                // Deteksi navigasi dengan mendengarkan click event
                document.addEventListener('click', () => {
                    this.isNavigating = true
                    setTimeout(() => {
                        this.isNavigating = false
                    }, 500)
                })
            }
            
            handleKeyDown(e) {
                if (this.isNavigating || this.isInputFocused()) {
                    this.keySequence = []
                    return
                }
                
                const currentTime = Date.now()
                
                // Reset jika lebih dari 3 detik sejak key terakhir
                if (currentTime - this.lastKeyTime > 3000) {
                    this.keySequence = []
                }
                
                this.lastKeyTime = currentTime
                
                if (e.key === 'Control') {
                    this.keySequence = ['Control']
                    clearTimeout(this.timeout)
                    this.timeout = setTimeout(() => {
                        this.keySequence = []
                    }, 3000)
                    return
                }
                
                if (this.keySequence.length > 0 && this.keySequence[0] === 'Control') {
                    const key = e.key.toLowerCase()
                    
                    // Jangan tambahkan jika key yang sama diulang
                    if (this.keySequence.length > 1 && this.keySequence[this.keySequence.length - 1] === key) {
                        return
                    }
                    
                    this.keySequence.push(key)
                    
                    if (this.keySequence.length === this.requiredSequence.length) {
                        if (this.keySequence[0] === 'Control' && 
                            this.keySequence[1] === 'c' && 
                            this.keySequence[2] === 'b') {
                            console.log('[v0] Kombinasi Ctrl+C+B terdeteksi di halaman menu, logout dan kembali ke scan...')
                            this.logoutAndBackToScan()
                        }
                        this.keySequence = []
                    }
                }
                
                if (e.key.toLowerCase() === 'p' && !this.isInputFocused() && !e.ctrlKey && !e.altKey && !e.metaKey) {
                    e.preventDefault()
                    this.toggleFullscreen()
                }
            }
            
            handleKeyUp(e) {
                if (e.key === 'Control') {
                    clearTimeout(this.timeout)
                    this.timeout = setTimeout(() => {
                        this.keySequence = []
                    }, 1000)
                }
            }
            
            isInputFocused() {
                const activeElement = document.activeElement
                return activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA' || activeElement.tagName === 'SELECT'
            }
            
            toggleFullscreen() {
                const elem = document.documentElement
                
                if (!document.fullscreenElement) {
                    if (elem.requestFullscreen) {
                        elem.requestFullscreen()
                    } else if (elem.webkitRequestFullscreen) {
                        elem.webkitRequestFullscreen()
                    } else if (elem.mozRequestFullScreen) {
                        elem.mozRequestFullScreen()
                    } else if (elem.msRequestFullscreen) {
                        elem.msRequestFullscreen()
                    }
                    console.log('[v0] Fullscreen mode activated')
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen()
                    } else if (document.webkitExitFullscreen) {
                        document.webkitExitFullscreen()
                    } else if (document.mozCancelFullScreen) {
                        document.mozCancelFullScreen()
                    } else if (document.msExitFullscreen) {
                        document.msExitFullscreen()
                    }
                    console.log('[v0] Fullscreen mode deactivated')
                }
            }
            
            handleVisibilityChange() {
                if (document.hidden) {
                    console.log('[v0] Tab tidak aktif, logout otomatis...')
                    this.logoutAndBackToScan()
                }
            }
            
            logoutAndBackToScan() {
                this.isNavigating = true
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("logout") }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                form.appendChild(csrfToken);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        window.menuKeyboardShortcutHandler = new MenuKeyboardShortcutHandler()
    </script>
    @yield('scripts')
</body>
</html>
