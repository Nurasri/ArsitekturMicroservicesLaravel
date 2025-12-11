<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CorrelationIdMiddleware
{
    public function handle($request, Closure $next)
    {
        // Ambil dari header jika ada, jika tidak buat baru
        $correlationId = $request->header('X-Correlation-ID') ?? (string) Str::uuid();

        // Simpan ke request agar bisa dipakai controller
        $request->attributes->set('correlation_id', $correlationId);

        // Inject correlation_id ke semua log
        Log::withContext([
            'correlation_id' => $correlationId
        ]);

        // Lanjut request
        $response = $next($request);

        // Tambah ke header response
        $response->headers->set('X-Correlation-ID', $correlationId);

        return $response;
    }
}
