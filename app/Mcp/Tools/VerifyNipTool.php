<?php

namespace App\Mcp\Tools;

use App\Services\AuthenticationService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class VerifyNipTool extends Tool
{
    protected string $name = 'verify_nip';
    protected string $title = 'Verify NIP';

    public function __construct(
        private AuthenticationService $auth
    ) {}

    public function description(): string
    {
        return 'Verifikasi NIP dan PIN karyawan. Mengembalikan JWT access token jika berhasil. '
            . 'Gunakan token ini sebagai argumen pada tool buku_menu dan cari_data di server policy-data.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'nip' => $schema->string()->min(1)->max(32)->description('Nomor Induk Pegawai'),
            'pin' => $schema->string()->min(4)->max(32)->description('PIN karyawan (4-32 karakter)'),
        ];
    }

    public function handle(Request $request): Response
        {
        $request->validate([
            'nip' => ['required', 'string', 'min:1', 'max:32'],
            'pin' => ['required', 'string', 'min:4', 'max:32'],
        ]);

        $result = $this->auth->verify(
            (string) $request->get('nip'),
            (string) $request->get('pin'),
        );

        return Response::text(json_encode($result, JSON_UNESCAPED_UNICODE));
    }
}