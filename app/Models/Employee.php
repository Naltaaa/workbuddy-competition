<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $primaryKey = 'nip';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nip',
        'nama',
        'pin_hash',
        'tier',
        'aktif',
        'failed_count',
        'locked_until',
    ];

    protected $hidden = [
        'pin_hash', // tidak boleh muncul di response/log apapun
    ];

    protected $casts = [
        'aktif'        => 'boolean',
        'tier'         => 'integer',
        'failed_count' => 'integer',
        'locked_until' => 'datetime',
    ];

    public function isLocked(): bool
    {
        return $this->locked_until !== null
            && $this->locked_until->isFuture();
    }
}