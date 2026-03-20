<?php

namespace App\Http\Controllers;

use App\Models\ChatSection;
use App\Models\ChatQa;
use Illuminate\Http\Request;

class ChatQaController extends Controller
{
    public function index()
    {
        $sections = ChatSection::with('qas')->orderBy('sort_order')->get();
        return view('admin.chat-qa.index', compact('sections'));
    }

    public function createQa(ChatSection $section)
    {
        return view('admin.chat-qa.create-qa', compact('section'));
    }

    public function storeQa(Request $request, ChatSection $section)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
        ]);

        $maxOrder = $section->qas()->max('sort_order') ?? 0;

        $section->qas()->create([
            ...$validated,
            'sort_order' => $maxOrder + 1,
        ]);

        return redirect()->route('chat-qa.index')->with('success', 'Q&A aggiunta con successo.');
    }

    public function editQa(ChatQa $qa)
    {
        $qa->load('section');
        return view('admin.chat-qa.edit-qa', compact('qa'));
    }

    public function updateQa(Request $request, ChatQa $qa)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
        ]);

        $qa->update($validated);

        return redirect()->route('chat-qa.index')->with('success', 'Q&A aggiornata con successo.');
    }

    public function destroyQa(ChatQa $qa)
    {
        $qa->delete();
        return redirect()->route('chat-qa.index')->with('success', 'Q&A eliminata.');
    }

    public function editSection(ChatSection $section)
    {
        return view('admin.chat-qa.edit-section', compact('section'));
    }

    public function updateSection(Request $request, ChatSection $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active');
        $section->update($validated);

        return redirect()->route('chat-qa.index')->with('success', 'Sezione aggiornata.');
    }
}
