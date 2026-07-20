#prompt_example 

When initializing a new conversation or task, start your prompt by feeding this file to the model and stating:


```
"Using the provided CRUD blueprint from this URL: https://github.com/npapratovic/laravel-checklist/blob/master/laravel-crud-blueprint.md, map out a complete new feature set. Replicate the structural layers found inside <migration_layer>, <model_layer>, <validation_layer>, <dto_layer>, <action_layer>, <controller_layer>, <resource_layer>, and <factory_layer> for the [RESOURCE_NAME] entity with these fields: [FIELDS_AND_TYPES]. Adhere strictly to the DTO+Action design pattern, preserve all formatting, strict typing, and whitespace choices exactly as shown. Let's think step by step to ensure all architectural layers are perfectly aligned and no fields are missed."
```
