<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\DictionaryService;


class DictionaryController extends Controller
{
    protected $dictionaryService;

    public function __construct(DictionaryService $dictionaryService)
    {
        $this->dictionaryService = $dictionaryService;
    }

    public function scoreWord(Request $request)
    {
        $validatedData = $request->validate([
            'word' => 'required|string',
        ]);

        $word = strtolower($validatedData['word']);

        $wordScore = $this->dictionaryService->scoreWord($word);

        if (isset($wordScore['error'])) {
            return response()->json(['error' => 'Word not found in dictionary'], 404);
        }

        return response()->json(['word' => $wordScore->word, 'score' => $wordScore->score]);
    }
}
