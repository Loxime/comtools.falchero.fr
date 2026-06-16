<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

final class SessionUserProvider
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    public function currentUser(Request $request): ?User
    {
        if (!$request->hasSession()) {
            return null;
        }

        $userId = $request->getSession()->get('user_id');
        $user = is_int($userId) || (is_string($userId) && ctype_digit($userId))
            ? $this->users->find((int) $userId)
            : null;

        return $user?->isActive() === true ? $user : null;
    }

    public function hasRole(User $user, string $role): bool
    {
        return in_array($role, $user->getRoles(), true);
    }
}
