<?php

namespace Tests\Feature\Api;

use App\Models\Locale;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Locale $locale;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->locale = Locale::factory()->create(['code' => 'en']);
    }

    public function test_can_create_translation()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/translations', [
                'locale_id' => $this->locale->id,
                'key' => 'test.key',
                'value' => 'Test Value',
                'device_type' => 'desktop',
                'group' => 'general',
                'is_active' => true
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'locale_id',
                    'key',
                    'value',
                    'device_type',
                    'group',
                    'is_active',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_can_update_translation()
    {
        $translation = Translation::factory()->create([
            'locale_id' => $this->locale->id
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/translations/{$translation->id}", [
                'locale_id' => $this->locale->id,
                'key' => 'updated.key',
                'value' => 'Updated Value',
                'device_type' => 'desktop',
                'group' => 'general',
                'is_active' => true
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'locale_id',
                    'key',
                    'value',
                    'device_type',
                    'group',
                    'is_active',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('translations', [
            'id' => $translation->id,
            'key' => 'updated.key',
            'value' => 'Updated Value',
            'device_type' => 'desktop',
            'group' => 'general',
            'is_active' => true
        ]);
    }

    public function test_can_delete_translation()
    {
        $translation = Translation::factory()->create([
            'locale_id' => $this->locale->id
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/translations/{$translation->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('translations', [
            'id' => $translation->id
        ]);
    }

    public function test_can_get_translation()
    {
        $translation = Translation::factory()->create([
            'locale_id' => $this->locale->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/translations/{$translation->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $translation->id,
                'key' => $translation->key,
                'value' => $translation->value
            ]);
    }

    public function test_can_search_translations()
    {
        Translation::factory()->count(3)->create([
            'locale_id' => $this->locale->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/translations/search?key=test');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'per_page',
                'total'
            ]);
    }

    public function test_can_get_translations_by_locale()
    {
        Translation::factory()->count(3)->create([
            'locale_id' => $this->locale->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/translations/locale/{$this->locale->code}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'key',
                    'value'
                ]
            ]);
    }
} 