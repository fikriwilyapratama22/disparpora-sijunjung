@extends('client.layouts.app')

@section('content')
    <div class="container py-4 mt-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-md-9">

    <!-- Breadcrumb -->
    <nav class="mb-3">
        <small>
            <a href="/" class="text-decoration-none text-success fw-semibold">
                <i class="bi bi-house-door"></i> Home
            </a>
            <span class="mx-2">›</span>
            <a href="/layanan" class="text-decoration-none text-success fw-semibold">Layanan</a>
            <span class="mx-2">›</span>
            <span class="fw-bold text-success">{{ $layanan->layanan }}</span>
        </small>
    </nav>

    <!-- Judul & Info -->
    <h2 class="fw-bold  mb-1">{{ $layanan->layanan }}</h2>
    <div class="small mb-4 text-dark">
        <i class="bi bi-building me-1"></i> {{ $layanan->dinas ?? 'Dinas Terkait' }} <br>
        <i class="bi bi-calendar-event me-1"></i>
        {{ \Carbon\Carbon::parse($layanan->created_at)->translatedFormat('l, d F Y, H:i') }} WIB
    </div>

    <!-- Gambar -->
    @if ($layanan->gambar)
        <div class="mb-4 text-center">
            <img src="{{ asset('layanan/' . $layanan->gambar) }}" alt="{{ $layanan->layanan }}"
                 class="img-fluid rounded shadow-sm" style="max-height: 350px; object-fit: cover;">
        </div>
    @endif

    <!-- Tentang -->
    @if ($konten)
        <h4 class="fw-bold mb-3">Tentang {{ $layanan->layanan }}</h4>
        {{-- <p class="text-justify text-dark">{!! nl2br(e($konten->isi1)) !!}</p> --}}
    @endif

    <!-- Accordion -->
    @for ($i = 1; $i <= 3; $i++)
        <div class="accordion mb-3" id="accordion{{ $i }}">
            <div class="accordion-item custom-accordion shadow-sm">
                <h2 class="accordion-header" id="heading{{ $i }}">
                    <button class="accordion-button custom-accordion-button" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $i }}">
                        {{ $konten->{'judul'.$i} ?? 'Belum ada informasi ' . $layanan->layanan }}
                    </button>
                </h2>
                <div id="collapse{{ $i }}" class="accordion-collapse collapse show">
    <div class="accordion-body text-dark text-justify">
        {!! nl2br(e($konten->{'isi'.$i} ?? 'Belum ada informasi.')) !!}
    </div>
</div>

            </div>
        </div>
    @endfor

    <!-- Tombol Ajukan -->
    <div class="mt-4">
        @auth
            <button class="btn btn-success px-4 py-2 rounded-pill shadow-sm text-white"
                    data-bs-toggle="modal" data-bs-target="#pengajuanLayananModal">
                <i class="bi bi-send-check me-1"></i> Ajukan Layanan
            </button>
        @endauth

        @guest
            <a href="{{ route('client.auth.login') }}"
               class="btn btn-outline-success px-4 py-2 rounded-pill shadow-sm">
                <i class="bi bi-box-arrow-in-right me-1"></i> Login untuk Ajukan Layanan
            </a>
        @endguest
    </div>
</div>

<style>
    .custom-accordion {
        border-radius: 12px;
        overflow: hidden;
        border: 1.5px solid #000000;
    }
    .custom-accordion-button {
        background-color: #f8f9fa;
        color: #000000; /* Judul accordion hijau */
        font-weight: 600;
    }
    .custom-accordion-button:focus {
        box-shadow: none;
    }
    .custom-accordion-button:not(.collapsed) {
        background-color: #e8f5e9;
        color: #198754;
    }
    .custom-accordion .accordion-body {
        background-color: #ffffff;
        line-height: 1.7;
        color: #000000; /* Isi konten hitam */
    }
       .text-justify {
        text-align: justify;
    }
