@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<section class="card" style="padding:clamp(28px,6vw,64px);margin:38px auto;max-width:820px">
    <p class="eyebrow">PERPUSTAKAAN KAMPUS</p>
    <h1 style="font-size:clamp(38px,7vw,62px);max-width:650px">Selamat datang di SiPustaka.</h1>
    <p class="muted" style="font-size:17px;line-height:1.7;max-width:620px">Kelola koleksi buku dan pantau peminjaman dalam satu tempat. Masuk untuk melanjutkan ke katalog perpustakaan.</p>
    <div style="display:flex;gap:12px;margin-top:24px;flex-wrap:wrap"><a class="button" href="{{ route('login') }}">Masuk</a><a class="button quiet" href="{{ route('register') }}">Daftar anggota</a></div>
</section>
@endsection
