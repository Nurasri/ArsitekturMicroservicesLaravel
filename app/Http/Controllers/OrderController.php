<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function index()
    {
        // Ambil data user dari User Service (API Provider)
        $response = Http::get('http://127.0.0.1:8001/api/users');

        // Jika gagal menghubungi user service
        if ($response->failed()) {
            return response()->json(['error' => 'User Service Unavailable'], 503);
        }

        // Data user hasil dari API
        $users = $response->json();

        // Data order (dummy)
        $orders = [
            ['id' => 101, 'user_id' => 1, 'item' => 'Laptop', 'quantity' => 1],
            ['id' => 102, 'user_id' => 2, 'item' => 'Keyboard', 'quantity' => 2],
        ];

        // Gabungkan data order dan data user berdasarkan user_id
        $merged = collect($orders)->map(function ($order) use ($users) {
            $user = collect($users)->firstWhere('id', $order['user_id']);
            $order['user'] = $user;
            return $order;
        });

        // Kembalikan hasil gabungan dalam format JSON
        return response()->json($merged);
    }
}
