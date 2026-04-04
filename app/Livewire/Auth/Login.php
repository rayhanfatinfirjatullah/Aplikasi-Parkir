<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\LogAktivitas;

class Login extends Component
{
    public string $username = '';
    public string $password = '';

    public function login()
    {
        $this->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(['username' => $this->username, 'password' => $this->password])) {
            $user = Auth::user();

            if (!$user->status_aktif) {
                Auth::logout();
                $this->addError('username', 'Akun Anda tidak aktif. Hubungi administrator.');
                return;
            }

            session()->regenerate();

            LogAktivitas::create([
                'id_user' => $user->id_user,
                'aktivitas' => 'Login ke sistem',
                'waktu_aktivitas' => now(),
            ]);

            return match ($user->role) {
                'admin' => redirect()->route('dashboard'),
                'petugas' => redirect()->route('petugas.transaksi-masuk'),
                'owner' => redirect()->route('owner.laporan'),
                default => redirect()->route('dashboard'),
            };
        }

        $this->addError('username', 'Username atau password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.guest');
    }
}
