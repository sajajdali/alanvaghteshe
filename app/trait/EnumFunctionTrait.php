<?php

namespace App\trait;

trait EnumFunctionTrait
{
    public function isOneOf(array $values): bool
    {
        foreach ($values as $value) {
            if ($this->is($value)) {
                return true;
            }
        }
        return false;
    }

    public function is(self $value): bool
    {
        return $this === $value;
    }

    public function isOr(array $values): bool
    {
        foreach ($values as $value) {
            if ($this->is($value)) {
                return true;
            }
        }
        return false;
    }

    public function isAnd(array $values) : bool
    {
        foreach ($values as $value) {
            if (!$this->is($value)) {
                return false;
            }
        }
        return true;
    }

    public function isNot(self $value): bool
    {
        return !$this->is($value);
    }

    public function isNotOf(array $values): bool
    {
        foreach ($values as $value) {
            if ($this->is($value)) {
                return false;
            }
        }
        return true;
    }

    public function isNotAnd(array $values) : bool
    {
        foreach ($values as $value) {
            if ($this->is($value)) {
                return false;
            }
        }
        return true;
    }
}
