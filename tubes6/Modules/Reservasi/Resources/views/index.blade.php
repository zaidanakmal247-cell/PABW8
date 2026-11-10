@extends('layouts.app')

@section('title', 'Reservasi Parkir')

@section('content')
@php($isPetugas = auth()->user()->isPetugas())
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Reservasi Parkir</h1>
        <p class="text-muted mb-0">
            {{ $isPetugas ? 'Perbarui status reservasi dan catat denda.' : 'Buat dan ubah reservasi milik Anda.' }}
        </p>
    </div>
    <span class="badge text-bg-info">AJAX</span>
</div>

<div id="notice" class="alert d-none" role="alert"></div>
@if($isPetugas)
    <div class="card border-0 shadow-sm mb-4 d-none" id="edit-card">
        <div class="card-header bg-white fw-semibold" id="form-title">Perbarui status dan denda</div>
        <div class="card-body">
            <form id="reservasi-form">
                <input id="reservasi-id" type="hidden">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label" for="status">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="denda_nominal">Denda (Rp)</label>
                        <input class="form-control" id="denda_nominal" name="denda_nominal" type="number" min="0" step="1000">
                        <div class="form-text">Kosongkan jika tidak ada denda.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="denda_keterangan">Keterangan denda</label>
                        <input class="form-control" id="denda_keterangan" name="denda_keterangan" maxlength="2000">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">Simpan perubahan</button>
                        <button class="btn btn-outline-secondary" id="cancel-edit" type="button">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold" id="form-title">Buat reservasi</div>
        <div class="card-body">
            <form id="reservasi-form" enctype="multipart/form-data">
                <input id="reservasi-id" type="hidden">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="nama_pengguna">Nama pengguna</label>
                        <input class="form-control" id="nama_pengguna" name="nama_pengguna" maxlength="255" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="nomor_plat">Nomor plat</label>
                        <input class="form-control" id="nomor_plat" name="nomor_plat" maxlength="20" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="kategori_id">Kategori</label>
                        <select class="form-select" id="kategori_id" name="kategori_id" required>
                            <option value="">Pilih kategori</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="tanggal_reservasi">Tanggal reservasi</label>
                        <input class="form-control" id="tanggal_reservasi" name="tanggal_reservasi" type="date" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="durasi_jam">Durasi (jam)</label>
                        <input class="form-control" id="durasi_jam" name="durasi_jam" type="number" min="1" max="24" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label" for="file_stnk">Berkas STNK (JPG, PNG, PDF; maksimal 4 MB)</label>
                        <input class="form-control" id="file_stnk" name="file_stnk" type="file" accept=".jpg,.jpeg,.png,.pdf">
                        <div class="form-text" id="file-help">Opsional untuk reservasi baru.</div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button class="btn btn-primary" id="save-button" type="submit">Simpan reservasi</button>
                        <button class="btn btn-outline-secondary d-none" id="cancel-edit" type="button">Batal edit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    @if($isPetugas)<th>Pemilik</th>@endif
                    <th>Nama / Plat</th><th>Kategori</th><th>Tanggal</th><th>Durasi</th>
                    <th>Status</th><th>STNK</th>@if($isPetugas)<th>Denda</th>@endif<th>Aksi</th>
                </tr>
            </thead>
            <tbody id="reservasi-list">
                <tr><td class="text-center text-muted py-4" colspan="{{ $isPetugas ? 9 : 7 }}">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const form = document.getElementById('reservasi-form');
    const list = document.getElementById('reservasi-list');
    const notice = document.getElementById('notice');
    const baseUrl = @json(route('reservasi.index'));
    const dataUrl = @json(route('reservasi.data'));
    const csrf = @json(csrf_token());
    const isPetugas = @json($isPetugas);
    const columnCount = isPetugas ? 9 : 7;

    function showNotice(message, type = 'success') {
        notice.className = `alert alert-${type}`;
        notice.textContent = message;
    }

    async function request(url, options = {}) {
        const response = await fetch(url, {
            ...options,
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.headers || {}),
            },
        });
        const body = await response.json();
        if (!response.ok) {
            const errors = body.errors ? Object.values(body.errors).flat().join(' ') : body.message;
            throw new Error(errors || 'Permintaan gagal diproses.');
        }
        return body;
    }

    function createCell(row, value) {
        const cell = document.createElement('td');
        cell.textContent = value;
        row.append(cell);
        return cell;
    }

    function render(items) {
        list.replaceChildren();
        if (!items.length) {
            const row = document.createElement('tr');
            const cell = createCell(row, 'Belum ada reservasi.');
            cell.colSpan = columnCount;
            cell.className = 'text-center text-muted py-4';
            list.append(row);
            return;
        }

        items.forEach((item) => {
            const row = document.createElement('tr');
            if (isPetugas) createCell(row, item.pemilik);
            const nameCell = createCell(row, `${item.nama_pengguna} — ${item.nomor_plat}`);
            nameCell.className = 'fw-semibold';
            createCell(row, item.kategori);
            createCell(row, item.tanggal_reservasi);
            createCell(row, `${item.durasi_jam} jam`);
            createCell(row, item.status);

            const fileCell = document.createElement('td');
            if (item.file_url) {
                const link = document.createElement('a');
                link.href = item.file_url;
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                link.textContent = 'Buka STNK';
                fileCell.append(link);
            } else {
                fileCell.textContent = '—';
            }
            row.append(fileCell);

            if (isPetugas) {
                createCell(row, item.denda_nominal
                    ? `Rp ${Number(item.denda_nominal).toLocaleString('id-ID')}`
                    : '—');
            }

            const actions = document.createElement('td');
            const edit = document.createElement('button');
            edit.type = 'button';
            edit.className = 'btn btn-sm btn-outline-primary';
            edit.textContent = isPetugas ? 'Status / denda' : 'Edit';
            edit.addEventListener('click', () => beginEdit(item));
            actions.append(edit);
            row.append(actions);
            list.append(row);
        });
    }

    async function loadItems() {
        const result = await request(dataUrl);
        render(result.data);
    }

    function beginEdit(item) {
        document.getElementById('reservasi-id').value = item.id;
        if (isPetugas) {
            form.elements.status.value = item.status;
            form.elements.denda_nominal.value = item.denda_nominal ?? '';
            form.elements.denda_keterangan.value = item.denda_keterangan ?? '';
            document.getElementById('edit-card').classList.remove('d-none');
            document.getElementById('form-title').textContent =
                `Perbarui status dan denda — ${item.nama_pengguna}`;
        } else {
            ['kategori_id', 'nama_pengguna', 'nomor_plat', 'tanggal_reservasi', 'durasi_jam']
                .forEach((field) => { form.elements[field].value = item[field]; });
            document.getElementById('form-title').textContent = 'Edit reservasi';
            document.getElementById('save-button').textContent = 'Perbarui reservasi';
            document.getElementById('cancel-edit').classList.remove('d-none');
            document.getElementById('file-help').textContent = item.file_url
                ? 'Berkas saat ini dapat dibuka dari daftar. Pilih berkas baru jika ingin menggantinya.'
                : 'Belum ada berkas; unggah jika diperlukan.';
        }
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function resetForm() {
        form.reset();
        document.getElementById('reservasi-id').value = '';
        if (isPetugas) {
            document.getElementById('edit-card').classList.add('d-none');
        } else {
            document.getElementById('form-title').textContent = 'Buat reservasi';
            document.getElementById('save-button').textContent = 'Simpan reservasi';
            document.getElementById('cancel-edit').classList.add('d-none');
            document.getElementById('file-help').textContent = 'Opsional untuk reservasi baru.';
        }
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const id = document.getElementById('reservasi-id').value;
        try {
            const body = new FormData(form);
            if (id) body.append('_method', 'PUT');
            const result = await request(id ? `${baseUrl}/${id}` : baseUrl, {
                method: 'POST',
                body,
            });
            showNotice(result.message);
            resetForm();
            await loadItems();
        } catch (error) {
            showNotice(error.message, 'danger');
        }
    });

    document.getElementById('cancel-edit')?.addEventListener('click', resetForm);
    loadItems().catch((error) => {
        showNotice(error.message, 'danger');
        list.replaceChildren();
    });
})();
</script>
@endpush
