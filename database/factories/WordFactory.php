<?php

namespace Database\Factories;

use App\Models\Word;
use App\Services\DictionaryService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Word>
 */
class WordFactory extends Factory
{

    protected $model = Word::class;


    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $dictionaryService = new DictionaryService();
        $word = $this->faker->unique()->word;

        $wordDetails = $dictionaryService->checkWordInDictionary($word);

        while (isset($wordDetails['error'])) {
            $word = $this->faker->unique()->word;
            $wordDetails = $dictionaryService->checkWordInDictionary($word);
        }

        $score = $dictionaryService->calculateScore($word);

        return [
            'word' => $word,
            'score' => $score,
        ];
    }
}
