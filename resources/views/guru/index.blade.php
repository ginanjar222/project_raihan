@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Updated header with elegant white design and sky blue accents -->
    <div class="card mb-4 shadow-sm" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2" style="color: #64b6f7; font-weight: 700; font-size: 2rem; font-family: 'DM Sans', sans-serif;">
                        Data Guru & Staff
                    </h1>
                    <p class="text-muted mb-0" style="font-family: 'DM Sans', sans-serif; color: #6b7280;">Kelola data guru dan staff sekolah</p>
                </div>
                <div class="d-flex gap-3">
                    <!-- Updated button with new color scheme -->
                    <a href="{{ url('/guru/create') }}" class="btn btn-primary btn-lg shadow-sm" style="background: #64b6f7; border: none; border-radius: 8px; transition: all 0.3s ease; font-family: 'DM Sans', sans-serif; font-weight: 600;">
                        <i class="fas fa-plus me-2"></i>Tambah Guru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Updated search bar with elegant white design -->
    <div class="card mb-4 shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text" style="background: #64b6f7; border: none; color: white;">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari berdasarkan nama, jabatan, atau UID kartu..." style="border: 2px solid #e5e7eb; border-left: none; font-family: 'DM Sans', sans-serif;">
                    </div>
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <div class="d-flex justify-content-md-end">
                        <span class="badge" style="background: #64b6f7; font-size: 0.9rem; padding: 8px 12px; font-family: 'DM Sans', sans-serif;">
                            <i class="fas fa-users me-1"></i>
                            Total: <span id="totalCount">{{ count($guru) }}</span> Guru/Staff
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Updated alert with new color scheme -->
    @if(session('success'))
        <div class="alert alert-success shadow-sm mb-4" style="border: none; border-radius: 8px; background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-2" style="color: #10b981;"></i>
            <span style="color: #047857; font-family: 'DM Sans', sans-serif;">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Updated no results message with elegant white design -->
    <div id="noResults" class="card shadow-sm mb-4" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff; display: none;">
        <div class="card-body text-center py-5">
            <i class="fas fa-search text-muted mb-3" style="font-size: 3rem; color: #9ca3af;"></i>
            <h5 class="text-muted mb-2" style="font-family: 'DM Sans', sans-serif; color: #6b7280;">Tidak ada hasil ditemukan</h5>
            <p class="text-muted mb-0" style="font-family: 'DM Sans', sans-serif; color: #9ca3af;">Coba gunakan kata kunci yang berbeda</p>
        </div>
    </div>

    <!-- Updated table with elegant white design and improved contrast -->
    <div class="card shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="guruTable">
                    <thead style="background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                        <tr>
                            <th class="px-4 py-4" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; color: #374151; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                <i class="fas fa-id-badge me-2" style="color: #64b6f7;"></i>NUPTK
                            </th>
                            <th class="px-4 py-4" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; color: #374151; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                <i class="fas fa-user me-2" style="color: #64b6f7;"></i>Nama
                            </th>
                            <th class="px-4 py-4" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; color: #374151; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                <i class="fas fa-briefcase me-2" style="color: #10b981;"></i>Jabatan
                            </th>
                            <th class="px-4 py-4" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; color: #374151; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                <i class="fas fa-id-card me-2" style="color: #f59e0b;"></i>UID Kartu
                            </th>
                            <th class="px-4 py-4 text-end" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; color: #374151; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                <i class="fas fa-cogs me-2" style="color: #64b6f7;"></i>Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody id="guruTableBody">
                        @foreach($guru as $index => $g)
                            <tr class="table-row-hover guru-row" style="transition: all 0.2s ease; border-bottom: 1px solid #f3f4f6;" 
                                data-nip="{{ strtolower($g->nip ?? '') }}"
                                data-nama="{{ strtolower($g->nama) }}" 
                                data-jabatan="{{ strtolower($g->jabatan) }}" 
                                data-uid="{{ strtolower($g->uid) }}">
                                <td class="px-4 py-3 align-middle">
                                    <span class="badge" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); padding: 6px 12px; border-radius: 20px; font-family: 'DM Sans', monospace; font-size: 0.75rem; font-weight: 500;">
                                        {{ $g->nip ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="d-flex align-items-center">
                                        @if($g->foto_url)
                                            <img src="{{ $g->foto_url }}" alt="{{ $g->nama }}" class="rounded-circle me-3 border shadow-sm" style="width: 42px; height: 42px; object-fit: cover;">
                                        @else
                                            <div class="avatar-circle me-3 shadow-sm" style="width: 42px; height: 42px; background: #64b6f7; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-family: 'DM Sans', sans-serif;">
                                                {{ strtoupper(substr($g->nama, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <span class="fw-semibold d-block" style="font-family: 'DM Sans', sans-serif; color: #374151;">{{ $g->nama }}</span>
                                            @if($g->has_face)
                                                <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); font-size: 0.7rem; border-radius: 10px; font-weight: 500;">
                                                    <i class="fas fa-check-circle me-1"></i>Wajah Terdaftar
                                                </span>
                                            @else
                                                <span class="badge" style="background: rgba(156, 163, 175, 0.1); color: #9ca3af; border: 1px solid rgba(156, 163, 175, 0.2); font-size: 0.7rem; border-radius: 10px; font-weight: 500;">
                                                    <i class="fas fa-minus-circle me-1"></i>Belum Ada Wajah
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <!-- Updated badge with elegant styling -->
                                    <span class="badge" style="background: rgba(100, 182, 247, 0.1); color: #64b6f7; border: 1px solid rgba(100, 182, 247, 0.2); padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-family: 'DM Sans', sans-serif; font-weight: 500;">
                                        {{ $g->jabatan }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <span class="badge" style="background: rgba(107, 114, 128, 0.1); color: #6b7280; border: 1px solid rgba(107, 114, 128, 0.2); padding: 6px 12px; border-radius: 20px; font-family: 'DM Sans', monospace; font-size: 0.75rem; font-weight: 500;">
                                        {{ $g->uid }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <!-- Removed Jadwal button and moved Edit/Hapus to the right -->
                                    <div class="d-flex justify-content-end gap-2">
                                        <!-- Updated buttons with new color scheme -->
                                        <a href="{{ url('/guru/'.$g->id.'/edit') }}" class="btn btn-warning btn-sm shadow-sm" style="background: #f59e0b; border: none; border-radius: 6px; transition: all 0.2s ease; font-family: 'DM Sans', sans-serif; font-weight: 500; color: white;">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        <form action="{{ url('/guru/'.$g->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <!-- Menghapus onsubmit confirm default dan menambahkan class untuk modal custom -->
                                            <button type="button" class="btn btn-danger btn-sm shadow-sm delete-btn" style="background: #ef4444; border: none; border-radius: 6px; transition: all 0.2s ease; font-family: 'DM Sans', sans-serif; font-weight: 500;" data-guru-name="{{ $g->nama }}">
                                                <i class="fas fa-trash me-1"></i>Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Custom Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
            <div class="modal-header" style="border-bottom: 1px solid #f3f4f6; padding: 24px 24px 16px 24px;">
                <div class="d-flex align-items-center">
                    <div class="me-3" style="width: 48px; height: 48px; background: rgba(239, 68, 68, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 20px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-1" id="deleteModalLabel" style="font-family: 'DM Sans', sans-serif; font-weight: 600; color: #374151;">Konfirmasi Hapus</h5>
                        <p class="text-muted mb-0" style="font-size: 0.875rem; font-family: 'DM Sans', sans-serif;">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin: 0;"></button>
            </div>
            <div class="modal-body" style="padding: 16px 24px 24px 24px;">
                <p style="font-family: 'DM Sans', sans-serif; color: #6b7280; margin-bottom: 0;">
                    Apakah Anda yakin ingin menghapus data guru <strong id="guruName" style="color: #374151;"></strong>? 
                    Data yang sudah dihapus tidak dapat dikembalikan.
                </p>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #f3f4f6; padding: 16px 24px 24px 24px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background: #6b7280; border: none; border-radius: 8px; padding: 8px 16px; font-family: 'DM Sans', sans-serif; font-weight: 500;">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete" style="background: #ef4444; border: none; border-radius: 8px; padding: 8px 16px; font-family: 'DM Sans', sans-serif; font-weight: 500;">
                    <i class="fas fa-trash me-1"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Updated hover effects with elegant white theme */
.table-row-hover:hover {
    background-color: rgba(100, 182, 247, 0.05) !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(100, 182, 247, 0.1);
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-primary:hover {
    background: #007acc !important;
}

.avatar-circle {
    transition: all 0.2s ease;
}

.avatar-circle:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(100, 182, 247, 0.2);
}

.card {
    transition: all 0.2s ease;
}

.badge {
    transition: all 0.2s ease;
}

.badge:hover {
    transform: scale(1.02);
}

/* Updated search input focus with sky blue color */
#searchInput:focus {
    border-color: #64b6f7;
    box-shadow: 0 0 0 0.2rem rgba(100, 182, 247, 0.25);
    outline: none;
}

.guru-row.hidden {
    display: none;
}

.guru-row.show {
    display: table-row;
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Menambahkan styling untuk modal custom */
.modal-content {
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.btn-close:focus {
    box-shadow: none;
}

#deleteModal .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>

<!-- Menambahkan JavaScript untuk fitur pencarian -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const guruRows = document.querySelectorAll('.guru-row');
    const totalCount = document.getElementById('totalCount');
    const noResults = document.getElementById('noResults');
    const guruTable = document.getElementById('guruTable');
    
    let totalGuru = {{ count($guru) }};
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleCount = 0;
        
        guruRows.forEach(function(row) {
            const nip = row.getAttribute('data-nip'); // menambahkan pencarian berdasarkan NIP
            const nama = row.getAttribute('data-nama');
            const jabatan = row.getAttribute('data-jabatan');
            const uid = row.getAttribute('data-uid');
            
            const isMatch = nip.includes(searchTerm) ||
                           nama.includes(searchTerm) || 
                           jabatan.includes(searchTerm) || 
                           uid.includes(searchTerm);
            
            if (isMatch) {
                row.classList.remove('hidden');
                row.classList.add('show');
                visibleCount++;
            } else {
                row.classList.add('hidden');
                row.classList.remove('show');
            }
        });
        
        // Update counter
        totalCount.textContent = visibleCount;
        
        // Show/hide no results message
        if (visibleCount === 0 && searchTerm !== '') {
            noResults.style.display = 'block';
            guruTable.style.display = 'none';
        } else {
            noResults.style.display = 'none';
            guruTable.style.display = 'block';
        }
    });
    
    // Clear search when input is empty
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            this.dispatchEvent(new Event('input'));
        }
    });
    
    let currentForm = null;
    
    // Handle delete button click
    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const guruName = this.getAttribute('data-guru-name');
            const form = this.closest('.delete-form');
            
            document.getElementById('guruName').textContent = guruName;
            currentForm = form;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });
    });
    
    // Handle confirm delete
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (currentForm) {
            currentForm.submit();
        }
    });
    
    // Reset form when modal is hidden
    document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function() {
        currentForm = null;
    });
});
</script>
@endsection
