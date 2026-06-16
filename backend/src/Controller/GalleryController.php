<?php

namespace App\Controller;

use App\Entity\GalleryImage;
use App\Repository\GalleryImageRepository;
use App\Security\SessionUserProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/galleries')]
final class GalleryController
{
    public function __construct(
        private readonly GalleryImageRepository $images,
        private readonly SessionUserProvider $sessionUsers,
    ) {
    }

    #[Route('', name: 'api_galleries_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $user = $this->requireUser($request);

        if ($user instanceof JsonResponse) {
            return $user;
        }

        return new JsonResponse([
            'images' => array_map(
                static fn (GalleryImage $image) => $image->toArray(),
                $this->images->findBy(['user' => $user], ['id' => 'DESC'])
            ),
        ]);
    }

    #[Route('/{id}', name: 'api_galleries_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, GalleryImage $image): JsonResponse
    {
        $user = $this->requireUser($request);

        if ($user instanceof JsonResponse) {
            return $user;
        }

        if ($image->getUser()->getId() !== $user->getId()) {
            return $this->error('Acces refuse.', Response::HTTP_FORBIDDEN);
        }

        return new JsonResponse(['image' => $image->toArray()]);
    }

    private function requireUser(Request $request): mixed
    {
        $user = $this->sessionUsers->currentUser($request);

        if ($user === null) {
            return $this->error('Non authentifie.', Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->sessionUsers->hasRole($user, 'ROLE_USER')) {
            return $this->error('Acces refuse.', Response::HTTP_FORBIDDEN);
        }

        return $user;
    }

    private function error(string $message, int $status): JsonResponse
    {
        return new JsonResponse(['error' => $message], $status);
    }
}
