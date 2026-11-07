<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public function log(
        string  $event,
        ?string $nip   = null,
        ?string $jti   = null,
        ?int    $tier  = null,
        ?string $query = null,
        ?int    $hitCount = null,
    ): void {
        DB::table('audit_log')->insert([
            'event'     => $event,
            'nip'       => $nip,
            'jti'       => $jti,
            'tier'      => $tier,
            'query'     => $query,
            'hit_count' => $hitCount,
            'ip'        => Request::ip(),
        ]);
    }
}
