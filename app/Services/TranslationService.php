<?php

namespace App\Services;

use App\DTOs\TranslationDTO;
use App\Repositories\Interfaces\TranslationRepositoryInterface;
use App\Services\Interfaces\TranslationServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TranslationService implements TranslationServiceInterface
{
    public function __construct(
        private readonly TranslationRepositoryInterface $repository
    ) {}

    public function createTranslation(TranslationDTO $dto): TranslationDTO
    {
        return $this->repository->create($dto);
    }

    public function updateTranslation(int $id, TranslationDTO $dto): TranslationDTO
    {
        return $this->repository->update($id, $dto);
    }

    public function deleteTranslation(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getTranslation(int $id): ?TranslationDTO
    {
        return $this->repository->find($id);
    }

    public function searchTranslations(array $filters): LengthAwarePaginator
    {
        return $this->repository->search($filters);
    }

    public function getTranslationsByLocale(string $localeCode, ?string $deviceType = null): array
    {
        return $this->repository->getByLocale($localeCode, $deviceType);
    }
} 