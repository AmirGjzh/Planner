---
paths:
  - 'database/migrations/**'
---

# Migrations

## Enums stored as DB enum columns plus PHP enum cast
Store enums as DB `enum()` columns whose values match the PHP enum case values (`->enum('priority', ['low','medium','high'])`), and cast them in the model to the enum class (`'priority' => TaskPriority::class`). Keep a `default()` on the column and match model/DB values exactly.
