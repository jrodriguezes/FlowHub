<?php

namespace Tests\Unit;

use App\Services\ExecutionPayloadSanitizer;
use PHPUnit\Framework\TestCase;

class ExecutionPayloadSanitizerTest extends TestCase
{
    public function test_it_redacts_sensitive_keys_and_token_patterns(): void
    {
        $sanitizer = new ExecutionPayloadSanitizer();

        $sanitized = $sanitizer->sanitize([
            'access_token' => 'ghp_abc1234567890',
            'note' => 'Authorization: Bearer secret-token-value',
            'safe' => 'visible',
        ]);

        $this->assertSame('[redacted]', $sanitized['access_token']);
        $this->assertStringContainsString('[redacted]', $sanitized['note']);
        $this->assertSame('visible', $sanitized['safe']);
    }
}
