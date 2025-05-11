<?php

namespace App\Repositories;

use App\DTOs\TranslationDTO;
use App\Models\Locale;
use App\Models\Translation;
use App\Repositories\Interfaces\TranslationRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TranslationRepository implements TranslationRepositoryInterface
{
    public function __construct(
        private readonly Translation $model
    ) {}

    public function create(TranslationDTO $dto): TranslationDTO
    {
        $translation = $this->model->create($dto->toArray());
        $this->clearCache($translation->locale->code);
        return $this->toDTO($translation);
    }

    public function update(int $id, TranslationDTO $dto): TranslationDTO
    {
        $translation = $this->model->findOrFail($id);
        $translation->update($dto->toArray());
        $this->clearCache($translation->locale->code);
        return $this->toDTO($translation);
    }

    public function delete(int $id): bool
    {
        $translation = $this->model->findOrFail($id);
        $localeCode = $translation->locale->code;
        $result = $translation->delete();
        
        if ($result) {
            $this->clearCache($localeCode);
        }
        
        return $result;
    }

    public function find(int $id): ?TranslationDTO
    {
        $translation = $this->model->with(['locale'])->find($id);
        return $translation ? $this->toDTO($translation) : null;
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $query = $this->model->with(['locale']);

        if (isset($filters['key'])) {
            $query->where('key', 'like', "%{$filters['key']}%");
        }

        if (isset($filters['value'])) {
            $query->where('value', 'like', "%{$filters['value']}%");
        }

        if (isset($filters['locale_id'])) {
            $query->where('locale_id', $filters['locale_id']);
        }

        if (isset($filters['device_type'])) {
            $query->where('device_type', $filters['device_type']);
        }

        if (isset($filters['group'])) {
            $query->where('group', $filters['group']);
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function getByLocale(string $localeCode, ?string $deviceType = null): array
    {
        $cacheKey = "translations:{$localeCode}" . ($deviceType ? ":{$deviceType}" : '');
        
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($localeCode, $deviceType) {
            $query = $this->model->with(['locale'])
                ->whereHas('locale', function ($q) use ($localeCode) {
                    $q->where('code', $localeCode);
                });

            if ($deviceType) {
                $query->where('device_type', $deviceType);
            }

            $translations = $query->get();
            
            return $translations->mapWithKeys(function ($translation) {
                return [$translation->key => $translation->value];
            })->toArray();
        });
    }

    private function toDTO(Translation $translation): TranslationDTO
    {
        return new TranslationDTO(
            id: $translation->id,
            locale_id: $translation->locale_id,
            key: $translation->key,
            value: $translation->value,
            device_type: $translation->device_type,
            group: $translation->group,
            is_active: $translation->is_active
        );
    }

    private function clearCache(string $localeCode): void
    {
        Cache::forget("translations:{$localeCode}");
        Cache::forget("translations:{$localeCode}:mobile");
        Cache::forget("translations:{$localeCode}:tablet");
        Cache::forget("translations:{$localeCode}:desktop");
    }
} 