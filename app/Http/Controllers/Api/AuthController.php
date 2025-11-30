<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    // REGISTER KHUSUS KASIR
    public function registerCashier(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'phone'       => 'required|string|max:20|unique:users,phone',
            'birth_date'  => 'required|date',
            'password'    => ['required', 'confirmed', Password::min(6)],
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        $user = User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'],
            'birth_date'    => $validated['birth_date'],
            'profile_photo' => $path,
            'password'      => Hash::make($validated['password']),
            'role'          => 'cashier',
            'status'        => 'pending', // penting: butuh approve admin dulu
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil. Menunggu persetujuan admin.',
            'data'    => $user,
        ], 201);
    }

    // LOGIN (ADMIN & KASIR — tapi flutter hanya role cashier)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah.'
            ], 401);
        }

        // Kalau kasir masih pending / rejected
        if ($user->role === 'cashier' && $user->status !== 'approved') {
            return response()->json([
                'message' => 'Akun Anda belum di-approve admin.',
                'status'  => $user->status,
            ], 403);
        }

        // Hapus token lama (optional)
        $user->tokens()->delete();

        $token = $user->createToken('kasir-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        // Optional: kalau mau kirim url foto (bukan cuma path)
        if ($user->profile_photo) {
            $user->profile_photo_url = Storage::url($user->profile_photo);
        }

        return response()->json($user);
    }

    // ==========================
    // UPDATE PROFIL (PUT /auth/me)
    // ==========================
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'phone'      => 'required|string|max:20|unique:users,phone,' . $user->id,
            'birth_date' => 'nullable|date',
        ]);

        $user->name       = $validated['name'];
        $user->email      = $validated['email'];
        $user->phone      = $validated['phone'];
        $user->birth_date = $validated['birth_date'] ?? null;
        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'data'    => $user,
        ]);
    }

    // ==================================
    // UPDATE FOTO PROFIL (POST /auth/profile/photo)
    // ==================================
    public function updatePhoto(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            // Flutter kirim field "photo" di Multipart
            'photo' => 'required|image|max:2048',
        ]);

        // hapus foto lama kalau ada
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('photo')->store('profile_photos', 'public');
        $user->profile_photo = $path;
        $user->save();

        return response()->json([
            'message' => 'Foto profil berhasil diperbarui.',
            'data'    => [
                // kirim url supaya bisa langsung dipakai di NetworkImage
                'profile_photo' => Storage::url($path),
            ],
        ]);
    }

    // ==================================
    // HAPUS FOTO PROFIL (DELETE /auth/profile/photo)
    // ==================================
    public function deletePhoto(Request $request)
    {
        $user = $request->user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->profile_photo = null;
            $user->save();
        }

        return response()->json([
            'message' => 'Foto profil berhasil dihapus.',
        ]);
    }
}
