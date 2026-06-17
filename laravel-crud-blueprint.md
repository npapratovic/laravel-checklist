# Laravel CRUD Architectural Blueprint & Guidelines

This document serves as a rigid architectural and style guide (blueprint) for code generation and refactoring within the application. The model must strictly adhere to the conventions, design patterns, and code formatting rules specified below without any deviations.

---

## 1. Core Architectural Principles

All CRUD operations and business logic must be implemented using the decoupled **DTO + Action** pattern. Writing business logic directly inside controllers or models is strictly prohibited.

1. **Route Model Binding:** Always use Route Model Binding in controllers to automatically resolve entities from routes.
2. **Validation (Form Requests):** Validation is handled exclusively within dedicated Form Request classes. The controller must never call `Validator::make` or manually check input fields.
3. **Data Transfer Objects (DTO):** Validated data from the Request is mapped into a strictly typed, readonly DTO before being passed to the action.
4. **Action Classes:** All business logic (creation, updates, deletion, side effects) resides in atomic Action classes that accept a DTO (or a Model in the case of update/delete operations).
5. **Responses (JSON Resources):** API responses must always be returned via Laravel's `JsonResource` to ensure uniform and encapsulated output structures.
6. **Strict Models:** Models must have explicitly defined `casts` and local `scopes` for common queries. Databases should be optimized with performance-driven indexes on foreign keys and frequently searched columns.

---

## 2. Coding Style & Formatting

* **Code Preservation:** Existing code, whitespaces, blank lines, and array structural patterns (whether written inline or multiline) **must left untouched** unless that specific line is being directly refactored.
* **Comments:** Detailed inline comments are welcome and encouraged to explain complex business logic steps.
* **Principles:** Maintain a strong focus on **DRY, SOLID, and YAGNI** principles, targeting the bare minimum code changes with maximum impact on performance.

---

## 3. Complete Single-File CRUD Blueprint Example

Below is a complete, monolithic code block containing all the necessary layers for a `Brand` resource. In practice, these are separated into individual domain files, but they are consolidated here in one place so the LLM can analyze the entire structure and relationships simultaneously.

