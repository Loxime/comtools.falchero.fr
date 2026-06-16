<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ApiStatusController
{
    #[Route('/api/status', name: 'api_status', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([
            'application' => 'ComTools',
            'status' => 'ok',
        ]);
    }
}
