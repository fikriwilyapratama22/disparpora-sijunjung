@extends('client.layouts.app')

@section('content')
    <!-- Message Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5 justify-content-center">  <!-- Centering the content -->
                <!-- Bagian Kiri: Informasi dan Alert -->
                <div class="col-lg-7 text-center">
                    <div class="d-flex align-items-start justify-content-center">
                        <img src="{{ asset('assets/images/chat.png') }}" alt="Contact Illustration"
                            class="img-fluid me-3" style="width: 100px; height: 100px;">
                        <div>
                            <h1 class="mb-4">Kirim saran atau masukan anda kepada kami</h1>
                            <p>Dinas Pariwisata Pemuda Dan Olahraga Kabupaten Sijunjung</p>

                            <!-- Tampilkan pesan sukses setelah verifikasi -->
                            @if (session('status'))
                                <div id="successAlert" class="alert alert-success text-center w-100 mb-4">
                                    <h5>{{ session('status') }}</h5>
                                </div>
                            @endif

                            <!-- Loading saat menunggu verifikasi -->
                            <div id="loadingSpinner" class="text-center w-100 mb-4 d-none">
                                <p>Sedang memproses pesan...</p>
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                  <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
    <h5 class="fw-bold text-primary text-uppercase">Buku Tamu</h5>
</div>

<div class="d-flex justify-content-center">
    <div class="bg-primary rounded-4 p-5 shadow-lg wow zoomIn" data-wow-delay="0.3s" style="width: 100%; max-width: 600px;">
        <form id="contactForm" action="{{ route('buku-tamu.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label text-white fw-semibold">Nama Anda</label>
                <input type="text" id="name" name="nama" class="form-control rounded" placeholder="Nama Anda" required>
                <div class="invalid-feedback">Nama hanya boleh mengandung huruf dan spasi.</div>
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label text-white fw-semibold">Alamat</label>
                <input type="text" id="alamat" name="alamat" class="form-control rounded" placeholder="Alamat Anda" required>
                <div class="invalid-feedback">Masukkan alamat yang valid.</div>
            </div>

            <div class="mb-3">
                <label for="telp" class="form-label text-white fw-semibold">Nomor HP</label>
                <input type="text" id="telp" name="no_telfon" class="form-control rounded" placeholder="08xxxxxxxxxx" required>
                <div class="invalid-feedback">Nomor HP tidak valid.</div>
            </div>
            <div class="mb-3">
                
    <label for="lokawisata_id" class="form-label text-white fw-semibold">Pilih Lokawisata</label>
    <select id="lokawisata_id" name="lokawisata_id" class="form-control rounded" required>
        <option value="">-- Pilih Lokawisata --</option>
        @foreach ($lokawisata as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </select>
    <div class="invalid-feedback">Silakan pilih salah satu lokawisata.</div>
</div>


            <div class="mb-3">
                <label for="pesan" class="form-label text-white fw-semibold">Pesan</label>
                <textarea id="pesan" name="pesan" class="form-control rounded" rows="4" placeholder="Pesan Anda..." required></textarea>
                <div class="invalid-feedback">Pesan tidak boleh kosong.</div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-light fw-bold rounded py-2">Kirim Pesan</button>
            </div>
        </form>
    </div>
</div>

            </div>
        </div>
    </div>
    <!-- Message End -->

    <!-- jQuery and AJAX script for form handling -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            let uploadedFilePath = ''; // Variable untuk menyimpan path file sementara

            // Handle file upload (jika diperlukan)
            $('#file').on('change', function() {
                var file = $(this).prop('files')[0];
                if (!file || !file.type.match('image.*')) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "File yang diunggah bukan gambar yang valid. Silakan unggah file dalam format JPG, JPEG, atau PNG.",
                        confirmButtonColor: '#3A57E8',
                    });
                    $(this).val('');
                    $('#image-preview').attr('src', '{{ asset('/uploads/noimage.jpg') }}');
                } else {
                    $('#invalid-picture').hide();

                    var oFReader = new FileReader();
                    oFReader.readAsDataURL(file);
                    oFReader.onload = function(oFREvent) {
                        $('#image-preview').attr('src', oFREvent.target.result);
                    };

                    var formdata = new FormData();
                    formdata.append("file", file);

                    // Tampilkan loading
                    Swal.fire({
                        title: 'Mengunggah...',
                        html: 'Tunggu sebentar, gambar sedang diunggah',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '/api/upload', // Endpoint untuk mengunggah gambar
                        type: "POST",
                        data: formdata,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            Swal.close(); // Tutup dialog loading
                            if (result['success'] == true) {
                                uploadedFilePath = result['data']['url'].replace('/xxx/', '/500/');
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil",
                                    text: "Gambar berhasil diunggah. Tekan tombol Simpan untuk menyimpan semua data.",
                                    confirmButtonColor: '#3A57E8',
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Gagal mengunggah gambar.",
                                    confirmButtonColor: '#3A57E8',
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: "Terjadi kesalahan saat mengunggah gambar.",
                                confirmButtonColor: '#3A57E8',
                            });
                            $('#file').val('');
                            $('#image-preview').attr('src', '{{ asset('/uploads/noimage.jpg') }}');
                        }
                    });
                }
            });

            // Fungsi Validasi
            function validateName(name) {
                const regex = /^[a-zA-Z\s]+$/;
                return regex.test(name); // Hanya huruf dan spasi yang diperbolehkan
            }

            function validateMessage(message) {
                return message.trim().length > 0; // Tidak boleh kosong
            }

            // Handle form submission with confirmation
            $('#contactForm').on('submit', function(e) {
                e.preventDefault(); // Mencegah submit form standar

                const name = $('#name').val();
                const message = $('#pesan').val();

                // Validasi data
                if (!validateName(name)) {
                    Swal.fire({
                        icon: "error",
                        title: "Nama tidak valid",
                        text: "Nama hanya boleh mengandung huruf dan spasi.",
                        confirmButtonColor: '#3A57E8',
                    });
                    return;
                }

                if (!validateMessage(message)) {
                    Swal.fire({
                        icon: "error",
                        title: "Pesan tidak valid",
                        text: "Pesan tidak boleh kosong.",
                        confirmButtonColor: '#3A57E8',
                    });
                    return;
                }

                // Jika semua validasi lolos, tampilkan konfirmasi
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Pastikan Anda sudah memeriksa semua data. Tidak ada perubahan lagi?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3A57E8',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, kirim sekarang!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData($('#contactForm')[0]);

                        // Sertakan path gambar yang telah diunggah
                        if (uploadedFilePath !== '') {
                            formData.set('file_path', uploadedFilePath);
                        } else {
                            formData.set('file_path', 'noimage.jpg'); // Gambar default jika tidak ada
                        }

                        // Tampilkan loading spinner saat memproses
                        $('#loadingSpinner').removeClass('d-none');

                        $.ajax({
                            url: "{{ route('buku-tamu.store') }}", // URL untuk mengirim pesan
                            method: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $('#loadingSpinner').addClass('d-none');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Pesan Anda telah dikirim. Terima kasih atas masukannya.',
                                    confirmButtonColor: '#3A57E8',
                                });

                                // Reset form setelah berhasil mengirim
                                $('#contactForm')[0].reset();
                                $('#image-preview').attr('src', '{{ asset('/uploads/noimage.jpg') }}');
                            },
                            error: function(xhr) {
                                $('#loadingSpinner').addClass('d-none');
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Terjadi kesalahan, silahkan periksa dan lengkapi form.",
                                    confirmButtonColor: '#3A57E8',
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
