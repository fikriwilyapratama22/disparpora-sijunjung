<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup | Disparpora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

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
            background-image: url('/assets/images/auth/01.png'); /* Path gambar Anda */
            background-size: cover; /* Gambar menutupi seluruh area */
            background-position: center; /* Posisi gambar tetap di tengah */
            border-top-right-radius: 25px;
            border-bottom-right-radius: 25px;
            height: 100vh; /* Memastikan gambar mengisi seluruh tinggi layar */
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
                <p class="mb-4 text-muted">Daftar Untuk Mengakses Layanan</p>

                {{-- Menampilkan error validasi --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('signup') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" placeholder="Masukkan Nama"
                            value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan Email"
                            value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3 position-relative">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="password"
                            placeholder="Masukkan Password" required>
                        <span class="position-absolute top-50 end-0 translate-middle-y me-3 mt-3" onclick="togglePassword('password', this)"
                            style="cursor: pointer;">
                            <i class="bi bi-eye-slash" id="togglePasswordIcon1"></i>
                        </span>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control"
                            id="password_confirmation" placeholder="Ulangi Password" required>
                        <span class="position-absolute top-50 end-0 translate-middle-y me-3 mt-3"
                            onclick="togglePassword('password_confirmation', this)" style="cursor: pointer;">
                            <i class="bi bi-eye-slash" id="togglePasswordIcon2"></i>
                        </span>
                    </div>
                    <script>
                        function togglePassword(fieldId, element) {
                            const input = document.getElementById(fieldId);
                            const icon = element.querySelector('i');

                            if (input.type === "password") {
                                input.type = "text";
                                icon.classList.remove("bi-eye-slash");
                                icon.classList.add("bi-eye");
                            } else {
                                input.type = "password";
                                icon.classList.remove("bi-eye");
                                icon.classList.add("bi-eye-slash");
                            }
                        }
                    </script>


                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-success">Sign Up</button>
                    </div>

                    <div class="text-center mt-3">
                        <small>Sudah punya akun? <a href="{{ route('client.auth.login') }}"
                                class="text-success fw-bold">Login</a></small>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Design -->
        <div class="right-box d-none d-md-block"></div>
    </div>

</body>

</html>
