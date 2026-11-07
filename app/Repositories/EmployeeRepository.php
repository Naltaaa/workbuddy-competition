<?php

namespace App\Repositories;

use App\Models\Employee;

class EmployeeRepository
{
    public function findByNip(string $nip): ?Employee
    {
        return Employee::where('nip', $nip)->first();
    }

    public function incrementFailedCount(Employee $employee): void
    {
        $employee->increment('failed_count');
        $employee->refresh();
    }

    public function lockUntil(Employee $employee, \DateTimeInterface $until): void
    {
        $employee->update([
            'locked_until' => $until,
        ]);
    }

    public function resetFailedCount(Employee $employee): void
    {
        $employee->update([
            'failed_count' => 0,
            'locked_until' => null,
        ]);
    }
}