# Password Validation Reference

## Password Requirements
- **Minimum length**: 8 characters
- **Uppercase letters**: At least 1
- **Digits**: At least 2
- **Special symbols**: At least 2 (must be from: @, #, !, ?)

## Valid Examples
✅ `MyPass123@#` - Perfect!
✅ `GymBro99!!` - Has uppercase, 2 digits, 2 symbols
✅ `Fitness2023@!` - Longer is better
✅ `WorkOut55#@` - Mixed case works
✅ `Strong123!@` - Simple and valid

## Invalid Examples
❌ `short1@#` - Only 8 chars but no uppercase
❌ `NoDigits@#` - Missing 2 digits (has 0)
❌ `NoSymbols123` - Missing 2 symbols
❌ `nouppercase123@#` - Missing uppercase letter
❌ `OnlyOneDigit1@#` - Only 1 digit (needs 2)
❌ `OnlyOneSymbol12@` - Only 1 symbol (needs 2)
❌ `WrongSymbol123$%` - Symbols must be @#!? only

## Regex Pattern
```
/^(?=.*[A-Z])(?=(?:.*\d){2,})(?=(?:.*[@#!?]){2,}).{8,}$/
```

### Breakdown:
- `^` - Start of string
- `(?=.*[A-Z])` - Lookahead: at least one uppercase letter
- `(?=(?:.*\d){2,})` - Lookahead: at least 2 digits
- `(?=(?:.*[@#!?]){2,})` - Lookahead: at least 2 symbols from @#!?
- `.{8,}` - Minimum 8 characters total
- `$` - End of string

## Implementation

### Server-Side (PHP)
```php
function validatePassword($password) {
    return preg_match('/^(?=.*[A-Z])(?=(?:.*\d){2,})(?=(?:.*[@#!?]){2,}).{8,}$/', $password);
}
```

### Client-Side (HTML5)
```html
<input 
    type="password" 
    pattern="^(?=.*[A-Z])(?=(?:.*\d){2,})(?=(?:.*[@#!?]){2,}).{8,}$"
    title="Min 8 chars, 1 uppercase, 2 digits, 2 symbols from @#!?"
    required
>
```

## Testing

Test these passwords during signup:
1. `MyPass123@#` → ✅ Should work
2. `short` → ❌ Too short
3. `longbutnouppercasedigitssymbols` → ❌ Missing requirements
4. `NoDigits@#` → ❌ Needs 2 digits
5. `Test1@` → ❌ Only 1 digit, needs 2
6. `Test12@` → ✅ Should work!
