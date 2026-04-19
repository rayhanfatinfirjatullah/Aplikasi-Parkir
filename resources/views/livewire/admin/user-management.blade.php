@section('page-title', 'Kelola User')
@section('page-subtitle', 'Manajemen data pengguna sistem')

{{-- Reusable Delete Modal Macro --}}
@php
function deleteModalClasses() {
    return 'fixed inset-0 z-[60] flex items-center justify-center bg-[#020617]/70 backdrop-blur-sm';
}
@endphp

<div x-data="{ showDeleteModal: false, deleteId: null, deleteName: '' }">

    {{-- ── Toolbar ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama & Username..."
                   class="input-base pl-10 w-full sm:w-80">
        </div>
        <button wire:click="openCreate" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah User
        </button>
    </div>

    {{-- ── Table ── --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                    <tr wire:key="user-{{ $user->id_user }}">
                        <td class="text-[#94a3b8] w-12">{{ $users->firstItem() + $i }}</td>
                        <td class="font-semibold text-[#e2e8f0]">{{ $user->nama_lengkap }}</td>
                        <td class="font-mono text-sm">{{ $user->username }}</td>
                        <td>
                            <span class="badge
                                @if($user->role === 'admin') badge-cyan
                                @elseif($user->role === 'petugas') badge-emerald
                                @else badge-amber @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <button wire:click="toggleStatus({{ $user->id_user }})"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold cursor-pointer transition-all duration-200
                                    {{ $user->status_aktif
                                        ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/25'
                                        : 'bg-rose-500/15 text-rose-400 border border-rose-500/30 hover:bg-rose-500/25' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $user->status_aktif ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                                {{ $user->status_aktif ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <button wire:click="openEdit({{ $user->id_user }})"
                                        class="p-2 text-cyan-400 hover:bg-cyan-500/10 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button @click="deleteId = {{ $user->id_user }}; deleteName = 'User ' + {{ \Illuminate\Support\Js::from($user->nama_lengkap) }}; showDeleteModal = true"
                                        class="p-2 text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center text-[#94a3b8]">
                            <svg class="w-10 h-10 mx-auto mb-3 text-[#334155]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                            Tidak ada data user
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-[#1e293b]">{{ $users->links() }}</div>
    </div>

    {{-- ── Form Modal (Livewire) ── --}}
    @if($showModal)
    <div class="modal-backdrop">
        <div class="modal-box p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-[#e2e8f0]">{{ $isEdit ? 'Edit User' : 'Tambah User' }}</h3>
                <button wire:click="closeModal" class="p-2 text-[#94a3b8] hover:text-[#e2e8f0] hover:bg-[#1e293b] rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form wire:submit="save">
                <div class="space-y-4">
                    <div>
                        <label class="input-label">Nama Lengkap</label>
                        <input wire:model="nama_lengkap" type="text"
                               class="input-base @error('nama_lengkap') input-error @enderror">
                        @error('nama_lengkap') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="input-label">Username</label>
                        <input wire:model="username" type="text"
                               class="input-base @error('username') input-error @enderror">
                        @error('username') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="input-label">Password {{ $isEdit ? '(kosongkan jika tidak diubah)' : '' }}</label>
                        <input wire:model="password" type="password"
                               class="input-base @error('password') input-error @enderror">
                        @error('password') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="input-label">Role</label>
                        <select wire:model="role" class="input-base @error('role') input-error @enderror">
                            <option value="admin">Admin</option>
                            <option value="petugas">Petugas</option>
                            <option value="owner">Owner</option>
                        </select>
                        @error('role') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="input-label">Status</label>
                        <select wire:model="status_aktif" class="input-base @error('status_aktif') input-error @enderror">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                        @error('status_aktif') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-[#1e293b]">
                    <button type="button" wire:click="closeModal" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ── Delete Confirmation Modal (Alpine) ── --}}
    <div x-show="showDeleteModal" style="display: none;"
         class="fixed inset-0 z-[60] flex items-center justify-center bg-[#020617]/70 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-[#0F172A] border border-[#1e293b] rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center"
             x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="showDeleteModal = false">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-rose-500/10 border border-rose-500/20 flex items-center justify-center">
                <svg class="w-7 h-7 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-[#e2e8f0] mb-2">Konfirmasi Penghapusan</h3>
            <p class="text-sm text-[#94a3b8] mb-6">
                Yakin ingin menghapus <strong class="font-bold text-rose-400" x-text="deleteName"></strong> secara permanen? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex gap-3">
                <button type="button" @click="showDeleteModal = false" class="btn-secondary w-full justify-center">Batal</button>
                <button type="button" @click="$wire.delete(deleteId); showDeleteModal = false" class="btn-danger w-full justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
            </div>
        </div>
    </div>

</div>
