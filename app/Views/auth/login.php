<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator | Pemuda MTA Perwakilan Sragen</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Auth Stylesheet -->
    <link rel="stylesheet" href="<?= base_url('css/auth.css') ?>">
</head>
<body class="auth-body">
    <div class="container p-3">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-9 col-md-7 col-lg-5 col-xl-4">
                <div class="login-card shadow-lg">
                    <div class="login-header text-center">
                        <div class="brand-icon mx-auto mb-2">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h5 class="fw-bold mb-1 text-white">Pemuda MTA Perwakilan Sragen</h5>
                        <p class="mb-0 text-white-50 small">Portal Administrator &amp; Pengurus</p>
                    </div>
                    
                    <div class="p-4 p-md-5">
                        <h5 class="fw-bold text-dark mb-4 text-center">Masuk ke Dashboard</h5>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger d-flex align-items-center mb-4 rounded-3" role="alert">
                                <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                                <div><?= esc(session()->getFlashdata('error')) ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success d-flex align-items-center mb-4 rounded-3" role="alert">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                <div><?= esc(session()->getFlashdata('success')) ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger mb-4 rounded-3">
                                <ul class="mb-0 ps-3">
                                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                                        <li><?= esc($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('admin/login') ?>" method="POST" autocomplete="off">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="login" class="form-label small fw-semibold text-secondary">Username atau Email</label>
                                <div class="input-group">
                                    <span class="input-group-text auth-input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" 
                                           class="form-control auth-form-control" 
                                           id="login" 
                                           name="login" 
                                           value="<?= old('login', 'superadmin') ?>" 
                                           placeholder="Masukkan username atau email" 
                                           required 
                                           autofocus>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label small fw-semibold text-secondary">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text auth-input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" 
                                           class="form-control auth-form-control" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Masukkan password" 
                                           required>
                                    <button class="btn btn-outline-secondary auth-toggle-password" 
                                            type="button" 
                                            id="togglePassword">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn auth-btn-primary w-100 mb-3 shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sekarang
                            </button>
                        </form>

                        <div class="demo-credentials mt-4">
                            <div class="fw-bold text-dark mb-1"><i class="bi bi-info-circle me-1 text-primary"></i> Info Akun Superadmin:</div>
                            <div class="text-secondary">Username: <code class="text-primary fw-semibold">superadmin</code></div>
                            <div class="text-secondary">Password: <code class="text-primary fw-semibold">admin123</code></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top small">
                            <a href="<?= base_url('/') ?>" class="text-decoration-none text-muted">
                                <i class="bi bi-house-door me-1"></i> Beranda
                            </a>
                            <a href="<?= base_url('pendataan') ?>" class="text-decoration-none text-danger fw-semibold">
                                <i class="bi bi-ui-checks me-1"></i> Form Pendataan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            if (type === 'password') {
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            } else {
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            }
        });
    </script>
</body>
</html>
