<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatSection;
use App\Models\ChatQa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function sections()
    {
        $sections = ChatSection::where('active', true)
            ->orderBy('sort_order')
            ->select('id', 'name', 'slug', 'subtitle')
            ->get();

        return response()->json($sections);
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'nullable|array',
            'history.*.role' => 'in:user,assistant',
            'history.*.content' => 'string',
            'browser_language' => 'nullable|string|max:10',
        ]);

        $message = $request->input('message');
        $history = $request->input('history', []);
        $browserLang = $request->input('browser_language', 'en');

        $languageNames = [
            'it' => 'Italian', 'en' => 'English', 'es' => 'Spanish',
            'fr' => 'French', 'de' => 'German', 'pt' => 'Portuguese',
            'zh' => 'Chinese', 'ja' => 'Japanese', 'ko' => 'Korean',
            'ar' => 'Arabic', 'ru' => 'Russian', 'nl' => 'Dutch',
        ];
        $defaultLang = $languageNames[$browserLang] ?? 'English';

        // Always include ALL active sections as context
        $context = $this->buildFullContext();

        $systemPrompt = "You are the virtual assistant of Nube, a software development company. "
            . "You MUST answer based on the information provided below. The information is written in English but covers all topics about Nube. "
            . "When the user asks a question in ANY language, search the provided information by MEANING, not by exact word match. "
            . "For example, if someone asks '¿Quiénes son sus clientes?' (Who are your clients?), look in the Portfolio section for client names like RDS, Eurobet, Lux Holding, Enpaia, etc. "
            . "Always try to find a relevant answer from the provided information before saying you cannot help. "
            . "Only if the question is truly about something completely unrelated to Nube (e.g. weather, cooking, math), politely say you can only help with information about Nube.\n\n"
            . "LANGUAGE RULES:\n"
            . "- The user's preferred language is {$defaultLang}. This is their browser language.\n"
            . "- By DEFAULT, always reply in {$defaultLang}.\n"
            . "- Words like 'Portfolio', 'Blog', 'About Us', 'Contacts' are button labels, NOT indicators of language. They should ALWAYS get a {$defaultLang} response.\n"
            . "- ONLY switch language if the user writes a FULL SENTENCE clearly in another language (e.g. a question in Spanish, French, etc.).\n"
            . "- Look at conversation history: if the user previously wrote in another language, continue in that language.\n\n"
            . "FORMATTING RULES:\n"
            . "- When including URLs, always write them as plain text (e.g. https://wa.me/393408538104), never use markdown link syntax like [text](url).\n"
            . "- Do NOT use markdown formatting: no **, no ##, no [](), no bullet points with *.\n"
            . "- Use plain text only. Separate paragraphs with line breaks.\n"
            . "- Keep responses natural, well-structured and concise.\n\n"
            . "=== COMPANY INFORMATION ===\n"
            . $context;

        // Build messages array with history for context
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Add conversation history (last 6 messages = 3 exchanges max)
        $recentHistory = array_slice($history, -6);
        foreach ($recentHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content'],
            ];
        }

        // Add current message
        $messages[] = ['role' => 'user', 'content' => $message];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.key'),
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => $messages,
            'max_tokens' => 500,
            'temperature' => 0.7,
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to generate response.',
            ], 500);
        }

        $reply = $response->json('choices.0.message.content', 'Sorry, I could not generate a response.');

        return response()->json([
            'reply' => $reply,
        ]);
    }

    private function buildFullContext(): string
    {
        $context = '';
        $sections = ChatSection::where('active', true)->with('qas')->orderBy('sort_order')->get();

        foreach ($sections as $section) {
            $context .= "=== {$section->name} ===\n";
            foreach ($section->qas as $qa) {
                $context .= "Q: {$qa->question}\nA: {$qa->answer}\n\n";
            }
        }

        return $context;
    }
}
