<?php

namespace App\Services;

use App\Repositories\EmployeeRepository;
use Illuminate\Support\Facades\Hash;

class AuthenticationService
{
    public function __construct(
        private EmployeeRepository  $employees,
        private LoginAttemptManager $attempts,
        private TokenService        $tokens,
        private AuditLogger         $audit,
    ) {}

    public function verify(string $nip, string $pin): array
    {
        $employee = $this->employees->findByNip($nip);

        // NIP tidak ditemukan — response identik dengan kegagalan lain
        // agar tidak membocorkan apakah NIP terdaftar atau tidak
        if ($employee === null) {
            $this->audit->log('verify_fail');
            return $this->fail();
        }

        // Akun terkunci
        if ($employee->isLocked()) {
            $this->audit->log('verify_fail', $employee->nip, tier: $employee->tier);
            return $this->fail();
        }

        // Akun nonaktif
        if (! $employee->aktif) {
            $this->audit->log('verify_fail', $employee->nip, tier: $employee->tier);
            return $this->fail();
        }

        // PIN salah — pin_hash tidak boleh muncul di log
        if (! Hash::check($pin, $employee->pin_hash)) {
            $this->attempts->recordFailure($employee);
            $this->audit->log('verify_fail', $employee->nip, tier: $employee->tier);
            return $this->fail();
        }

        // Berhasil
        $this->attempts->recordSuccess($employee);
        $issued = $this->tokens->issue($employee);
        $this->audit->log('verify_ok', $employee->nip, $issued['jti'], $employee->tier);

        return [
            'ok'         => true,
            'nama'       => $employee->nama,
            'token'      => $issued['token'],
            'expires_in' => $issued['expires_in'],
        ];
    }

    private function fail(): array
    {
        return ['ok' => false, 'reason' => 'auth'];
    }
}