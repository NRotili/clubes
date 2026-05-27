<?php

namespace App\Services;

use App\Models\Socio;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private const ENDPOINT = 'https://exp.host/--/api/v2/push/send';

    public static function enviarAlSocio(Socio $socio, string $title, string $body, array $data = []): void
    {
        $token = $socio->usuario?->expo_push_token;
        if (!$token || !str_starts_with($token, 'ExponentPushToken[')) return;

        self::send($token, $title, $body, $data);
    }

    public static function enviarAVarios(array $tokens, string $title, string $body, array $data = []): void
    {
        $tokens = array_values(array_filter($tokens, fn($t) => str_starts_with((string) $t, 'ExponentPushToken[')));
        if (empty($tokens)) return;

        $messages = array_map(fn($token) => [
            'to'    => $token,
            'title' => $title,
            'body'  => $body,
            'data'  => $data,
        ], $tokens);

        try {
            Http::withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(self::ENDPOINT, $messages);
        } catch (\Throwable $e) {
            Log::warning('ExpoPush batch error: ' . $e->getMessage());
        }
    }

    private static function send(string $token, string $title, string $body, array $data = []): void
    {
        try {
            $response = Http::withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(self::ENDPOINT, [
                'to'    => $token,
                'title' => $title,
                'body'  => $body,
                'data'  => $data,
            ]);
            Log::info('ExpoPush response: ' . $response->body());
        } catch (\Throwable $e) {
            Log::warning('ExpoPush error: ' . $e->getMessage());
        }
    }
}
