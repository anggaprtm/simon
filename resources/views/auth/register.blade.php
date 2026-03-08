<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Register &mdash; {{ config('app.name') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        body, #app, .section { 
            font-family: 'Plus Jakarta Sans', sans-serif !important; 
        }

        /* Styling Input Form & Select */
        .form-control {
            border-radius: 0.75rem !important; /* rounded-xl */
            padding: 0.75rem 1rem !important;
            font-size: 0.95rem;
            border: 1.5px solid #e5e7eb;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
            height: auto !important;
        }
        select.form-control {
            appearance: auto; /* Memastikan icon dropdown muncul rapi */
        }
        .form-control:focus {
            border-color: #6366f1 !important; /* Indigo 500 */
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
        }
        .form-control.is-invalid {
            border-color: #ef4444 !important; /* Red 500 */
            background-image: none !important;
        }

        /* Styling Label */
        label {
            font-weight: 600 !important;
            color: #374151 !important; /* Gray 700 */
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        /* Styling Button (Override btn-success menjadi warna Indigo Tema Aplikasi) */
        .btn-success {
            background-color: #4f46e5 !important; /* Indigo 600 */
            border-color: #4f46e5 !important;
            border-radius: 0.5rem !important; /* rounded-lg */
            font-weight: 700 !important;
            padding: 0.6rem 1.5rem !important;
            font-size: 0.95rem !important;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .btn-success:hover {
            background-color: #4338ca !important; /* Indigo 700 */
            transform: translateY(-1px);
        }

        /* Styling Alert Sukses */
        .alert-success {
            background-color: #ecfdf5 !important;
            color: #065f46 !important;
            border: 1px solid #a7f3d0 !important;
            border-radius: 0.75rem !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.75rem 1rem;
        }

        /* Styling Error Message */
        .invalid-feedback strong {
            color: #ef4444 !important;
            font-weight: 500;
            font-size: 0.85rem;
        }

        /* Styling Link Bawah */
        .mt-4.text-center a {
            color: #4f46e5;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
            font-size: 0.9rem;
        }
        .mt-4.text-center a:hover {
            color: #4338ca;
            text-decoration: underline;
        }

        /* Penyesuaian container form agar terpusat secara vertikal di desktop */
        .col-lg-4 .p-4.m-3 {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 100vh;
            margin: 0 !important;
            padding: 2.5rem !important;
        }
    </style>
</head>

<body>
    <div id="app">
        <section class="section">
            <div class="d-flex flex-wrap align-items-stretch">
                <div class="col-lg-4 col-md-6 col-12 order-lg-1 min-vh-100 order-2 bg-white">
                    <div class="p-4 m-3">
                        <div class="text-center mb-5">
                            <a href="/">
                                <img src="{{ asset('images/logo-app.png') }}" alt="Logo FTMM" style="height: 85px;">
                            </a>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success mb-4">{{ session('status') }}</div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate="">
                            @csrf

                            <div class="form-group mb-4">
                                <label for="name">Nama Lengkap</label>
                                <input id="name" type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback d-block mt-2" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="email">Email</label>
                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="invalid-feedback d-block mt-2" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="id_program_studi">Unit/Program Studi</label>
                                <select id="id_program_studi" name="id_program_studi"
                                    class="form-control @error('id_program_studi') is-invalid @enderror" required>
                                    <option value="">-- Pilih Unit --</option>
                                    @foreach ($programStudis as $prodi)
                                        <option value="{{ $prodi->id }}" {{ old('id_program_studi') == $prodi->id ? 'selected' : '' }}>
                                            {{ $prodi->nama_program_studi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_program_studi')
                                    <span class="invalid-feedback d-block mt-2" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="password">Password</label>
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password" required>
                                @error('password')
                                    <span class="invalid-feedback d-block mt-2" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="password_confirmation">Konfirmasi Password</label>
                                <input id="password_confirmation" type="password"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    name="password_confirmation" required>
                                @error('password_confirmation')
                                    <span class="invalid-feedback d-block mt-2" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group text-right mt-5">
                                <button type="submit" class="btn btn-success btn-lg btn-block w-100">
                                    Daftar Akun Baru
                                </button>
                            </div>
                        </form>

                        <div class="mt-4 text-center">
                            <a href="{{ route('login') }}" class="d-block">Sudah punya akun? Login di sini</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 col-12 order-lg-2 order-1 min-vh-100 background-walk-y position-relative overlay-gradient-bottom"
                    style="background-image: url('{{ asset('assets/img/unsplash/login-bgs.jpg') }}');">
                    <div class="absolute-bottom-left index-2">
                        <div class="text-light p-5 pb-2">
                            <div class="mb-5 pb-3">
                                <h1 class="mb-2 display-4 font-weight-bold" style="font-family: 'Plus Jakarta Sans', sans-serif;" id="greetings"></h1>
                                <h5 class="font-weight-normal text-muted-transparent">FTMM, Gedung Nano</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>

    @include('layouts.partials.greetings')
    <script>
        $(document).ready(function() {
            $("#greetings").html(greetings());
        });
    </script>
</body>
</html>