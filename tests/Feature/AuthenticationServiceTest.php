<?php

use App\Models\Employee;
use App\Services\AuthenticationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->auth = app(AuthenticationService::class);

    Employee::create([
        'nip'          => '10001',
        'nama'         => 'Staf Ujicoba',
        'pin_hash'     => Hash::make('1234', ['rounds' => 4]),
        'tier'         => 1,
        'aktif'        => true,
        'failed_count' => 0,
        'locked_until' => null,
    ]);

    Employee::create([
        'nip'          => '10002',
        'nama'         => 'Akun Nonaktif',
        'pin_hash'     => Hash::make('1234', ['rounds' => 4]),
        'tier'         => 1,
        'aktif'        => false,
        'failed_count' => 0,
        'locked_until' => null,
    ]);
});

test('login berhasil dengan nip dan pin valid', function () {
    $result = $this->auth->verify('10001', '1234');

    expect($result['ok'])->toBeTrue();
    expect($result['nama'])->toBe('Staf Ujicoba');
    expect($result)->toHaveKey('token');
    expect($result)->toHaveKey('expires_in');
});

test('login gagal dengan pin salah', function () {
    $result = $this->auth->verify('10001', '9999');

    expect($result)->toBe(['ok' => false, 'reason' => 'auth']);
});

test('login gagal dengan nip tidak ditemukan', function () {
    $result = $this->auth->verify('99999', '1234');

    expect($result)->toBe(['ok' => false, 'reason' => 'auth']);
});

test('login gagal dengan akun nonaktif', function () {
    $result = $this->auth->verify('10002', '1234');

    expect($result)->toBe(['ok' => false, 'reason' => 'auth']);
});

test('akun terkunci setelah 5 kegagalan', function () {
    foreach (range(1, 5) as $i) {
        $this->auth->verify('10001', '9999');
    }

    $result = $this->auth->verify('10001', '1234');

    expect($result)->toBe(['ok' => false, 'reason' => 'auth']);
});

test('response kegagalan selalu identik', function () {
    $wrongPin    = $this->auth->verify('10001', '9999');
    $unknownNip  = $this->auth->verify('99999', '1234');
    $inactive    = $this->auth->verify('10002', '1234');

    expect($wrongPin)->toBe($unknownNip)->toBe($inactive);
});

test('pin tidak muncul dalam response', function () {
    $result = $this->auth->verify('10001', '1234');

    expect(json_encode($result))->not->toContain('1234');
    expect(json_encode($result))->not->toContain('pin');
});