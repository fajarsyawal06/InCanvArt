<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use App\Models\Artwork;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;

class AuthController extends Controller
{
    public function registerLihat()
    {
        return view('register');
    }

    public function registerSubmit(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan. Silakan pilih username lain.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan. Silakan gunakan email lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama dengan password yang Anda masukkan.',
        ]);


        $data = new User();
        $data->username = $request->username;
        $data->email = $request->email;
        $data->password = bcrypt($request->password);
        $data->token = Str::random(64);
        $data->save();

        Profile::firstOrCreate(
            ['user_id' => $data->user_id],
            [
                'nama_lengkap' => $data->username,
                'bio'          => null,
                'foto_profil'  => null,
                'kontak'       => [],   // karena di-cast array<->json
            ]
        );

        Mail::to($data->email)->send(new VerifyEmail($data, $data->token));

        return redirect()->route('login.lihat')
        ->with('Success', 'Registrasi berhasil. Silakan verifikasi akun terlebih dahulu melalui email Anda.');
    }
    public function loginLihat()
    {
        return view('login');
    }
    public function loginSubmit(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = $request->username;

        // Tentukan apakah input berupa email atau username
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Ambil data user berdasarkan kolom yang sesuai
        $data = User::where($fieldType, $loginInput)->first();

        if ($data && Hash::check($request->password, $data->password)) {
            // Cek apakah akun sudah aktif atau belum
            if ($data->status !== 'aktif') {
                return redirect()->route('login.lihat')
                    ->with('failed', 'Akun Anda belum diverifikasi. Silakan cek email untuk verifikasi.');
            }

            // Jika akun sudah aktif, lanjutkan login
            Auth::login($data);
            $request->session()->regenerate();

            //cek role user
            if ($data->role === 'admin') {
                return redirect()->route('admin')->with('success', 'Login Admin Berhasil');
            }
            return redirect()->route('dashboard')->with('success', 'Login Berhasil');
        }

        // Jika login gagal
        return redirect()->route('login.lihat')
            ->with('failed', 'Username atau password salah.');
    }


    public function verify(Request $request)
    {
        $user = User::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        // Jika user tidak ditemukan
        if (!$user) {
            return redirect()->route('login.lihat')
                ->with('failed', 'Link verifikasi tidak valid atau sudah digunakan.');
        }

        // Update status user menjadi aktif dan hapus token
        $user->update([
            'status' => 'aktif',
            'token' => null
        ]);

        return redirect()->route('login.lihat')
            ->with('success', 'Akun Anda berhasil diverifikasi! Silakan login untuk melanjutkan.');
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route('login.lihat');
    }
}
