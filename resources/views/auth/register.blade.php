<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
	<title>Register &mdash; {{ config('app.name') }}</title>

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>
	<div id="app">
		<section class="section">
			<div class="d-flex flex-wrap align-items-stretch">
				<!-- Form Register -->
				<div class="col-lg-4 col-md-6 col-12 order-lg-1 min-vh-100 order-2 bg-white">
					<div class="p-4 m-3">
						<!-- Logo -->
						<div class="text-center mb-4">
							<a href="/">
								<img src="{{ asset('images/logo-app.png') }}" alt="Logo FTMM" style="height: 85px;">
							</a>
						</div>

						<!-- Alert jika ada session -->
						@if (session('status'))
							<div class="alert alert-success">{{ session('status') }}</div>
						@endif

						<!-- Form -->
						<form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate="">
							@csrf

							<!-- Nama -->
							<div class="form-group">
								<label for="name">Nama</label>
								<input id="name" type="text"
									class="form-control @error('name') is-invalid @enderror"
									name="name" value="{{ old('name') }}" required autofocus>
								@error('name')
									<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
								@enderror
							</div>

							<!-- Email -->
							<div class="form-group">
								<label for="email">Email</label>
								<input id="email" type="email"
									class="form-control @error('email') is-invalid @enderror"
									name="email" value="{{ old('email') }}" required>
								@error('email')
									<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
								@enderror
							</div>

							<!-- Program Studi -->
							<div class="form-group">
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
									<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
								@enderror
							</div>

							<!-- Password -->
							<div class="form-group">
								<label for="password">Password</label>
								<input id="password" type="password"
									class="form-control @error('password') is-invalid @enderror"
									name="password" required>
								@error('password')
									<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
								@enderror
							</div>

							<!-- Konfirmasi Password -->
							<div class="form-group">
								<label for="password_confirmation">Konfirmasi Password</label>
								<input id="password_confirmation" type="password"
									class="form-control @error('password_confirmation') is-invalid @enderror"
									name="password_confirmation" required>
								@error('password_confirmation')
									<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
								@enderror
							</div>

							<!-- Tombol Daftar -->
							<div class="form-group text-right">
								<button type="submit" class="btn btn-success btn-lg btn-icon icon-right">
									Daftar
								</button>
							</div>
						</form>

						<div class="mt-3 text-center">
							<a href="{{ route('login') }}">Sudah punya akun? Login di sini</a>
						</div>
					</div>
				</div>

				<!-- Background Kanan -->
				<div class="col-lg-8 col-12 order-lg-2 order-1 min-vh-100 background-walk-y position-relative overlay-gradient-bottom"
					style="background-image: url('{{ asset('assets/img/unsplash/login-bgs.jpg') }}');">
					<div class="absolute-bottom-left index-2">
						<div class="text-light p-5 pb-2">
							<div class="mb-5 pb-3">
								<h1 class="mb-2 display-4 font-weight-bold" id="greetings"></h1>
								<h5 class="font-weight-normal text-muted-transparent">FTMM, Gedung Nano</h5>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>

	<!-- Minimal JS -->
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
