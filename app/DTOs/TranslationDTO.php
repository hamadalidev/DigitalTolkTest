<?php

namespace App\DTOs;

class TranslationDTO extends BaseDTO
{
    public function __construct(
        public ?int $id = null,
        public int $locale_id,
        public string $key,
        public string $value,
        public string $device_type = 'desktop',
        public string $group = 'general',
        public bool $is_active = true,
    ) {}
} 