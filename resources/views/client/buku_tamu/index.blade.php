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
        @foreach($lokawisata as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
        @endforeach
    </select>
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
