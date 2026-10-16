<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Repositori Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between flex-shrink-0">
            <div>
                <!-- Brand / Logo -->
                <div class="h-16 flex items-center px-6 border-b border-slate-800 bg-slate-950 font-bold text-lg text-white tracking-wide">
                    <i class="ri-folder-shield-2-line text-blue-500 mr-2 text-xl"></i>
                    Repositori Lab
                </div>
                <!-- Navigasi Menu -->
                <nav class="p-4 space-y-1 text-sm font-medium">
                    @php 
                        $role = Auth::user()->role->nama_role; 
                    @endphp
                    <div class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Menu Utama
                    </div>
                    <a href="{{ route('dashboard') }}" 
                        class="flex items-center px-3 py-2.5 rounded-lg {{ request()->is('dashboard') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white transition' }}">
                        <i class="ri-dashboard-line mr-3 text-lg"></i>
                        Dashboard
                    </a>
                    
                    <!-- Khusus Laboran -->
                    @if($role === 'Laboran')
                        <div class="pt-4 px-3 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Master Data
                        </div>
                        <a href="{{ route('laboran.users.index') }}" 
                            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('laboran.users.*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white transition' }}">
                            <i class="ri-user-settings-line mr-3 text-lg"></i>
                            Kelola Akun
                        </a>
                        <a href="{{ route('laboran.mata-kuliah.index') }}" 
                            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('laboran.mata-kuliah.*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white transition' }}">
                            <i class="ri-book-open-line mr-3 text-lg"></i>
                            Mata Kuliah
                        </a>
                        <a href="{{ route('modul.index') }}" 
                            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('modul.*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white transition' }}">
                            <i class="ri-article-line mr-3 text-lg"></i>
                            Semua Modul
                        </a>
                        <a href="{{ route('tugas.index') }}" 
                            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('tugas.*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white transition' }}">
                            <i class="ri-task-line mr-3 text-lg"></i>
                            Semua Tugas
                        </a>
                    @endif

                    <!-- Khusus Dosen & Aslab -->
                    @if(in_array($role, ['Dosen', 'Aslab']))
                        <div class="pt-4 px-3 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Akademik
                        </div>
                        <a href="{{ route('kelas.index') }}" 
                            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('kelas.*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white transition' }}">
                            <i class="ri-book-mark-line mr-3 text-lg"></i>
                            Mata Kuliah Diampu
                        </a>
                        <a href="{{ route('tugas.index') }}" 
                            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('tugas.*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white transition' }}">
                            <i class="ri-task-line mr-3 text-lg"></i>
                            Kelola Tugas
                        </a>
                    @endif

                    <!-- Khusus Mahasiswa -->
                    @if($role === 'Mahasiswa')
                        <div class="pt-4 px-3 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Praktikum
                        </div>
                        <a href="{{ route('kelas.index') }}" 
                            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('kelas.*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white transition' }}">
                            <i class="ri-book-read-line mr-3 text-lg"></i>
                            Mata Kuliah Praktikum
                        </a>
                        <a href="{{ route('profil.index') }}" 
                            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('profil.*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white transition' }}">
                            <i class="ri-user-settings-line mr-3 text-lg"></i>
                            Profil Akun
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Tombol Keluar -->
            <div class="p-4 border-t border-slate-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                        class="w-full flex items-center justify-center px-3 py-2 rounded-lg bg-red-600/10 hover:bg-red-600 text-red-400 hover:text-white text-sm font-medium transition duration-200">
                        <i class="ri-logout-box-r-line mr-2"></i>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Wadah Konten Utama -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 flex-shrink-0">
                <div class="text-slate-600 text-sm font-medium">
                    Halaman / <span class="text-slate-900 font-semibold">@yield('page_title', 'Dashboard')</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-slate-800 leading-none">{{ Auth::user()->nama }}</p>
                        <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                            {{ Auth::user()->role->nama_role }}
                        </span>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-slate-200 border border-slate-300 flex items-center justify-center text-slate-600 font-bold">
                        {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                    </div>
                </div>
            </header>
            
            <main class="flex-1 overflow-y-auto p-8 bg-slate-50">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>