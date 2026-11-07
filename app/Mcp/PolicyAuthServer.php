<?php

namespace App\Mcp;

use App\Mcp\Tools\VerifyNipTool;
use Laravel\Mcp\Server;

class PolicyAuthServer extends Server
{
    protected string $name = 'policy-auth';
    protected string $version = '1.0.0';
    protected string $instructions = <<<'MD'
        Server autentikasi karyawan. Gunakan tool verify_nip untuk memverifikasi
        identitas karyawan dan mendapatkan access token. Token berlaku 20 menit.
        Gunakan token tersebut sebagai argumen pada tool di server policy-data.
        MD;

    protected array $tools = [
        VerifyNipTool::class,
    ];
}