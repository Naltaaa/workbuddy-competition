<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

beforeEach(function () {
    DB::table('employees')->insert([
        'nip'          => '10001',
        'nama'         => 'Staf Ujicoba',
        'pin_hash'     => Hash::make('1234', ['rounds' => 4]),
        'tier'         => 1,
        'aktif'        => true,
        'failed_count' => 0,
        'locked_until' => null,
        'created_at'   => now(),
        'updated_at'   => now(),
    ]);
    Config::set('app.bridge_key', 'test-bridge-key-untuk-testing');
    $this->headers = ['X-Bridge-Key' => 'test-bridge-key-untuk-testing'];
});

test('endpoint mengembalikan token saat login berhasil', function () use (&$headers) {
    $response = $this->postJson('/api/mcp/verify', [
        'nip' => '10001',
        'pin' => '1234',
    ], $this->headers);

    $response->assertStatus(200)
        ->assertJson(['ok' => true])
        ->assertJsonStructure(['ok', 'nama', 'token', 'expires_in'])
        ->assertHeader('Cache-Control', 'no-store, private');
});

test('endpoint menolak tanpa bridge key', function () {
    $response = $this->postJson('/api/mcp/verify', [
        'nip' => '10001',
        'pin' => '1234',
    ]);

    $response->assertStatus(401)
        ->assertJson(['ok' => false, 'reason' => 'auth']);
});

test('endpoint menolak bridge key salah', function () {
    $response = $this->postJson('/api/mcp/verify', [
        'nip' => '10001',
        'pin' => '1234',
    ], ['X-Bridge-Key' => 'wrong-key']);

    $response->assertStatus(401);
});

test('endpoint validasi input nip kosong', function () use (&$headers) {
    $response = $this->postJson('/api/mcp/verify', [
        'pin' => '1234',
    ], $this->headers);

    $response->assertStatus(422);
});

test('endpoint validasi pin terlalu pendek', function () use (&$headers) {
    $response = $this->postJson('/api/mcp/verify', [
        'nip' => '10001',
        'pin' => '12',
    ], $this->headers);

    $response->assertStatus(422);
});

test('response tidak mengandung pin', function () use (&$headers) {
    $response = $this->postJson('/api/mcp/verify', [
        'nip' => '10001',
        'pin' => '1234',
    ], $this->headers);

    expect($response->getContent())->not->toContain('1234');
});