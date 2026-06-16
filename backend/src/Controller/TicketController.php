<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use App\Security\SessionUserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/tickets')]
final class TicketController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TicketRepository $tickets,
        private readonly SessionUserProvider $sessionUsers,
    ) {
    }

    #[Route('', name: 'api_tickets_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $user = $this->requireUser($request);

        if ($user instanceof JsonResponse) {
            return $user;
        }

        return new JsonResponse([
            'tickets' => array_map(
                static fn (Ticket $ticket) => $ticket->toArray(),
                $this->tickets->findBy(['user' => $user], ['id' => 'DESC'])
            ),
        ]);
    }

    #[Route('', name: 'api_tickets_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->requireUser($request);

        if ($user instanceof JsonResponse) {
            return $user;
        }

        $data = $this->jsonPayload($request);
        $title = trim((string) ($data['title'] ?? ''));

        if ($title === '') {
            return $this->error('Titre requis.', Response::HTTP_BAD_REQUEST);
        }

        $ticket = (new Ticket())
            ->setUser($user)
            ->setTitle($title)
            ->setContent(isset($data['content']) ? (string) $data['content'] : null);

        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        return new JsonResponse(['ticket' => $ticket->toArray()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_tickets_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, Ticket $ticket): JsonResponse
    {
        $user = $this->requireUser($request);

        if ($user instanceof JsonResponse) {
            return $user;
        }

        if ($ticket->getUser()->getId() !== $user->getId()) {
            return $this->error('Acces refuse.', Response::HTTP_FORBIDDEN);
        }

        return new JsonResponse(['ticket' => $ticket->toArray()]);
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
