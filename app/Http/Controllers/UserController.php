<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;


class UserController extends Controller
{
    public function index()
    {
        // Log bahwa User Service menerima request
        Log::info('UserService: request masuk ke index() untuk mengambil daftar user');

        $users = User::all();

        Log::info('UserService: Berhasil mengembalikan data user', [
            'total' => count($users)
        ]);

        Log::info('User list requested');
        return response()->json($users);
    }

    public function store(Request $request)
    {
        Log::info('UserService: request masuk ke store()', [
            'name' => $request->name,
            'email' => $request->email
        ]);

        try {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            Log::info('UserService: user berhasil dibuat', [
                'user_id' => $user->id
            ]);

            return response()->json($user, 201);
        } catch (\Exception $e) {

            // Jika gagal insert
            Log::error('UserService: gagal membuat user', [
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Gagal membuat user'], 500);
        }
    }
    public function update(Request $request, $id)
    {
        Log::info('UserService: request masuk ke update()', [
            'user_id' => $id,
            'name' => $request->name,
            'email' => $request->email
        ]);

        try {

            $user = User::find($id);

            if (!$user) {
                Log::warning('UserService: user tidak ditemukan untuk update', [
                    'user_id' => $id
                ]);

                return response()->json(['error' => 'User tidak ditemukan'], 404);
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            Log::info('UserService: user berhasil diupdate', [
                'user_id' => $user->id
            ]);

            return response()->json($user);
        } catch (\Exception $e) {

            Log::error('UserService: gagal update user', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Gagal update user'], 500);
        }
    }
    public function destroy($id)
    {
        Log::info('UserService: request masuk ke destroy()', [
            'user_id' => $id
        ]);

        try {

            $user = User::find($id);

            if (!$user) {
                Log::warning('UserService: user tidak ditemukan untuk delete', [
                    'user_id' => $id
                ]);

                return response()->json(['error' => 'User tidak ditemukan'], 404);
            }

            $user->delete();

            Log::info('UserService: user berhasil dihapus', [
                'user_id' => $id
            ]);

            return response()->json(['message' => 'User berhasil dihapus']);
        } catch (\Exception $e) {

            Log::error('UserService: gagal menghapus user', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Gagal menghapus user'], 500);
        }
    }
}
