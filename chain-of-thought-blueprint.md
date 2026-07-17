#chain-of-thought-blueprint.md
 
You are a Senior System Architect specializing in Laravel, MySQL, and strict DDD/CRUD architectures (DTO + Action pattern).

TASK:
Create a complete, client-scoped CRUD resource for the "SLARule" entity using the structural rules defined in the blueprint below.

CRITICAL ARCHITECTURAL CONSTRAINTS:
1. CODE PRESERVATION: Do not alter, add, or omit any architectural layers. Keep strict typing (declare(strict_types=1)), 'final' classes, and exact whitespace alignment.
2. CLIENT SCOPE: This entity is strictly client-scoped. Every query, index, unique constraint, and action must take 'client_id' into account.
3. STRICT ENUMS: Every enum field must be a backed string enum in PHP, stored as a string in the database, validated using 'Illuminate\Validation\Rules\Enum', and cast in the Model.
4. FOREIGN KEYS: 'template_id' must reference 'communication_templates'.

FIELDS SPECIFICATION:
- client_id (unsignedBigInteger, indexed, foreign key)
- rule_name (string, indexed)
- is_active (boolean, default: true, indexed)
- operational_status (string, enum)
- reference_date (dateTime/timestamp)
- condition_type (string, enum)
- offset_direction (string, enum)
- offset_value (integer)
- action_type (string, enum)
- template_id (unsignedBigInteger, foreign key to communication_templates)
- recipient_type (string, enum)
- severity (string, enum)

[INSERT THE ENTIRE CONTENT OF YOUR BLUEPRINT / GITHUB FILE HERE]

---

### CHAIN OF THOUGHT EXECUTION PROTOCOL

Before generating any code layers, you MUST execute and write down the following 5 reasoning steps. Do not skip any step.

#### STEP 1: ENUM ARCHITECTURE & MAPPING
List every single required Enum class for this CRUD. For each enum, write its namespace (App\Enums), its backed type (string), and define 3 realistic case values.
*Stop and write this down before proceeding.*

#### STEP 2: DATABASE & INDEX OPTIMIZATION
Analyze the query performance needs on a CloudWays shared server environment:
- Which composite indexes are needed given that this resource is ALWAYS queried within a 'client_id' scope? (e.g., ['client_id', 'is_active'])
- Write down the exact foreign key definitions and cascading rules.
*Stop and write this down before proceeding.*

#### STEP 3: DTO & VALIDATION RIGOR
- Map every field to its strict PHP type (including nullability and Enums).
- Write down the exact validation rule array for the Enum fields using the Laravel 'Rule::enum()' syntax.
*Stop and write this down before proceeding.*

#### STEP 4: CLIENT-SCOPED ACTION PLAN
- Explain how the 'CreateSLARule' and 'UpdateSLARule' actions will ensure that the 'client_id' is securely bound and handled within a DB::transaction().
*Stop and write this down before proceeding.*

#### STEP 5: FINAL VERIFICATION CHECKLIST
Verify that:
1. All classes are 'final'.
2. All layers use XML tags exactly like the blueprint (<migration_layer>, <model_layer>, <validation_layer>, <dto_layer>, <action_layer>, <controller_layer>, <resource_layer>, <factory_layer>).
3. No shortcuts or "// TODO" comments are left in the code.

---

GENERATION INSTRUCTION:
Now, write down your Step 1-5 analysis text. Immediately following the analysis, generate the complete, production-ready code files wrapped in their respective XML tags.
