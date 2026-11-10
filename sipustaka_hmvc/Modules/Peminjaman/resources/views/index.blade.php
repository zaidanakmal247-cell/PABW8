@extends('layouts.app')

@section('title', 'Peminjaman')

@section('content')
<div class="page-heading"><div><p class="eyebrow">TRANSAKSI</p><h1>Data Peminjaman</h1><p class="muted">Tambah, lihat, ubah, dan hapus data tanpa memuat ulang halaman.</p></div></div>
<div id="notice" class="notice" role="status" hidden></div>
<section class="card" style="padding:22px;margin-bottom:22px">
    <h2 id="form-title" style="margin:0 0 16px;font-size:19px">Tambah peminjaman</h2>
    <form id="loan-form" class="form-grid" style="padding:0;max-width:none" enctype="multipart/form-data">
        <input type="hidden" id="record_id">
        <div class="field"><label for="nama_peminjam">Nama peminjam</label><input id="nama_peminjam" name="nama_peminjam" maxlength="150" required><small class="error" data-error="nama_peminjam"></small></div>
        <div class="field"><label for="judul_buku">Judul buku</label><input id="judul_buku" name="judul_buku" maxlength="255" required><small class="error" data-error="judul_buku"></small></div>
        <div class="field"><label for="tanggal_pinjam">Tanggal pinjam</label><input id="tanggal_pinjam" name="tanggal_pinjam" type="date" required><small class="error" data-error="tanggal_pinjam"></small></div>
        <div class="field"><label for="tanggal_kembali">Tanggal kembali</label><input id="tanggal_kembali" name="tanggal_kembali" type="date"><small class="error" data-error="tanggal_kembali"></small></div>
        <div class="field"><label for="status">Status</label><select id="status" name="status" required><option value="Dipinjam">Dipinjam</option><option value="Dikembalikan">Dikembalikan</option></select><small class="error" data-error="status"></small></div>
        <div class="field"><label for="bukti">Bukti peminjaman <span class="muted">(JPG, PNG, PDF; maks. 2 MB)</span></label><input id="bukti" name="bukti" type="file" accept=".jpg,.jpeg,.png,.pdf"><small class="error" data-error="bukti"></small><small id="current-file" class="muted"></small></div>
        <div class="form-actions"><button id="submit-button" class="button" type="submit">Simpan peminjaman</button><button id="cancel-edit" class="button quiet" type="button" hidden>Batal ubah</button></div>
    </form>
</section>
<div class="table-wrap">
    <table>
        <thead><tr><th>Peminjam</th><th>Judul buku</th><th>Tanggal pinjam</th><th>Tanggal kembali</th><th>Status</th><th>Bukti</th><th>Aksi</th></tr></thead>
        <tbody id="loans"><tr><td class="empty" colspan="7">Memuat data...</td></tr></tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const form = document.getElementById('loan-form');
    const rows = document.getElementById('loans');
    const notice = document.getElementById('notice');
    const baseUrl = @json(url('/peminjaman'));
    const dataUrl = @json(route('peminjaman.data'));
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    let records = [];

    function showNotice(message, isError = false) {
        notice.hidden = false;
        notice.textContent = message;
        notice.className = `notice ${isError ? 'alert-error' : 'success'}`;
    }

    function clearErrors() {
        document.querySelectorAll('[data-error]').forEach((el) => { el.textContent = ''; });
    }

    function showErrors(errors) {
        Object.entries(errors).forEach(([field, messages]) => {
            const target = document.querySelector(`[data-error="${field}"]`);
            if (target) target.textContent = messages[0];
        });
    }

    async function request(url, options = {}) {
        const response = await fetch(url, {
            ...options,
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, ...(options.headers || {}) },
        });
        const payload = await response.json();
        if (!response.ok) {
            const error = new Error(payload.message || 'Permintaan gagal. Silakan coba kembali.');
            error.status = response.status;
            error.errors = payload.errors || {};
            throw error;
        }
        return payload;
    }

    function render() {
        rows.replaceChildren();
        if (!records.length) {
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.colSpan = 7;
            td.className = 'empty';
            td.textContent = 'Belum ada data peminjaman.';
            tr.append(td);
            rows.append(tr);
            return;
        }
        records.forEach((record) => {
            const tr = document.createElement('tr');
            [record.nama_peminjam, record.judul_buku, record.tanggal_pinjam, record.tanggal_kembali || '—', record.status].forEach((value) => {
                const td = document.createElement('td');
                td.textContent = value;
                tr.append(td);
            });
            const fileCell = document.createElement('td');
            if (record.bukti_url) {
                const link = document.createElement('a');
                link.href = record.bukti_url;
                link.target = '_blank';
                link.rel = 'noopener';
                link.textContent = 'Buka bukti';
                fileCell.append(link);
            } else {
                fileCell.textContent = '—';
            }
            tr.append(fileCell);

            const actions = document.createElement('td');
            actions.className = 'actions';
            const edit = document.createElement('button');
            edit.type = 'button';
            edit.className = 'link';
            edit.dataset.action = 'edit';
            edit.dataset.id = record.id;
            edit.textContent = 'Ubah';
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'link danger-text';
            remove.dataset.action = 'delete';
            remove.dataset.id = record.id;
            remove.textContent = 'Hapus';
            actions.append(edit, remove);
            tr.append(actions);
            rows.append(tr);
        });
    }

    async function loadRecords() {
        try {
            const payload = await request(dataUrl);
            records = payload.data;
            render();
        } catch (error) {
            rows.replaceChildren();
            showNotice(error.message, true);
        }
    }

    function resetForm() {
        form.reset();
        document.getElementById('record_id').value = '';
        document.getElementById('form-title').textContent = 'Tambah peminjaman';
        document.getElementById('submit-button').textContent = 'Simpan peminjaman';
        document.getElementById('cancel-edit').hidden = true;
        document.getElementById('current-file').textContent = '';
        clearErrors();
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors();
        notice.hidden = true;
        const id = document.getElementById('record_id').value;
        const body = new FormData(form);
        const url = id ? `${baseUrl}/${id}` : baseUrl;
        if (id) body.append('_method', 'PUT');
        try {
            const payload = await request(url, { method: 'POST', body });
            showNotice(payload.message);
            resetForm();
            await loadRecords();
        } catch (error) {
            if (error.status === 422) showErrors(error.errors);
            else showNotice(error.message, true);
        }
    });

    document.getElementById('cancel-edit').addEventListener('click', resetForm);
    rows.addEventListener('click', async (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const record = records.find((item) => String(item.id) === button.dataset.id);
        if (!record) return;

        if (button.dataset.action === 'edit') {
            document.getElementById('record_id').value = record.id;
            ['nama_peminjam', 'judul_buku', 'tanggal_pinjam', 'tanggal_kembali', 'status'].forEach((field) => {
                document.getElementById(field).value = record[field] || '';
            });
            document.getElementById('form-title').textContent = 'Ubah peminjaman';
            document.getElementById('submit-button').textContent = 'Simpan perubahan';
            document.getElementById('cancel-edit').hidden = false;
            document.getElementById('current-file').textContent = record.bukti_url ? 'Berkas saat ini tetap tersimpan jika tidak memilih pengganti.' : '';
            clearErrors();
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else if (confirm('Hapus data peminjaman ini?')) {
            try {
                const payload = await request(`${baseUrl}/${record.id}`, { method: 'DELETE' });
                showNotice(payload.message);
                await loadRecords();
            } catch (error) {
                showNotice(error.message, true);
            }
        }
    });

    loadRecords();
})();
</script>
@endpush
