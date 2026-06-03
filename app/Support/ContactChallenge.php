<?php

namespace App\Support;

use Illuminate\Support\Facades\Crypt;
use Throwable;

final class ContactChallenge
{
    /**
     * @return array{token: string, question: string}
     */
    public static function generate(): array
    {
        $operator = random_int(0, 2);

        if ($operator === 0) {
            $left = random_int(2, 12);
            $right = random_int(2, 12);
            $answer = $left + $right;
            $question = "{$left} + {$right}";
        } elseif ($operator === 1) {
            $left = random_int(6, 18);
            $right = random_int(2, $left - 1);
            $answer = $left - $right;
            $question = "{$left} - {$right}";
        } else {
            $left = random_int(2, 9);
            $right = random_int(2, 9);
            $answer = $left * $right;
            $question = "{$left} × {$right}";
        }

        $payload = [
            'answer' => $answer,
            'expires_at' => now()->addMinutes(15)->timestamp,
            'nonce' => bin2hex(random_bytes(8)),
        ];

        return [
            'token' => Crypt::encryptString(json_encode($payload, JSON_THROW_ON_ERROR)),
            'question' => $question,
        ];
    }

    public static function verify(string $token, mixed $answer): bool
    {
        if (! is_numeric($answer)) {
            return false;
        }

        try {
            $payload = json_decode(Crypt::decryptString($token), true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return false;
        }

        if (! is_array($payload) || ! isset($payload['expires_at'])) {
            return false;
        }

        if (now()->timestamp > (int) $payload['expires_at']) {
            return false;
        }

        if (isset($payload['answer'])) {
            return (int) $payload['answer'] === (int) $answer;
        }

        // Legacy tokens (addition only).
        if (isset($payload['left'], $payload['right'])) {
            return ((int) $payload['left'] + (int) $payload['right']) === (int) $answer;
        }

        return false;
    }
}
