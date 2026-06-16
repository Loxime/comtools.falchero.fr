<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\SessionUserProvider;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class AuthController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $users,
        private readonly SessionUserProvider $sessionUsers,
    ) {
    }

    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = $this->jsonPayload($request);
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $password = (string) ($data['password'] ?? '');
        $username = isset($data['username']) ? (string) $data['username'] : null;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->error('Email invalide.', Response::HTTP_BAD_REQUEST);
        }

        if (!$this->isValidPassword($password)) {
            return $this->error('Le mot de passe doit contenir au moins 8 caracteres, 1 majuscule et 1 chiffre.', Response::HTTP_BAD_REQUEST);
        }

        if ($this->users->findOneBy(['email' => $email]) !== null) {
            return $this->error('Cet email existe deja.', Response::HTTP_CONFLICT);
        }

        $user = (new User())
            ->setEmail($email)
            ->setPassword(password_hash($password, PASSWORD_BCRYPT))
            ->setUsername($username);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException) {
            return $this->error('Cet email existe deja.', Response::HTTP_CONFLICT);
        }

        return new JsonResponse(['user' => $user->toArray()], Response::HTTP_CREATED);
    }

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = $this->jsonPayload($request);
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $password = (string) ($data['password'] ?? '');
        $user = $this->users->findOneBy(['email' => $email]);

        if ($user === null || !$user->isActive() || !password_verify($password, $user->getPassword())) {
            return $this->error('Identifiants invalides.', Response::HTTP_UNAUTHORIZED);
        }

        $session = $request->getSession();
        $session->migrate(true);
        $session->set('user_id', $user->getId());

        return new JsonResponse(['user' => $user->toArray()]);
    }

    #[Route('/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        if ($request->hasSession()) {
            $request->getSession()->invalidate();
        }

        return new JsonResponse(['message' => 'Deconnecte.']);
    }

    #[Route('/me', name: 'api_me', methods: ['GET'])]
    public function me(Request $request): JsonResponse
    {
        $user = $this->sessionUsers->currentUser($request);

        if ($user === null) {
            return $this->error('Non authentifie.', Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->sessionUsers->hasRole($user, 'ROLE_USER')) {
            return $this->error('Acces refuse.', Response::HTTP_FORBIDDEN);
        }

        return new JsonResponse(['user' => $user->toArray()]);
    }

    private function jsonPayload(Request $request): array
    {
        $data = json_decode($request->getContent(), true);

        return is_array($data) ? $data : [];
    }

    private function isValidPassword(string $password): bool
    {
        return strlen($password) >= 8
            && preg_match('/[A-Z]/', $password) === 1
            && preg_match('/\d/', $password) === 1;
    }

    private function error(string $message, int $status): JsonResponse
    {
        return new JsonResponse(['error' => $message], $status);
    }
}
