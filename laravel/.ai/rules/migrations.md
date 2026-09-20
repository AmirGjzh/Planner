---
paths:
  - 'database/migrations/**'
---

# Migrations

Column conventions for migrations.

## Store enums as DB enum columns plus PHP enum casts

- DB column: `->enum('priority', ['low', 'medium', 'high'])` — values must match the PHP enum case values.
- Model cast: `'priority' => TaskPriority::class`.
- Keep a `default()` on the column and match model/DB values exactly.