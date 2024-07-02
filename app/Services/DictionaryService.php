<?php

namespace App\Services;

use App\Models\Word;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class DictionaryService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.rapidapi.key');
        $this->apiUrl = config('services.rapidapi.url');
    }

    public function scoreWord($word)
    {
        $wordScore = Word::where('word', $word)->first();

        if (!$wordScore) {
            $wordDetails = $this->checkWordInDictionary($word);

            if (isset($wordDetails['error'])) {
                return ['error' => 'Word not found in dictionary'];
            }

            $score = $this->calculateScore($word);

            $wordScore = Word::create([
                'word' => $word,
                'score' => $score,
            ]);
        }

        return $wordScore;
    }

    public function checkWordInDictionary($word)
    {
        $client = new Client([
            'base_uri' => $this->apiUrl,
            'headers' => [
                'X-RapidAPI-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ],
        ]);

        try {
            $response = $client->get("words/{$word}");
            $body = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() == 200) {
                return $body;
            } else {
                return null;
            }
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 404) {
                return ['error' => 'Word not found in dictionary'];
            } else {
                return ['error' => 'Client error: ' . $e->getMessage()];
            }
        } catch (GuzzleException $e) {
            return ['error' => 'Guzzle error: ' . $e->getMessage()];
        }
    }

    private function calculateScore($word)
    {
        $score = 0;

        // Unique letters count
        $uniqueLetters = count(array_unique(str_split($word)));
        $score += $uniqueLetters;

        // Palindrome check
        if ($this->isPalindrome($word)) {
            $score += 3;
        }

        // Almost palindrome check
        if ($this->isAlmostPalindrome($word)) {
            $score += 2;
        }

        return $score;
    }

    private function isPalindrome($word)
    {
        $reversedWord = strrev($word);
        return strtolower($word) === strtolower($reversedWord);
    }

    private function isAlmostPalindrome($word)
    {
        $len = strlen($word);
        for ($i = 0; $i < $len; $i++) {
            $newWord = substr($word, 0, $i) . substr($word, $i + 1);
            if ($this->isPalindrome($newWord)) {
                return true;
            }
        }
        return false;
    }
}