</style>


            <!-- Modal Floating Form -->
            <div class="modal fade" id="pengajuanLayananModal" tabindex="-1" aria-labelledby="pengajuanLayananLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content border-3 rounded-5">
                        <div class="modal-header custom-modal-header">
                            <div>
                                <h5 class="modal-title mb-1">Selamat Datang,</h5>
                                <small class="subtitle">Isi Data Untuk Mengisi Pengajuan Layanan</small>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <form action="{{ route('client.pengajuan.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body p-4">
                                <!-- Nama Lengkap -->
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" name="nama" placeholder="Masukkan Nama.."
                                        value="{{ old('nama', $user->name ?? '') }}" required>
                                </div>

                                <!-- Pilihan Layanan -->
                                <div class="mb-3">
                                    <label class="form-label">Pilihan Layanan Publik</label>
                                    <select class="form-select" name="pengajuan_id" required>
                                        <option value="">Pilih Layanan</option>
                                        @foreach ($pengajuan as $l)
                                            <option value="{{ $l->id }}">{{ $l->jenis }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Kontak & Identitas -->
                                <div class="mb-3">
                                    <label class="form-label">No whatsapp</label>
                                    <input type="number" class="form-control" name="no_hp" placeholder="No whatsapp" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">NIK</label>
                                    <input type="number" class="form-control" name="nik" placeholder="NIK" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">alamat</label>
                                    <input type="text" class="form-control" name="alamat" placeholder="Masukkan Tempat.." required>
                                </div>

                                <!-- FIELD TAMBAHAN UNTUK PEMINJAMAN (id = 1) -->
                                <div id="form-peminjaman" style="display: none;">
                                    <div class="mb-3">
                                        <label class="form-label">Jam Mulai</label>
                                        <input type="text" class="form-control clockpicker" name="jam_mulai" placeholder="Pilih jam mulai">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Jam selesai</label>
                                        <input type="text" class="form-control clockpicker" name="jam_selesai" placeholder="Pilih jam selesai">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Mulai</label>
                                        <input type="date" class="form-control" name="tgl_mulai" id="tgl_mulai_1" placeholder="tgl mulai" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Selesai</label>
                                        <input type="date" class="form-control" name="tgl_selesai" id="tgl_selesai_1" placeholder="tgl selesai" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" req>Jenis Barang</label>
                                        <input type="text" class="form-control" name="barang" placeholder="Barang">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Lokasi</label>
                                        <input type="text" class="form-control" name="tempat" placeholder="Lokasi">
                                    </div>
                                </div>

                                <!-- FIELD TAMBAHAN UNTUK KERAMAIAN (id = 3) -->
                                <div id="form-keramaian" style="display: none;">
                                    <div class="mb-3">
                                        <label class="form-label">Jam Mulai</label>
                                        <input type="text" class="form-control clockpicker" name="jam_mulai" placeholder="Pilih jam mulai">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Jam selesai</label>
                                        <input type="text" class="form-control clockpicker" name="jam_selesai" placeholder="Pilih jam selesai">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Mulai</label>
                                        <input type="date" class="form-control" name="tgl_mulai" id="tgl_mulai_2" placeholder="tgl mulai" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Selesai</label>
                                        <input type="date" class="form-control" name="tgl_selesai" id="tgl_selesai_2" placeholder="tgl selesai" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Lokasi</label>
                                        <input type="text" class="form-control" name="tempat" placeholder="Lokasi">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Kegiatan</label>
                                        <input type="text" class="form-control" name="kegiatan" placeholder="Kegiatan">
                                    </div>
                                </div>

                                <!-- FIELD TAMBAHAN UNTUK PENELITIAN (id = 2) -->
                                <div id="form-penelitian" style="display: none;">
                                    <div class="mb-3">
                                        <label class="form-label">NIM</label>
                                        <input type="number" class="form-control" name="nim" placeholder="NIM">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Fakultas</label>
                                        <input type="text" class="form-control" name="fakultas" placeholder="Fakultas">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Universitas</label>
                                        <input type="text" class="form-control" name="universitas" placeholder="Universitas andalas(lengkap)">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Program Studi</label>
                                        <input type="text" class="form-control" name="prodi" placeholder="Program Studi">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tempat Penelitian</label>
                                        <input type="text" class="form-control" name="tmp_penelitian" placeholder="Tempat Penelitian">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Judul Penelitian</label>
                                        <input type="text" class="form-control" name="judul" placeholder="Judul Penelitian">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Mulai</label>
                                        <input type="date" class="form-control" name="tgl_mulai" id="tgl_mulai_3" placeholder="tgl mulai" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Selesai</label>
                                        <input type="date" class="form-control" name="tgl_selesai" id="tgl_selesai_3" placeholder="tgl selesai" required>
                                    </div>
                                </div>

                             <!-- Media Upload -->
<div class="mb-3">
    <label class="form-label">Media Upload</label>
    <div class="border p-3 rounded text-center">
        <p class="mb-1">Add your documents here, and you can upload up to 5 files max</p>
        <input type="file" class="form-control" id="fileUpload" name="file[]" multiple
            accept=".jpg,.png,.svg,.zip,.pdf,.docx" />
        <small class="text-muted d-block mt-1">Max 5 MB per file | PPT files are not allowed</small>
        <div id="fileWarning" class="text-danger mt-2" style="display:none;"></div>
    </div>
    {{-- <div class="text-center my-2">OR</div>
    <div class="input-group">
        <input type="url" name="file_url" class="form-control"
            placeholder="https://sharefile.xyz/file.jpg">
        <button type="button" class="btn btn-success">Upload</button>
    </div> --}}
</div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success w-100">Kirim</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Notifikasi SweetAlert jika berhasil --}}
            @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#198754'
                });
            </script>
            @endif

            {{-- Show/Hide form sections + required toggling --}}
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const layananSelect = document.querySelector('select[name="pengajuan_id"]');
                    const formPenelitian = document.getElementById('form-penelitian');
                    const formPeminjaman = document.getElementById('form-peminjaman');
                    const formKeramaian = document.getElementById('form-keramaian');
                    const allForms = [formPenelitian, formPeminjaman, formKeramaian];

                    function hideAllForms() {
                        allForms.forEach(form => {
                            form.style.display = 'none';
                            const inputs = form.querySelectorAll('input, select, textarea');
                            inputs.forEach(input => {
                                input.disabled = true;
                                input.removeAttribute('required');
                            });
                        });
                    }

                    layananSelect.addEventListener('change', function() {
                        hideAllForms();
                        if (this.value == '2') {
                            formPenelitian.style.display = 'block';
                            formPenelitian.querySelectorAll('input, select, textarea').forEach(input => {
                                input.disabled = false; input.setAttribute('required', 'required');
                            });
                            // jam tidak ada di penelitian → tidak perlu
                        } else if (this.value == '1') {
                            formPeminjaman.style.display = 'block';
                            formPeminjaman.querySelectorAll('input, select, textarea').forEach(input => {
                                input.disabled = false; input.setAttribute('required', 'required');
                            });
                            // jam_mulai & jam_selesai bukan wajib mutlak? hapus required jika perlu
                        } else if (this.value == '3') {
                            formKeramaian.style.display = 'block';
                            formKeramaian.querySelectorAll('input, select, textarea').forEach(input => {
                                input.disabled = false; input.setAttribute('required', 'required');
                            });
                        }
                    });

                    hideAllForms(); // initial
                });
            </script>

            {{-- Validasi tanggal mulai/selesai per section --}}
            <script>
            document.addEventListener("DOMContentLoaded", function () {
              const TODAY = new Date().toISOString().split('T')[0];

              const sections = [
                { name: 'peminjaman', container: document.getElementById('form-peminjaman'),
                  mulai: document.getElementById('tgl_mulai_1'),
                  selesai: document.getElementById('tgl_selesai_1') },
                { name: 'keramaian', container: document.getElementById('form-keramaian'),
                  mulai: document.getElementById('tgl_mulai_2'),
                  selesai: document.getElementById('tgl_selesai_2') },
                { name: 'penelitian', container: document.getElementById('form-penelitian'),
                  mulai: document.getElementById('tgl_mulai_3'),
                  selesai: document.getElementById('tgl_selesai_3') },
              ];

              function isActiveSection(s) {
                if (!s.container) return false;
                const style = window.getComputedStyle(s.container);
                return style.display !== 'none' && style.visibility !== 'hidden';
              }

              sections.forEach(s => {
                if (s.mulai)   s.mulai.setAttribute('min', TODAY);
                if (s.selesai) s.selesai.setAttribute('min', TODAY);
                if (s.mulai && s.selesai) {
                  s.mulai.addEventListener('change', () => {
                    const minForEnd = s.mulai.value || TODAY;
                    s.selesai.setAttribute('min', minForEnd);
                    if (s.selesai.value && s.selesai.value < minForEnd) {
                      s.selesai.value = '';
                    }
                  });
                }
              });

              const form = document.querySelector('form[action*="client.pengajuan.store"]') || document.querySelector('form');
              if (!form) return;

              form.addEventListener('submit', function (e) {
                const active = sections.find(isActiveSection);
                if (!active) return;

                const start = active.mulai ? active.mulai.value : '';
                const end   = active.selesai ? active.selesai.value : '';

                if (!start || !end) {
                  e.preventDefault();
                  alert('Tanggal mulai dan tanggal selesai wajib diisi.');
                  (active.mulai && !start ? active.mulai : active.selesai)?.focus();
                  return;
                }
                if (start < TODAY) {
                  e.preventDefault();
                  alert('Tanggal mulai tidak boleh sebelum hari ini. Silakan input ulang.');
                  active.mulai.focus();
                  return;
                }
                if (end < start) {
                  e.preventDefault();
                  alert('Tanggal selesai tidak boleh sebelum tanggal mulai. Silakan input ulang.');
                  active.selesai.focus();
                  return;
                }
              });
            });
            </script>

            <!-- Sidebar -->
            <div class="col-md-3">
                <h5 class="fw-bold">Layanan Terkait</h5>
                <ul class="list-unstyled">
                    @foreach (App\Models\Layanan::where('id', '!=', $layanan->id)->latest()->take(5)->get() as $layanan)
                        <li class="d-flex mb-3">
                            <img src="{{ asset('layanan/' . $layanan->gambar) }}" alt="{{ $layanan->layanan }}"
                                class="me-2" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                            <div>
                                <strong>{{ $layanan->layanan }}</strong>
                                <p class="small text-muted mb-0">
                                    {{ Str::limit(strip_tags(optional($layanan->content)->isi1), 50) }}
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- ClockPicker dependencies (HAPUS baris jQuery jika sudah dimuat di layout) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.js"></script>

    {{-- Inisialisasi ClockPicker + normalisasi format waktu + validasi jam selesai >= jam mulai (jika tanggal sama) --}}
    <script>
      (function(){
        function initClockpicker(scope){
          $(scope).find('.clockpicker').clockpicker({
            autoclose: true,
            donetext: 'Pilih',
            placement: 'bottom',
            align: 'left'
          });
        }
        // Inisialisasi awal (modal mungkin belum dibuka)
        initClockpicker(document);

        // Jika modal bootstrap dipakai, re-init ketika modal ditampilkan (agar posisi popper akurat)
        document.getElementById('pengajuanLayananModal')?.addEventListener('shown.bs.modal', function(){
          initClockpicker(this);
        });

        // Normalisasi HH:mm
        $(document).on('change', '.clockpicker', function () {
          var v = (this.value || '').trim();
          var m = v.match(/^(\d{1,2}):(\d{2})$/);
          if (!m) { this.value = ''; return; }
          var h = parseInt(m[1], 10), i = parseInt(m[2],10);
          if (isNaN(h) || isNaN(i) || h<0 || h>23 || i<0 || i>59) { this.value = ''; return; }
          this.value = (h<10?'0':'')+h+':'+(i<10?'0':'')+i;
        });

        // Validasi jam selesai >= jam mulai untuk tanggal yang sama (di submit)
        function toDateTime(d, t) {
          if (!d || !t) return null;
          const [Y,M,D] = d.split('-').map(Number);
          const [h,i]   = t.split(':').map(Number);
          return new Date(Y,(M-1),D,h,i,0);
        }
        const $form = $('form[action*="client.pengajuan.store"]').first().length
          ? $('form[action*="client.pengajuan.store"]').first()
          : $('form').first();

        $form.on('submit', function (e) {
          // Cari container aktif
          const candidates = ['#form-peminjaman','#form-keramaian'];
          let $active = null;
          for (const sel of candidates) {
            const $c = $(sel);
            if ($c.is(':visible')) { $active = $c; break; }
          }
          if (!$active) return; // tidak ada jam pada penelitian

          const sd = $active.find('input[name="tgl_mulai"]').val();
          const ed = $active.find('input[name="tgl_selesai"]').val();
          const st = $active.find('input[name="jam_mulai"]').val();
          const et = $active.find('input[name="jam_selesai"]').val();

          if (sd && ed && st && et && sd === ed) {
            const startDT = toDateTime(sd, st);
            const endDT   = toDateTime(ed, et);
            if (startDT && endDT && endDT < startDT) {
              e.preventDefault();
              alert('Jam selesai tidak boleh lebih awal dari jam mulai pada tanggal yang sama.');
              $active.find('input[name="jam_selesai"]').focus();
              return false;
            }
          }
        });
      })();
    </script>

    <script>
    document.getElementById('fileUpload').addEventListener('change', function () {
        const maxSize = 5 * 1024 * 1024; // 5 MB
        const warning = document.getElementById('fileWarning');
        let files = this.files;
        let isValid = true;

        for (let i = 0; i < files.length; i++) {
            let file = files[i];
            let fileName = file.name.toLowerCase();

            // Cek ukuran file
            if (file.size > maxSize) {
                isValid = false;
                warning.innerText = `File "${file.name}" melebihi batas 5 MB!`;
                this.value = ""; // reset input
                break;
            }

            // Cek ekstensi PPT / PPTX
            if (fileName.endsWith(".ppt") || fileName.endsWith(".pptx")) {
                isValid = false;
                warning.innerText = `File "${file.name}" tidak diperbolehkan (PPT/PPTX dilarang)!`;
                this.value = ""; // reset input
                break;
            }
        }

        warning.style.display = isValid ? "none" : "block";
    });
</script>
@endsection
