#prompt_example 

When initializing a new conversation or task, start your prompt by feeding this file to the model and stating:


```
"Using the provided CRUD blueprint from this URL: https://github.com/npapratovic/laravel-checklist/blob/master/laravel-crud-blueprint.md, map out a complete new feature set. Replicate the structural layers found inside <migration_layer>, <model_layer>, <validation_layer>, <dto_layer>, <action_layer>, <controller_layer>, <resource_layer>, and <factory_layer> for the [RESOURCE_NAME] entity with these fields: [FIELDS_AND_TYPES]. Adhere strictly to the DTO+Action design pattern, preserve all formatting, strict typing, and whitespace choices exactly as shown. Let's think step by step to ensure all architectural layers are perfectly aligned and no fields are missed."
```

EXAMPLE FIELDS SPECIFICATION:

- client_id (unsignedBigInteger, indexed, foreign key references clients)
- rule_name (string, indexed)
- is_active (boolean, default: true, indexed)
- operational_status (enum: 'active', 'inactive', 'pending')
- reference_date (dateTime)
- condition_type (enum: 'threshold', 'relative', 'absolute')
- offset_direction (enum: 'before', 'after')
- offset_value (integer)
- action_type (enum: 'email', 'sms', 'webhook')
- template_id (unsignedBigInteger, foreign key references communication_templates)
- recipient_type (enum: 'user', 'admin', 'manager')
- severity (enum: 'low', 'medium', 'high', 'critical')
