<?php

namespace Idez\Bankly\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;

trait HasScopes
{
    public function containsScope(string $scope): bool
    {
        return Str::contains($this->scopes, $scope);
    }

    public function setScopes(array|string|Collection $scopes): self
    {
        $this->scopes = $this->normalizeScopes($scopes);

        return $this;
    }

    public function getScopes(): string
    {
        return $this->scopes;
    }

    /**
     * @param array|string|Collection $scopes
     * @return string
     * @throws InvalidArgumentException
     */
    public function normalizeScopes(array|string|Collection $scopes): string
    {
        if (is_string($scopes)) {
            $scopes = explode(' ', $scopes);
        }

        $scopes = collect($scopes);

        if ($scopes->isEmpty()) {
            throw new InvalidArgumentException('Scopes must be a non-empty string or collection');
        }

        if ($scopes->count() > 10) {
            throw new InvalidArgumentException('Scopes must be less than 10');
        }

        return $scopes->implode(' ');
    }
}
