<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        Log::info('OrderService: index called');

        // Ambil correlation ID dari request
        $correlationId = request()->attributes->get('correlation_id');

        Log::info("OrderService: forwarding request to UserService", [
            'correlation_id' => $correlationId
        ]);

        // Kirim correlation ID ke User Service
        $response = Http::withHeaders([
            'X-Correlation-ID' => $correlationId
        ])->get('http://127.0.0.1:8001/api/users');

        if ($response->failed()) {
            Log::error("OrderService: UserService unreachable");
            return response()->json(['error' => 'User Service Unavailable'], 503);
        }

        Log::info("OrderService: received response from UserService");

        $users = $response->json();

        $orders = Order::all();

        // Gabung order dengan user
        $merged = collect($orders)->map(function ($order) use ($users) {
            $order['user'] = collect($users)->firstWhere('id', $order->user_id);
            return $order;
        });

        return response()->json($merged);
    }

    public function store(Request $request)
    {
        Log::info('Request masuk ke OrderService - store()', [
            'user_id' => $request->user_id,
            'item' => $request->item,
            'qty' => $request->quantity,
        ]);

        try {
            $order = Order::create([
                'user_id' => $request->user_id,
                'item' => $request->item,
                'quantity' => $request->quantity,
            ]);

            Log::info('Order berhasil dibuat', ['order_id' => $order->id]);

            return response()->json($order, 201);
        } catch (\Exception $e) {
            Log::error('Gagal membuat order', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal membuat order'], 500);
        }
    }
    public function update(Request $request, $id)
    {
        Log::info('Request update order', [
            'order_id' => $id,
            'data' => $request->all()
        ]);

        try {
            $order = Order::find($id);

            if (!$order) {
                Log::warning("Order dengan ID $id tidak ditemukan");
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Update data order
            $order->update([
                'user_id' => $request->user_id ?? $order->user_id,
                'item' => $request->item ?? $order->item,
                'quantity' => $request->quantity ?? $order->quantity,
            ]);

            Log::info("Order ID $id berhasil diupdate");

            return response()->json($order, 200);
        } catch (\Exception $e) {
            Log::error("Gagal mengupdate order ID $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal mengupdate order'], 500);
        }
    }

    public function destroy($id)
    {
        Log::info("Request delete order ID $id");

        try {
            $order = Order::find($id);

            if (!$order) {
                Log::warning("Order dengan ID $id tidak ditemukan");
                return response()->json(['error' => 'Order not found'], 404);
            }

            $order->delete();

            Log::info("Order ID $id berhasil dihapus");

            return response()->json(['message' => 'Order berhasil dihapus'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus order ID $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal menghapus order'], 500);
        }
    }
}
