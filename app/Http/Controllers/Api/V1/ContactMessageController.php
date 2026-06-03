<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Rules\ValidContactEmail;
use App\Support\ContactChallenge;
use App\Support\ContactMessageSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ContactMessageController extends Controller
{
    public function challenge(): JsonResponse
    {
        $challenge = ContactChallenge::generate();

        return response()
            ->json([
                'ok' => true,
                'token' => $challenge['token'],
                'question' => $challenge['question'],
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function store(Request $request): JsonResponse
    {
        if ($request->filled('website')) {
            throw ValidationException::withMessages([
                'challenge_answer' => ['Security check failed. Please try again.'],
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'max:255', new ValidContactEmail],
            'message' => ['required', 'string', 'max:12000'],
            'challenge_token' => ['required', 'string'],
            'challenge_answer' => ['required', 'integer'],
        ]);

        if (! ContactChallenge::verify($validated['challenge_token'], $validated['challenge_answer'])) {
            throw ValidationException::withMessages([
                'challenge_answer' => ['Incorrect answer. Please solve the math question and try again.'],
            ]);
        }

        $message = ContactMessageSanitizer::clean($validated['message']);

        if (ContactMessageSanitizer::plainText($message) === '') {
            throw ValidationException::withMessages([
                'message' => ['The message field is required.'],
            ]);
        }

        ContactMessage::query()->create([
            'name' => trim($validated['name']),
            'email' => Str::lower(trim($validated['email'])),
            'message' => $message,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Thank you. Your message has been received and we will respond soon.',
        ], 201);
    }
}
