<?php

namespace App\Repositories\Interfaces;

use App\DTOs\TranslationDTO;
use Illuminate\Pagination\LengthAwarePaginator;

interface TranslationRepositoryInterface
{
    public function create(TranslationDTO $dto): TranslationDTO;
    public function update(int $id, TranslationDTO $dto): TranslationDTO;
    public function delete(int $id): bool;
    public function find(int $id): ?TranslationDTO;
    public function search(array $filters): LengthAwarePaginator;
    public function getByLocale(string $localeCode, ?string $deviceType = null): array;
}
