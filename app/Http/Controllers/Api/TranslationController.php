<?php

namespace App\Http\Controllers\Api;

use App\DTOs\TranslationDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\TranslationRequest;
use App\Http\Resources\TranslationResource;
use App\Services\Interfaces\TranslationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Translation Management API",
 *     description="API for managing translations with device-specific content for multi-language support",
 *     @OA\Contact(
 *         email="admin@example.com",
 *         name="API Support",
 *         url="https://www.example.com/support"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="https://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="JWT token authentication"
 * )
 *
 * @OA\Tag(
 *     name="Translations",
 *     description="API endpoints for managing translations"
 * )
 *
 * @OA\Schema(
 *     schema="Translation",
 *     type="object",
 *     title="Translation Model",
 *     description="Translation resource representation",
 *     @OA\Property(property="id", type="integer", format="int64", example=1, description="Unique identifier"),
 *     @OA\Property(property="locale_id", type="integer", format="int64", example=1, description="Locale identifier"),
 *     @OA\Property(property="key", type="string", example="welcome.message", description="Translation key used for lookups"),
 *     @OA\Property(property="value", type="string", example="Welcome to our application!", description="Translated text"),
 *     @OA\Property(property="device_type", type="string", enum={"mobile", "tablet", "desktop"}, example="mobile", description="Device type this translation applies to"),
 *     @OA\Property(property="group", type="string", example="general", description="Translation group for organization"),
 *     @OA\Property(property="is_active", type="boolean", example=true, description="Whether this translation is active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
 * )
 *
 * @OA\Schema(
 *     schema="TranslationRequest",
 *     type="object",
 *     title="Translation Request",
 *     description="Data required to create or update a translation",
 *     required={"locale_id", "key", "value", "device_type"},
 *     @OA\Property(property="locale_id", type="integer", format="int64", example=1, description="Locale identifier"),
 *     @OA\Property(property="key", type="string", example="welcome.message", description="Translation key used for lookups"),
 *     @OA\Property(property="value", type="string", example="Welcome to our application!", description="Translated text"),
 *     @OA\Property(property="device_type", type="string", enum={"mobile", "tablet", "desktop"}, example="mobile", description="Device type this translation applies to"),
 *     @OA\Property(property="group", type="string", example="general", description="Translation group for organization"),
 *     @OA\Property(property="is_active", type="boolean", example=true, description="Whether this translation is active")
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     title="Error Response",
 *     description="Standard error response format",
 *     @OA\Property(property="message", type="string", description="Error message"),
 *     @OA\Property(property="error", type="string", description="Error code")
 * )
 *
 * @OA\Schema(
 *     schema="PaginatedResponse",
 *     type="object",
 *     title="Paginated Response",
 *     description="Standard paginated response format",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Translation")),
 *     @OA\Property(property="links", type="object",
 *         @OA\Property(property="first", type="string", example="http://localhost/api/translations?page=1"),
 *         @OA\Property(property="last", type="string", example="http://localhost/api/translations?page=5"),
 *         @OA\Property(property="prev", type="string", nullable=true),
 *         @OA\Property(property="next", type="string", example="http://localhost/api/translations?page=2")
 *     ),
 *     @OA\Property(property="meta", type="object",
 *         @OA\Property(property="current_page", type="integer", example=1),
 *         @OA\Property(property="from", type="integer", example=1),
 *         @OA\Property(property="last_page", type="integer", example=5),
 *         @OA\Property(property="path", type="string", example="http://localhost/api/translations"),
 *         @OA\Property(property="per_page", type="integer", example=15),
 *         @OA\Property(property="to", type="integer", example=15),
 *         @OA\Property(property="total", type="integer", example=75)
 *     )
 * )
 */
class TranslationController extends Controller
{
    /**
     * Constructor with dependency injection
     */
    public function __construct(
        private readonly TranslationServiceInterface $translationService
    ) {}

    /**
     * Get all translations with optional filtering
     *
     * @OA\Get(
     *     path="/api/translations",
     *     summary="List all translations",
     *     description="Get a paginated list of translations with optional filtering",
     *     operationId="listTranslations",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="key",
     *         in="query",
     *         description="Filter by translation key (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="value",
     *         in="query",
     *         description="Filter by translation value (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="locale_id",
     *         in="query",
     *         description="Filter by locale ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="device_type",
     *         in="query",
     *         description="Filter by device type",
     *         required=false,
     *         @OA\Schema(type="string", enum={"mobile", "tablet", "desktop"})
     *     ),
     *     @OA\Parameter(
     *         name="group",
     *         in="query",
     *         description="Filter by translation group",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15, minimum=1, maximum=100)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1, minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of translations",
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['key', 'value', 'locale_id', 'device_type', 'group']);
        $filters['per_page'] = $request->input('per_page', 15);

        $translations = $this->translationService->searchTranslations($filters);
        return response()->json(TranslationResource::collection($translations));
    }

    /**
     * Create a new translation
     *
     * @OA\Post(
     *     path="/api/translations",
     *     summary="Create a new translation",
     *     description="Add a new translation to the system",
     *     operationId="createTranslation",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Translation data",
     *         @OA\JsonContent(ref="#/components/schemas/TranslationRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Translation created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Translation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"key": {"The key field is required."}, "locale_id": {"The locale id field is required."}}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Duplicate translation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="A translation with this key already exists for the specified locale, device type, and group."),
     *             @OA\Property(property="error", type="string", example="duplicate_translation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function store(TranslationRequest $request): JsonResponse
    {
        try {
            $dto = new TranslationDTO(
                id: null,
                locale_id: $request->locale_id,
                key: $request->key,
                value: $request->value,
                device_type: $request->device_type,
                group: $request->group,
                is_active: $request->is_active ?? true
            );

            $translation = $this->translationService->createTranslation($dto);
            return response()->json(new TranslationResource($translation), 201);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return response()->json([
                'message' => 'A translation with this key already exists for the specified locale, device type, and group.',
                'error' => 'duplicate_translation'
            ], 409);
        } catch (\Exception $e) {
            Log::error('Translation creation failed: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'An error occurred while creating the translation.',
                'error' => 'server_error'
            ], 500);
        }
    }

    /**
     * Get a specific translation by ID
     *
     * @OA\Get(
     *     path="/api/translations/{id}",
     *     summary="Get a specific translation",
     *     description="Retrieve details for a single translation by its ID",
     *     operationId="getTranslation",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Translation ID",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Translation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Translation not found"),
     *             @OA\Property(property="error", type="string", example="not_found")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $translation = $this->translationService->getTranslation((int) $id);
        return response()->json(new TranslationResource($translation));
    }

    /**
     * Update an existing translation
     *
     * @OA\Put(
     *     path="/api/translations/{id}",
     *     summary="Update a translation",
     *     description="Update an existing translation",
     *     operationId="updateTranslation",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Translation ID",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Translation data",
     *         @OA\JsonContent(ref="#/components/schemas/TranslationRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Translation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"key": {"The key field is required."}, "locale_id": {"The locale id field is required."}}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Translation not found"),
     *             @OA\Property(property="error", type="string", example="not_found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Duplicate translation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="A translation with this key already exists for the specified locale, device type, and group."),
     *             @OA\Property(property="error", type="string", example="duplicate_translation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function update(TranslationRequest $request, int $id): JsonResponse
    {
        try {
            $dto = new TranslationDTO(
                id: (int) $id,
                locale_id: $request->locale_id,
                key: $request->key,
                value: $request->value,
                device_type: $request->device_type,
                group: $request->group,
                is_active: $request->is_active
            );

            $translation = $this->translationService->updateTranslation((int) $id, $dto);
            return response()->json(new TranslationResource($translation));
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return response()->json([
                'message' => 'A translation with this key already exists for the specified locale, device type, and group.',
                'error' => 'duplicate_translation'
            ], 409);
        } catch (\Exception $e) {
            Log::error('Translation update failed: ' . $e->getMessage(), [
                'id' => $id,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'An error occurred while updating the translation.',
                'error' => 'server_error'
            ], 500);
        }
    }

    /**
     * Delete a translation
     *
     * @OA\Delete(
     *     path="/api/translations/{id}",
     *     summary="Delete a translation",
     *     description="Remove a translation from the system",
     *     operationId="deleteTranslation",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Translation ID",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Translation deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Translation not found"),
     *             @OA\Property(property="error", type="string", example="not_found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->translationService->deleteTranslation((int) $id);
            return response()->json(['message' => 'Translation deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Translation deletion failed: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'An error occurred while deleting the translation.',
                'error' => 'server_error'
            ], 500);
        }
    }

    /**
     * Get translations by locale code
     *
     * @OA\Get(
     *     path="/api/translations/locale/{locale}",
     *     summary="Get translations by locale",
     *     description="Retrieve all translations for a specific locale code with optional device type filtering",
     *     operationId="getTranslationsByLocale",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="locale",
     *         in="path",
     *         required=true,
     *         description="Locale code (e.g., en, es, fr)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="device_type",
     *         in="query",
     *         required=false,
     *         description="Device type filter",
     *         @OA\Schema(type="string", enum={"mobile", "tablet", "desktop"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translations for the specified locale",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\AdditionalProperties(
     *                 type="string",
     *                 example="Welcome message"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function getByLocale(string $locale, Request $request): JsonResponse
    {
        try {
            $deviceType = $request->input('device_type');
            $translations = $this->translationService->getTranslationsByLocale($locale, $deviceType);
            return response()->json($translations)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            Log::error('Fetching translations by locale failed: ' . $e->getMessage(), [
                'locale' => $locale,
                'device_type' => $request->input('device_type'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'An error occurred while fetching translations.',
                'error' => 'server_error'
            ], 500);
        }
    }

    /**
     * Get translations in JSON format for frontend frameworks
     *
     * @OA\Get(
     *     path="/api/translations/json/{locale}",
     *     summary="Get translations in JSON format",
     *     description="Get translations grouped by their translation group for a specific locale and device type, optimized for frontend frameworks",
     *     operationId="getJsonTranslations",
     *     tags={"Translations"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="path",
     *         required=true,
     *         description="Locale code (e.g., en, es, fr)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="device_type",
     *         in="query",
     *         required=false,
     *         description="Device type (desktop, mobile, tablet)",
     *         @OA\Schema(type="string", enum={"desktop", "mobile", "tablet"}, default="desktop")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved translations",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="translations",
     *                 type="object",
     *                 description="Translations grouped by their group",
     *                 @OA\AdditionalProperties(
     *                     type="object",
     *                     description="Group of translations",
     *                     @OA\AdditionalProperties(
     *                         type="string",
     *                         description="Translation value"
     *                     )
     *                 ),
     *                 example={
     *                     "general": {
     *                         "general.welcome": "Welcome to our application",
     *                         "general.goodbye": "Thank you for using our application"
     *                     },
     *                     "auth": {
     *                         "auth.login": "Login",
     *                         "auth.logout": "Logout"
     *                     }
     *                 }
     *             ),
     *             @OA\Property(property="locale", type="string", example="en", description="The locale code requested"),
     *             @OA\Property(property="device_type", type="string", example="desktop", description="The device type requested"),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 description="Metadata about the translations",
     *                 @OA\Property(property="generated_at", type="string", format="date-time", example="2023-01-01T12:00:00Z", description="When these translations were generated"),
     *                 @OA\Property(property="version", type="string", example="1.0", description="Version of the translation format")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Locale not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Locale not found"),
     *             @OA\Property(property="error", type="string", example="not_found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function getJsonTranslations(string $locale, Request $request): JsonResponse
    {
        try {
            $deviceType = $request->input('device_type', 'desktop');
            $translations = $this->translationService->getTranslationsByLocale($locale, $deviceType);

            // Group translations by their group
            $groupedTranslations = [];
            foreach ($translations as $key => $value) {
                $parts = explode('.', $key);
                $group = $parts[0] ?? 'general';

                if (!isset($groupedTranslations[$group])) {
                    $groupedTranslations[$group] = [];
                }

                $groupedTranslations[$group][$key] = $value;
            }

            return response()->json([
                'translations' => $groupedTranslations,
                'locale' => $locale,
                'device_type' => $deviceType,
                'meta' => [
                    'generated_at' => now()->toIso8601String(),
                    'version' => '1.0'
                ]
            ])->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            Log::error('Fetching JSON translations failed: ' . $e->getMessage(), [
                'locale' => $locale,
                'device_type' => $request->input('device_type', 'desktop'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'An error occurred while fetching translations.',
                'error' => 'server_error'
            ], 500);
        }
    }
}
