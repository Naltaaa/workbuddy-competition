<?php

namespace App\Services;

use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use Illuminate\Support\Carbon;

class LoginAttemptManager
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    public function __construct(
        private EmployeeRepository $repository
    ) {}

    public function recordFailure(Employee $employee): void
    {
        $this->repository->incrementFailedCount($employee);

        if ($employee->failed_count >= self::MAX_ATTEMPTS) {
            $this->repository->lockUntil(
                $employee,
                Carbon::now()->addMinutes(self::LOCKOUT_MINUTES)
            );
        }
    }

    public function recordSuccess(Employee $employee): void
    {
        $this->repository->resetFailedCount($employee);
    }
}