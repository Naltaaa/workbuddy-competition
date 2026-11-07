<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyRequest;
use App\Services\AuthenticationService;
use Illuminate\Http\JsonResponse;

class VerifyController extends Controller
{
    public function __construct(
        private AuthenticationService $auth
    ) {}

    public function __invoke(VerifyRequest $request): JsonResponse
    {
        $result = $this->auth->verify(
            $request->input('nip'),
            $request->input('pin'),
        );

        return response()
            ->json($result)
            ->header('Cache-Control', 'no-store');
    }
}