<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages\AbsensiPkl;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('landing');
});

Route::get('/login', function () {
    return redirect('/admin/login');
});

Route::post('/absensi/masuk', [AbsensiPkl::class, 'absenMasuk'])->middleware('auth');
Route::post('/absensi/keluar', [AbsensiPkl::class, 'absenKeluar'])->middleware('auth');