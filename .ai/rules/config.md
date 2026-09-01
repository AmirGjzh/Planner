---
paths:
  - config/themes.php
---

# Config

## Fallback theme must be a present theme file
config/themes.php previously fell back to 'blue' even though config/themes/blue.json no longer exists (resolving name='blue' with empty vars). Default and invalid-APP_THEME fallback is now 'forest', one of the 6 present themes (ocean, forest, magic, safrron, amber, chocolate). Keep the fallback pointing at a file that exists.
