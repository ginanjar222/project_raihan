@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 font-space-grotesk">Detail Guru & Staff</h1>
                    <p class="text-slate-600 mt-2 font-dm-sans">Informasi lengkap data guru dan staff</p>
                </div>
                <a href="{{ route('guru.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Detail Card -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                <h2 class="text-2xl font-bold text-white font-space-grotesk">{{ $guru->nama }}</h2>
                <p class="text-blue-100 mt-1 font-dm-sans">{{ $guru->jabatan }}</p>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Informasi Dasar -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-semibold text-slate-900 font-space-grotesk border-b border-slate-200 pb-2">
                            Informasi Dasar
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                <div>
                                    <p class="text-sm font-medium text-slate-500 font-dm-sans">Nama Lengkap</p>
                                    <p class="text-lg text-slate-900 font-medium font-dm-sans">{{ $guru->nama }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-emerald-500 rounded-full mt-2 flex-shrink-0"></div>
                                <div>
                                    <p class="text-sm font-medium text-slate-500 font-dm-sans">Jabatan</p>
                                    <p class="text-lg text-slate-900 font-medium font-dm-sans">{{ $guru->jabatan }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Sistem -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-semibold text-slate-900 font-space-grotesk border-b border-slate-200 pb-2">
                            Informasi Sistem
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-orange-500 rounded-full mt-2 flex-shrink-0"></div>
                                <div>
                                    <p class="text-sm font-medium text-slate-500 font-dm-sans">UID Kartu RFID</p>
                                    <p class="text-lg text-slate-900 font-mono bg-slate-100 px-3 py-1 rounded-lg inline-block">{{ $guru->uid }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-purple-500 rounded-full mt-2 flex-shrink-0"></div>
                                <div>
                                    <p class="text-sm font-medium text-slate-500 font-dm-sans">Jam Masuk</p>
                                    <p class="text-lg text-slate-900 font-medium font-dm-sans">{{ \Carbon\Carbon::parse($guru->jam_masuk)->format('H:i') }} WIB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 pt-6 border-t border-slate-200">
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('guru.edit', $guru->id) }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Data
                        </a>
                        
                        <form action="{{ route('guru.destroy', $guru->id) }}" method="POST" class="inline-block" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus Data
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
