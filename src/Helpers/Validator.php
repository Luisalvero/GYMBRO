<?php
/**
 * Input Validation Helper
 * 
 * Provides validation methods for user input
 */

namespace App\Helpers;

class Validator
{
    private array $errors = [];
    private array $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Validate email
     */
    public function email(string $field, string $label = 'Email'): self
    {
        $value = $this->getValue($field);
        
        if (empty($value)) {
            $this->errors[$field] = "$label is required";
        } elseif (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "$label must be a valid email address";
        }
        
        return $this;
    }

    /**
     * Validate required field
     */
    public function required(string $field, string $label = null): self
    {
        $label = $label ?? ucfirst($field);
        $value = $this->getValue($field);
        
        if (empty($value) && $value !== '0') {
            $this->errors[$field] = "$label is required";
        }
        
        return $this;
    }

    /**
     * Validate minimum length
     */
    public function minLength(string $field, int $min, string $label = null): self
    {
        $label = $label ?? ucfirst($field);
        $value = $this->getValue($field);
        
        if (!empty($value) && strlen($value) < $min) {
            $this->errors[$field] = "$label must be at least $min characters";
        }
        
        return $this;
    }

    /**
     * Validate maximum length
     */
    public function maxLength(string $field, int $max, string $label = null): self
    {
        $label = $label ?? ucfirst($field);
        $value = $this->getValue($field);
        
        if (!empty($value) && strlen($value) > $max) {
            $this->errors[$field] = "$label must not exceed $max characters";
        }
        
        return $this;
    }

    /**
     * Validate password strength
     * - Min 8 chars, 1 uppercase, 2 digits, 2 symbols from @#!?
     */
    public function password(string $field, string $label = 'Password'): self
    {
        $value = $this->getValue($field);
        
        if (empty($value)) {
            $this->errors[$field] = "$label is required";
            return $this;
        }

        if (strlen($value) < 8) {
            $this->errors[$field] = "$label must be at least 8 characters";
        } elseif (!preg_match('/[A-Z]/', $value)) {
            $this->errors[$field] = "$label must contain at least 1 uppercase letter";
        } elseif (!preg_match('/(?:.*\d){2,}/', $value)) {
            $this->errors[$field] = "$label must contain at least 2 digits";
        } elseif (!preg_match('/(?:.*[@#!?]){2,}/', $value)) {
            $this->errors[$field] = "$label must contain at least 2 symbols (@#!?)";
        }
        
        return $this;
    }

    /**
     * Validate password confirmation
     */
    public function confirmed(string $field, string $confirmField, string $label = 'Password'): self
    {
        if ($this->getValue($field) !== $this->getValue($confirmField)) {
            $this->errors[$confirmField] = "$label confirmation does not match";
        }
        
        return $this;
    }

    /**
     * Validate numeric value in range
     */
    public function between(string $field, int $min, int $max, string $label = null): self
    {
        $label = $label ?? ucfirst($field);
        $value = $this->getValue($field);
        
        if (!is_numeric($value)) {
            $this->errors[$field] = "$label must be a number";
        } elseif ($value < $min || $value > $max) {
            $this->errors[$field] = "$label must be between $min and $max";
        }
        
        return $this;
    }

    /**
     * Validate value is in array
     */
    public function in(string $field, array $allowed, string $label = null): self
    {
        $label = $label ?? ucfirst($field);
        $value = $this->getValue($field);
        
        if (!empty($value) && !in_array($value, $allowed)) {
            $this->errors[$field] = "$label is invalid";
        }
        
        return $this;
    }

    /**
     * Validate array values are in allowed list
     */
    public function arrayIn(string $field, array $allowed, string $label = null): self
    {
        $label = $label ?? ucfirst($field);
        $values = $this->getValue($field);
        
        if (!is_array($values)) {
            $this->errors[$field] = "$label must be an array";
            return $this;
        }
        
        foreach ($values as $value) {
            if (!in_array($value, $allowed)) {
                $this->errors[$field] = "$label contains invalid values";
                break;
            }
        }
        
        return $this;
    }

    /**
     * Custom validation with callback
     */
    public function custom(string $field, callable $callback, string $message): self
    {
        $value = $this->getValue($field);
        
        if (!$callback($value)) {
            $this->errors[$field] = $message;
        }
        
        return $this;
    }

    /**
     * Check if validation passed
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Check if validation failed
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Get all errors
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error message
     */
    public function firstError(): ?string
    {
        return reset($this->errors) ?: null;
    }

    /**
     * Get value from data
     */
    private function getValue(string $field)
    {
        return $this->data[$field] ?? null;
    }

    /**
     * Get sanitized data
     */
    public function validated(): array
    {
        $validated = [];
        foreach ($this->data as $key => $value) {
            if (!isset($this->errors[$key])) {
                $validated[$key] = is_string($value) ? trim($value) : $value;
            }
        }
        return $validated;
    }
}
