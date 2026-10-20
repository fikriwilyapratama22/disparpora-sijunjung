<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Disparpora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-container {
            height: 100vh;
            display: flex;
        }

        .left-box {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
        }

        .login-card {
            width: 100%;
            max-width: 350px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background: #fff;
        }

        .right-box {
            flex: 1;
            background-image: url('/assets/images/auth/01.png');
            background-size: cover;
            background-position: center;
            border-top-right-radius: 25px;
            border-bottom-right-radius: 25px;
            height: 100vh;
        }

        .btn-outline-success {
            border-radius: 6px;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <!-- Left Form -->
        <div class="left-box">
            <div class="login-card">
                <h3 class="fw-bold">Selamat Datang!</h3>
                <p class="mb-4 text-muted">Masukkan Akun Untuk Mengakses Layanan</p>

                <!-- Notif Success (jika habis daftar) -->
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Masukkan Email"
                            value="{{ old('email') }}" required>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Masukkan Password" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-success">Login</button>
                    </div>
                    <div class="text-center mt-3">
                        <small>Belum punya akun? <a href="{{ route('client.auth.daftar') }}"
                                class="text-success fw-bold">Daftar</a></small>
                    </div>
                </form>

            </div>
            <div class="text-"></div>
        </div>

        <!-- Right Design -->
        <div class="right-box d-none d-md-block"></div>
    </div>

</body>

</html>
