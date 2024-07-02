<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\DictionaryService;

class ScoreWordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:score-word-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Score a word from console input';

    protected $dictionaryService;

    public function __construct(DictionaryService $dictionaryService)
    {
        parent::__construct();
        $this->dictionaryService = $dictionaryService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $word = $this->ask('Enter a word to score');

        $wordScore = $this->dictionaryService->scoreWord($word);

        if (isset($wordScore['error'])) {
            $this->error('Word not found in dictionary');
        } else {
            $this->info("Word: {$wordScore->word}");
            $this->info("Score: {$wordScore->score}");
        }
    }
}
