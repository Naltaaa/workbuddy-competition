<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $hashPin = fn (string $rawPin): string => Hash::make($rawPin, ['rounds' => 12]);
        $now = now();

        $pins = [
            '10001' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            '20001' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            '30001' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
        ];

        $this->command->info('PIN yang dihasilkan (simpan sekarang, tidak akan ditampilkan lagi):');
        foreach ($pins as $nip => $pin) {
            $this->command->info("  NIP $nip → PIN: $pin");
        }

        DB::table('employees')->truncate();

        DB::table('employees')->insert([
            [
                'nip'          => '10001',
                'nama'         => 'Karyawan',
                'pin_hash'     => $hashPin($pins['10001']),
                'tier'         => 1,
                'aktif'        => true,
                'failed_count' => 0,
                'locked_until' => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'nip'          => '20001',
                'nama'         => 'HR',
                'pin_hash'     => $hashPin($pins['20001']),
                'tier'         => 2,
                'aktif'        => true,
                'failed_count' => 0,
                'locked_until' => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'nip'          => '30001',
                'nama'         => 'Manager',
                'pin_hash'     => $hashPin($pins['30001']),
                'tier'         => 3,
                'aktif'        => true,
                'failed_count' => 0,
                'locked_until' => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ]);
    }
}