<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CorrelationIdMiddleware
{
    public function handle($request, Closure $next)
    {
        // ambil dari header, kalau tidak ada → buat baru
        $correlationId = $request->header('X-Correlation-ID') ?? (string) Str::uuid();

        // simpan ke atribut request
        $request->attributes->set('correlation_id', $correlationId);

        // masukkan ke context logging → otomatis muncul di semua Log::info()
        Log::withContext(['correlation_id' => $correlationId]);

        // proses request
        $response = $next($request);

        // masukkan kembali ke header response
        $response->headers->set('X-Correlation-ID', $correlationId);

        return $response;
    }
}
