<?php

namespace App\Services;

class ExecutionPayloadSanitizer
{
    private const SENSITIVE_KEY_PATTERN = '/(token|secret|password|authorization|api[_-]?key|refresh)/i';

    /**
     * @param  array<string, mixed>|null  $payload
     * @return array<string, mixed>|null
     */
    public function sanitize(?array $payload): ?array
    {
        if ($payload === null) {
            return null;
        }

        return $this->sanitizeValue($payload);
    }

    /**
     * @return array<string, mixed>|list<mixed>|string|int|float|bool|null
     */
    private function sanitizeValue(mixed $value): mixed
    {
        if (is_array($value)) {
            $sanitized = [];

            foreach ($value as $key => $item) {
                if (is_string($key) && preg_match(self::SENSITIVE_KEY_PATTERN, $key)) {
                    $sanitized[$key] = '[redacted]';

                    continue;
                }

                $sanitized[$key] = $this->sanitizeValue($item);
            }

            return $sanitized;
        }

        if (is_string($value)) {
            return $this->sanitizeString($value);
        }

        return $value;
    }

    public function sanitizeString(string $value): string
    {
        $patterns = [
            '/\bghp_[A-Za-z0-9_]+/' => '[redacted-github-token]',
            '/\bgho_[A-Za-z0-9_]+/' => '[redacted-github-token]',
            '/\bBearer\s+[A-Za-z0-9\-._~+\/]+=*/i' => 'Bearer [redacted]',
            '/\bya29\.[A-Za-z0-9\-._~+\/]+=*/' => '[redacted-google-token]',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $value = preg_replace($pattern, $replacement, $value) ?? $value;
        }

        return $value;
    }
}
