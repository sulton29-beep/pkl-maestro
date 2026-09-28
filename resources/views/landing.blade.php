<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKL SMKS MAESTRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #0f172a; color: #f8fafc; font-family: 'Inter', sans-serif; }
        .card-role { transition: all 0.3s ease; }
        .card-role:hover { transform: translateY(-5px); box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.5); }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-6">

    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">PKL SMKS MAESTRO</h1>
        <p class="text-slate-400 text-lg">Sistem Informasi Manajemen Praktik Kerja Lapangan</p>
        <p class="text-slate-500 text-sm mt-2">Silakan pilih role Anda untuk masuk</p>
    </div>

    <!-- Grid 9 Role -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-w-5xl">
        
        <!-- Admin -->
        <a href="/admin/login" class="card-role bg-slate-800 p-6 rounded-xl border border-slate-700 flex flex-col items-center text-center">
            <div class="bg-blue-500/20 p-3 rounded-full mb-3 text-blue-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <h3 class="text-xl font-semibold text-white">Admin</h3>
            <p class="text-sm text-slate-400 mt-1">Kelola semua data sistem</p>
        </a>

        <!-- Kepala Sekolah -->
        <a href="/admin/login" class="card-role bg-slate-800 p-6 rounded-xl border border-slate-700 flex flex-col items-center text-center">
            <div class="bg-emerald-500/20 p-3 rounded-full mb-3 text-emerald-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <h3 class="text-xl font-semibold text-white">Kepala Sekolah</h3>
            <p class="text-sm text-slate-400 mt-1">Lihat laporan & approve</p>
        </a>

        <!-- Waka Kurikulum -->
        <a href="/admin/login" class="card-role bg-slate-800 p-6 rounded-xl border border-slate-700 flex flex-col items-center text-center">
            <div class="bg-purple-500/20 p-3 rounded-full mb-3 text-purple-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <h3 class="text-xl font-semibold text-white">Waka Kurikulum</h3>
            <p class="text-sm text-slate-400 mt-1">Kelola kurikulum & siswa</p>
        </a>

        <!-- Kaprodi TBSM -->
        <a href="/admin/login" class="card-role bg-slate-800 p-6 rounded-xl border border-slate-700 flex flex-col items-center text-center">
            <div class="bg-orange-500/20 p-3 rounded-full mb-3 text-orange-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <h3 class="text-xl font-semibold text-white">Kaprodi TBSM</h3>
            <p class="text-sm text-slate-400 mt-1">Kelola siswa TBSM</p>
        </a>

        <!-- Kaprodi MPLB -->
        <a href="/admin/login" class="card-role bg-slate-800 p-6 rounded-xl border border-slate-700 flex flex-col items-center text-center">
            <div class="bg-cyan-500/20 p-3 rounded-full mb-3 text-cyan-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <h3 class="text-xl font-semibold text-white">Kaprodi MPLB</h3>
            <p class="text-sm text-slate-400 mt-1">Kelola siswa MPLB</p>
        </a>

        <!-- Guru Pembimbing -->
        <a href="/admin/login" class="card-role bg-slate-800 p-6 rounded-xl border border-slate-700 flex flex-col items-center text-center">
            <div class="bg-pink-500/20 p-3 rounded-full mb-3 text-pink-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h3 class="text-xl font-semibold text-white">Guru Pembimbing</h3>
            <p class="text-sm text-slate-400 mt-1">Bimbing siswa PKL</p>
        </a>

        <!-- Guru Penguji -->
        <a href="/admin/login" class="card-role bg-slate-800 p-6 rounded-xl border border-slate-700 flex flex-col items-center text-center">
            <div class="bg-yellow-500/20 p-3 rounded-full mb-3 text-yellow-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-xl font-semibold text-white">Guru Penguji</h3>
            <p class="text-sm text-slate-400 mt-1">Input nilai sidang</p>
        </a>

        <!-- Siswa -->
        <a href="/admin/login" class="card-role bg-slate-800 p-6 rounded-xl border border-slate-700 flex flex-col items-center text-center">
            <div class="bg-indigo-500/20 p-3 rounded-full mb-3 text-indigo-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <h3 class="text-xl font-semibold text-white">Siswa</h3>
            <p class="text-sm text-slate-400 mt-1">Isi logbook & absensi</p>
        </a>

        <!-- DUDI -->
        <a href="/admin/login" class="card-role bg-slate-800 p-6 rounded-xl border border-slate-700 flex flex-col items-center text-center">
            <div class="bg-rose-500/20 p-3 rounded-full mb-3 text-rose-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <h3 class="text-xl font-semibold text-white">DUDI</h3>
            <p class="text-sm text-slate-400 mt-1">Verifikasi logbook siswa</p>
        </a>

    </div>

    <div class="mt-12 text-slate-600 text-sm">
        &copy; 2026 PKL SMKS MAESTRO. All rights reserved.
    </div>

</body>
</html>