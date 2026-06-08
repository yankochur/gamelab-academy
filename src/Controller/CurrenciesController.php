<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/currencies')]
class CurrenciesController extends AbstractController
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
        private EntityManagerInterface $em,
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $currencies = $this->currencyRepository->findAll();

        return $this->json(array_map(fn(Currency $c) => $this->serialize($c), $currencies));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $currency = $this->currencyRepository->find($id);
        if (!$currency) {
            return $this->json(['error' => 'Currency not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serialize($currency));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        // TODO: реализовать создание валюты
        // Подсказка: посмотри как устроен UsersController::create()
        // Поля: name (string, обязательное), description (string, опциональное), active (bool, default true)
        // Не забудь установить cdate = new \DateTime()
        return $this->json(['error' => 'Not implemented'], Response::HTTP_NOT_IMPLEMENTED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        // TODO: реализовать обновление валюты
        // Подсказка: посмотри как устроен UsersController::update()
        // Можно обновлять поля: name, description, active
        return $this->json(['error' => 'Not implemented'], Response::HTTP_NOT_IMPLEMENTED);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        // TODO: реализовать удаление валюты
        // Подсказка: посмотри как устроен UsersController::delete()
        return $this->json(['error' => 'Not implemented'], Response::HTTP_NOT_IMPLEMENTED);
    }

    private function serialize(Currency $currency): array
    {
        return [
            'id'          => $currency->getId(),
            'name'        => $currency->getName(),
            'description' => $currency->getDescription(),
            'active'      => $currency->isActive(),
            'cdate'       => $currency->getCdate()->format(\DateTime::ATOM),
        ];
    }
}
