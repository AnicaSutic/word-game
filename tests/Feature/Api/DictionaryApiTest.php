<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Word;
use Mockery;
use App\Services\DictionaryService;


class DictionaryApiTest extends TestCase
{

    use RefreshDatabase;

    public function testScoreWordFromDatabase()
    {
        $word = 'elephant';
        $score = 7;

        Word::create(['word' => $word, 'score' => $score]);

        $response = $this->postJson('/api/score', ['word' => $word]);

        $response->assertStatus(200)
            ->assertJson(['word' => $word, 'score' => $score]);
    }

    public function testScoreWordFromAPI()
    {
        $word = 'elephant';
        $wordDetails = ['word' => $word, 'score' => 7];

        $dictionaryService = Mockery::mock(DictionaryService::class);
        $dictionaryService->shouldReceive('checkWordInDictionary')->with($word)->once()->andReturn($wordDetails);
        $this->app->instance(DictionaryService::class, $dictionaryService);

        $response = $this->postJson('/api/score', ['word' => $word]);

        $response->assertStatus(200)
            ->assertJson(['word' => $word, 'score' => $wordDetails['score']]);
    }

    public function testScoreWordFromAPIAndService()
    {
        $word = 'elephant';
        $wordDetails = ['word' => $word, 'score' => 7];

        $dictionaryService = Mockery::mock(DictionaryService::class);
        $dictionaryService->shouldReceive('checkWordInDictionary')->with($word)->once()->andReturn($wordDetails);
        $this->app->instance(DictionaryService::class, $dictionaryService);

        $response = $this->postJson('/api/score', ['word' => $word]);

        $response->assertStatus(200)
            ->assertJson(['word' => $word, 'score' => $wordDetails['score']]);
    }
}