```php
// ==========================================
// 1. DATABASE MIGRATION
// ==========================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $create) {
            $create->id();
            $create->string('name')->index();
            $create->string('code')->unique();
            $create->boolean('is_active')->default(true)->index();
            $create->timestamps();
            $table->softDeletes();
        });
    } 
};

// ==========================================
// 2. STRICT ELOQUENT MODEL
// ==========================================
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $code
 * @property-read bool $is_active
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
 
final class Brand extends Model
{
    /**
     * @use HasFactory<BrandFactory>
     */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('is_active', true);
    }

    public function scopeSearchByName(Builder $builder, string $name): Builder
    {
        return $builder->where('name', 'like', "%{$name}%");
    }
}

// ==========================================
// 3. FORM REQUEST (VALIDATION)
// ==========================================

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class BrandIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'code'      => ['required', 'string', 'max:50', 'unique:brands,code'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'code'      => ['required', 'string', 'max:50', 'unique:brands,code'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'code'      => ['required', 'string', 'max:50', 'unique:brands,code'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
// ==========================================
// 4. DATA TRANSFER OBJECT (DTO)
// ==========================================
declare(strict_types=1);

namespace App\DTO;

use Illuminate\Foundation\Http\FormRequest;

final readonly class BrandData
{
    public function __construct(
        public string $name,
        public string $code,
        public bool $isActive
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            code: $request->validated('code'),
            isActive: (bool) $request->validated('is_active', true)
        );
    }
}

// ==========================================
// 5. ATOMIC ACTION CLASS: Create, Update, Delete
// ==========================================

declare(strict_types=1);

namespace App\Actions\Brand;

use App\Models\Brand;
use App\DTO\BrandData;

final readonly class CreateBrand
{
    public function handle(BrandData $dto): Brand
    {
        return DB::transaction(function () use ($dto): Brand {

            $brand = Brand::query()->create([
                'name'      => $dto->name,
                'code'      => $dto->code,
                'is_active' => $dto->isActive,
            ]);

            return $brand;
    
        });

    }
}

declare(strict_types=1);

namespace App\Actions\Brand;

use App\Models\Brand;
use App\DTO\BrandData;

final readonly class UpdateBrand
{
    public function handle(BrandData $dto): Brand
    {
      return DB::transaction(function () use ($item, $dto): Brand {
            $item->update([
                'name'      => $dto->name,
                'code'      => $dto->code,
                'is_active' => $dto->isActive,
            ]);

            return $item;
        });
    }
}

declare(strict_types=1);

namespace App\Actions;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;

final readonly class DeleteBrand
{
    public function handle(Brand $item): bool
    {
        return DB::transaction(function () use ($item) {
            return $item->delete();
        });
    }
}

// ==========================================
// 6. CONTROLLER (CRUDDY BY DESIGN)
// ==========================================

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateBrand;
use App\Actions\DeleteBrand;
use App\Actions\UpdateBrand;
use App\DTO\BrandData;
use App\Http\Requests\BrandIndexRequest;
use App\Http\Requests\CreateBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

final readonly class BrandController
{
    public function index(BrandIndexRequest $request): Response
    {
        $query = Brand::query();

        // Option 1: API Response
        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => BrandResource::collection($query->paginate(10)),
            ]);
        }

        // Option 2: Inertia Response
        return inertia('brands/index', [
            'brands' => BrandResource::collection($query->paginate(10)),
        ]);
    }

    public function show(Brand $brand): JsonResponse
    {
        // Option 1: API Response
        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => new BrandResource($brand),
            ]);
        }

        // Option 2: Inertia Response
        return Inertia::render('brands/show', [
            'brand' => new BrandResource($brand),
        ]);
    }

    public function store(
        CreateBrandRequest $request,
        CreateBrand $action
    ): JsonResponse {

        $data = BrandData::fromRequest($request);

        $brand = $action->handle($data);

        // Option 1: API Response
        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => new BrandResource($brand),
            ], 201);
        }

        // Option 2: Inertia Response
        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function update(
        UpdateBrandRequest $request,
        Brand $brand,
        UpdateBrand $action
    ): JsonResponse {

        $data = BrandData::fromRequest($request);

        $updated = $action->handle($brand, $data);

        // Option 1: API Response
        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => new BrandResource($updated),
            ], 201);
        }

        // Option 2: Inertia Response
        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(
        Brand $brand,
        DeleteBrand $action
    ): JsonResponse {

        $action->handle($brand);

        // Option 1: API Response
        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Model deleted successfully',
            ]);
        }

        // Option 2: Inertia Response
        return redirect()->intended(route('dashboard', absolute: false));
    }
}


// ==========================================
// 7. JSON RESOURCE
// ==========================================
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class BrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'code'       => $this->code,
            'is_active'  => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

// ==========================================
// 8. FACTORY
// ==========================================

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Brand>
 */
final class BrandFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $greek_letters = ['Alpha', 'Beta', 'Gamma', 'Delta', 'Epsilon', 'Zeta', 'Eta', 'Theta', 'Iota', 'Kappa', 'Lambda', 'Mu', 'Nu', 'Xi', 'Omicron', 'Pi', 'Rho', 'Sigma', 'Tau', 'Upsilon', 'Phi', 'Chi', 'Psi', 'Omega'];
        $levels = ['Low', 'Medium', 'High', 'Critical', 'Emergency', 'Alert', 'Notice', 'Informational'];

        return [
            // convert template_name to uppercase
            'name' => strtoupper(fake()->randomElement($levels).'-'.fake()->randomElement($greek_letters).'-'.fake()->randomNumber(3)),
            'code' => fake()->unique()->regexify('[A-Z]{5}[0-4]{3}'),
            'is_active' => fake()->boolean(),
        ];
    }
}

```

---

## 4. Blueprint Prompting Instructions

When initializing a new conversation or task, start your prompt by feeding this file to the model and stating:

> *"Using the provided CRUD blueprint, generate a complete CRUD resource structure for the `[RESOURCE_NAME]` entity with the following fields: `[FIELDS_AND_TYPES]`. Adhere strictly to the DTO+Action design pattern, single-file structural relationship, dedicated Form Requests, Route Model Binding, and preserve all code formatting rules and whitespaces."*