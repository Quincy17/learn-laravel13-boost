<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AIController extends Controller
{
    private const MAX_RESPONSES_PER_SESSION = 10;
    private const MAX_SENTENCES = 7;

    public function index()
    {
        $response = \Laravel\Ai\agent(
            instructions: 'You are a helpful assistant that provides information about Laravel. Answer the user\'s question in a clear and concise manner in exactly ' . self::MAX_SENTENCES . ' sentences maximum. Be brief and to the point.',
        )->prompt('What is Laravel?');

        $responseCount = session('ai_response_count', 0);
        $remainingResponses = max(0, self::MAX_RESPONSES_PER_SESSION - $responseCount);

        return view('ai.index', [
            'response' => $response,
            'responseCount' => $responseCount,
            'remainingResponses' => $remainingResponses,
            'maxResponses' => self::MAX_RESPONSES_PER_SESSION,
            'maxSentences' => self::MAX_SENTENCES,
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);

        $responseCount = session('ai_response_count', 0);
        if ($responseCount >= self::MAX_RESPONSES_PER_SESSION) {
            return response()->json([
                'success' => false,
                'message' => 'Anda telah mencapai batas maksimal ' . self::MAX_RESPONSES_PER_SESSION . ' respons per sesi.',
            ], 429);
        }

        try {
            $response = \Laravel\Ai\agent(
                instructions: 'You are a helpful assistant that provides information about Laravel. Answer the user\'s question in a clear and concise manner in exactly ' . self::MAX_SENTENCES . ' sentences maximum. Be brief and to the point. Do not provide unnecessary information.',
            )->prompt($request->input('prompt'));

            session(['ai_response_count' => $responseCount + 1]);

            return response()->json([
                'success' => true,
                'response' => (string) $response,
                'responseCount' => $responseCount + 1,
                'remainingResponses' => max(0, self::MAX_RESPONSES_PER_SESSION - ($responseCount + 1)),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}