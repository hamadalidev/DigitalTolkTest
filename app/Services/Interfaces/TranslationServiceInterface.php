<?php

namespace App\Services\Interfaces;

use App\DTOs\TranslationDTO;
use Illuminate\Pagination\LengthAwarePaginator;

interface TranslationServiceInterface
{
    public function createTranslation(TranslationDTO $dto): TranslationDTO;
    public function updateTranslation(int $id, TranslationDTO $dto): TranslationDTO;
    public function deleteTranslation(int $id): bool;
    public function getTranslation(int $id): ?TranslationDTO;
    public function searchTranslations(array $filters): LengthAwarePaginator;
    public function getTranslationsByLocale(string $localeCode, ?string $deviceType = null): array;
} 