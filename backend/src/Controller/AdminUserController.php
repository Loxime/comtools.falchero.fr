<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Security\SessionUserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/users')]
final class AdminUserController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $users,
        private readonly SessionUserProvider $sessionUsers,
    ) {
    }

    #[Route('', name: 'api_admin_users_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        if (($response = $this->requireAdmin($request)) !== null) {
            return $response;
        }

        return new JsonResponse([
            'users' => array_map(
                static fn ($user) => $user->toArray(),
                $this->users->findBy([], ['id' => 'ASC'])
            ),
        ]);
    }

    #[Route('/{id}', name: 'api_admin_users_update', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    public function update(Request $request, int $id): JsonResponse
    {
        if (($response = $this->requireAdmin($request)) !== null) {
            return $response;
        }

        $user = $this->users->find($id);

        if ($user === null) {
            return $this->error('Utilisateur introuvable.', Response::HTTP_NOT_FOUND);
        }

        $data = $this->jsonPayload($request);

        if (array_key_exists('isActive', $data)) {
            $user->setIsActive((bool) $data['isActive']);
        }

        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = array_values(array_intersect($data['roles'], ['ROLE_USER', 'ROLE_ADMIN']));
            $user->setRoles($roles === [] ? ['ROLE_USER'] : $roles);
        }

        $this->entityManager->flush();

        return new JsonResponse(['user' => $user->toArray()]);
    }

    #[Route('/{id}', name: 'api_admin_users_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        $currentUser = $this->sessionUsers->currentUser($request);

        if ($currentUser === null) {
            return $this->error('Non authentifie.', Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->sessionUsers->hasRole($currentUser, 'ROLE_ADMIN')) {
            return $this->error('Acces refuse.', Response::HTTP_FORBIDDEN);
        }

        $user = $this->users->find($id);

        if ($user === null) {
            return $this->error('Utilisateur introuvable.', Response::HTTP_NOT_FOUND);
        }

        if ($user->getId() === $currentUser->getId()) {
            return $this->error('Impossible de supprimer son propre compte.', Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    private function requireAdmin(Request $request): ?JsonResponse
    {
        $user = $this->sessionUsers->currentUser($request);

        if ($user === null) {
            return $this->error('Non authentifie.', Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->sessionUsers->hasRole($user, 'ROLE_ADMIN')) {
            return $this->error('Acces refuse.', Response::HTTP_FORBIDDEN);
        }

        return null;
    }

    private function jsonPayload(Request $request): array
    {
        $data = json_decode($request->getContent(), true);

        return is_array($data) ? $data : [];
    }

    private function error(string $message, int $status): JsonResponse
    {
        return new JsonResponse(['error' => $message], $status);
    }
}
