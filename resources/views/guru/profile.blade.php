@extends('layouts.app')

@section('title', 'Profil Guru - E-Learning SMK Yadika 13')
@section('page-title', 'Profil Guru')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user-edit me-2"></i>Edit Profil
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('guru.profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $guru->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $guru->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nis_nip" class="form-label">NIP</label>
                            <input type="text" class="form-control @error('nis_nip') is-invalid @enderror"
                                   id="nis_nip" name="nis_nip" value="{{ old('nis_nip', $guru->nis_nip) }}">
                            @error('nis_nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="no_hp" class="form-label">No. Telepon</label>
                            <input type="text" class="form-control @error('no_hp') is-invalid @enderror"
                                   id="no_hp" name="no_hp" value="{{ old('no_hp', $guru->no_hp) }}">
                            @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror"
                                  id="alamat" name="alamat" rows="3">{{ old('alamat', $guru->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Ubah Password</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="current_password" class="form-label">Password Lama</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password" name="current_password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                               id="password_confirmation" name="password_confirmation">
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>Informasi Akun
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="fas fa-user-circle fa-4x text-gray-300"></i>
                </div>

                <div class="mb-2">
                    <strong>Role:</strong> {{ ucfirst($guru->role) }}
                </div>

                <div class="mb-2">
                    <strong>Bergabung sejak:</strong> {{ $guru->created_at->format('d M Y') }}
                </div>

                <div class="mb-2">
                    <strong>Terakhir update:</strong> {{ $guru->updated_at->format('d M Y H:i') }}
                </div>

                <hr>

                <div class="small text-muted">
                    Pastikan informasi profil Anda selalu terupdate untuk kemudahan komunikasi.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
