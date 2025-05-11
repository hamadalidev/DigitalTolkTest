<?php

namespace Tests\Unit\Services;

use App\DTOs\TranslationDTO;
use App\Repositories\Interfaces\TranslationRepositoryInterface;
use App\Services\TranslationService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    private TranslationService $service;
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(TranslationRepositoryInterface::class);
        $this->service = new TranslationService($this->repository);
    }

    public function test_create_translation()
    {
        $dto = new TranslationDTO(
            id: null,
            locale_id: 1,
            key: 'test.key',
            value: 'Test Value',
            group: 'test'
        );

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with($dto)
            ->andReturn($dto);

        $result = $this->service->createTranslation($dto);

        $this->assertEquals($dto, $result);
    }

    public function test_update_translation()
    {
        $dto = new TranslationDTO(
            id: 1,
            locale_id: 1,
            key: 'test.key',
            value: 'Updated Value',
            group: 'test'
        );

        $this->repository
            ->shouldReceive('update')
            ->once()
            ->with(1, $dto)
            ->andReturn($dto);

        $result = $this->service->updateTranslation(1, $dto);

        $this->assertEquals($dto, $result);
    }

    public function test_delete_translation()
    {
        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $result = $this->service->deleteTranslation(1);

        $this->assertTrue($result);
    }

    public function test_get_translation()
    {
        $dto = new TranslationDTO(
            id: 1,
            locale_id: 1,
            key: 'test.key',
            value: 'Test Value',
            group: 'test'
        );

        $this->repository
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($dto);

        $result = $this->service->getTranslation(1);

        $this->assertEquals($dto, $result);
    }

    public function test_search_translations()
    {
        $filters = ['key' => 'test'];
        $paginator = new LengthAwarePaginator([], 0, 15);

        $this->repository
            ->shouldReceive('search')
            ->once()
            ->with($filters)
            ->andReturn($paginator);

        $result = $this->service->searchTranslations($filters);

        $this->assertEquals($paginator, $result);
    }

    public function test_get_translations_by_locale()
    {
        $translations = ['test.key' => 'Test Value'];

        $this->repository
            ->shouldReceive('getByLocale')
            ->once()
            ->with('en', null)
            ->andReturn($translations);

        $result = $this->service->getTranslationsByLocale('en');

        $this->assertEquals($translations, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 