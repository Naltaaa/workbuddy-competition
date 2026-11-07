<?php

namespace App\Services;

use App\Models\Employee;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;

class TokenService
{
    private string $privateKey;
    private string $publicKey;

    public function __construct()
    {
        $this->privateKey = file_get_contents(config('jwt.private_key_path'));
        $this->publicKey  = file_get_contents(config('jwt.public_key_path'));
    }

    public function issue(Employee $employee): array
    {
        $now = time();
        $jti = Str::lower(Str::random(12));

        $roleMap = [1 => 'employee', 2 => 'hr', 3 => 'manager'];

        $payload = [
            'iss'       => config('jwt.issuer'),
            'aud'       => config('jwt.audience'),
            'sub'       => $employee->nip,
            'tier'      => $employee->tier,
            'role'      => $roleMap[$employee->tier] ?? 'employee',
            'jti'       => $jti,
            'auth_time' => $now,
            'iat'       => $now,
            'exp'       => $now + config('jwt.expiry_seconds'),
        ];

        // Token tidak boleh dicatat ke log — kembalikan via array saja
        $token = JWT::encode($payload, $this->privateKey, 'RS256');

        return [
            'token'      => $token,
            'jti'        => $jti,
            'expires_in' => config('jwt.expiry_seconds'),
        ];
    }

    public function verify(string $token): object
    {
        return JWT::decode($token, new Key($this->publicKey, 'RS256'));
    }
}