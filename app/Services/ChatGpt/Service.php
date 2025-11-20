<?php

namespace App\Services\ChatGpt;

use Exception;
use Orhanerday\OpenAi\OpenAi;
use Mis3085\Tiktoken\Facades\Tiktoken;

class Service
{
    protected OpenAi $openAi;
    protected int $maxOutputTokens = 4000;
    protected int $tokenLimit = 100000;
    protected int $safetyMargin = 1000;
    protected array $tokenCache = [];

    public function __construct()
    {
        $openAiKey = config('services.openai.key');
        
        // Faster empty check
        if (!isset($openAiKey[0])) {
            throw new Exception('Make sure the Open API key is added.');
        }

        $this->openAi = new OpenAi($openAiKey);
    }

    public function generateContent($prompt, $input)
    {
        $promptTokens = $this->estimateTokenCount($prompt);
        $availableTokens = $this->tokenLimit - $promptTokens - $this->maxOutputTokens - $this->safetyMargin;

        if ($availableTokens <= 0) {
            throw new Exception('Prompt is too long to process with the current token limit.');
        }

        $inputJson = json_encode($input);
        $inputTokens = $this->estimateTokenCount($inputJson);
        
        return $inputTokens <= $availableTokens 
            ? $this->processSingleRequest($prompt, $input) 
            : $this->processChunkedRequest($prompt, $input, $availableTokens, $inputJson);
    }

    public function processSingleRequest($prompt, $input)
    {

        try {
            // Pre-built message structure for better performance
            $messages = [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => json_encode($input)]
            ];

            $response = $this->openAi->chat([
                'model' => 'gpt-5',
                'messages' => $messages,
            ]);

            return $this->parseResponse($response);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
    

    protected function processChunkedRequest($prompt, $input, $availableTokens, $inputJson = null)
    {
        info('need to chunk');

        $messages = $input['messages'] ?? [];
        if (empty($messages)) {
            throw new Exception('No messages to process.');
        }

        $chunks = $this->splitIntoChunks($messages, $availableTokens);
        $chunkCount = count($chunks);
        
        // Process chunks - consider parallel processing if supported
        $results = [];
        foreach ($chunks as $chunk) {
            $results[] = $this->processSingleRequest($prompt, ['messages' => $chunk]);
        }

        return $this->combineResults($results);
    }

    protected function splitIntoChunks(array $messages, int $availableTokens): array
    {
        $chunks = [];
        $currentChunk = [];
        $currentChunkTokens = 0;

        foreach ($messages as $thread => $message) {
            $messageKey = $thread . '_' . md5(serialize($message));
            
            if (!isset($this->tokenCache[$messageKey])) {
                $this->tokenCache[$messageKey] = $this->estimateTokenCount(json_encode([$thread => $message]));
            }
            
            $messageTokens = $this->tokenCache[$messageKey];

            // Start new chunk if current one would exceed limit
            if ($currentChunkTokens + $messageTokens > $availableTokens && $currentChunk) {
                $chunks[] = $currentChunk;
                $currentChunk = [];
                $currentChunkTokens = 0;
            }

            $currentChunk[$thread] = $message;
            $currentChunkTokens += $messageTokens;
        }

        // Add final chunk
        if ($currentChunk) {
            $chunks[] = $currentChunk;
        }

        return $chunks;
    }

    protected function combineResults(array $partialResults)
    {
        $combined = [];
        $calendarEvents = [];
        
        foreach ($partialResults as $result) {
            // Fast JSON decoding with error suppression for performance
            $decoded = $result;
            if (is_string($result)) {
                $decoded = json_decode($result, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    continue;
                }
            }
            
            if (!is_array($decoded)) continue;

            foreach ($decoded as $key => $data) {
                // Handle calendar_events with direct array merge
                if ($key === 'calendar_events' && is_array($data)) {
                    $calendarEvents = array_merge($calendarEvents, $data);
                    continue;
                }
                
                // Handle summaries with optimized merging
                if (isset($data['summaries']) && is_array($data['summaries'])) {
                    if (!isset($combined[$key])) {
                        $combined[$key] = $data;
                    } else {
                        $combined[$key]['summaries'] = array_merge(
                            $combined[$key]['summaries'] ?? [],
                            $data['summaries']
                        );
                        
                        // Fast field merging using array_diff_key + array_merge
                        $otherFields = array_diff_key($data, ['summaries' => true]);
                        if ($otherFields) {
                            $combined[$key] = array_merge($combined[$key], $otherFields);
                        }
                    }
                } elseif (!isset($combined[$key])) {
                    $combined[$key] = $data;
                }
            }
        }
        
        $combined['calendar_events'] = $calendarEvents;
        
        return json_encode($combined, JSON_PRETTY_PRINT);
    }

    public function parseResponse($response)
    {
        // Use static patterns for faster parsing
        static $errorPattern = '/"error":/';
        
        if (preg_match($errorPattern, $response)) {
            throw new Exception('Something went wrong from OpenAI API');
        }

        // Fast extraction using regex instead of full JSON decode
        if (preg_match('/"content":"(.*?)"/', $response, $matches)) {
            return $matches[1];
        }

        // Fallback to JSON decode
        $aiRecords = json_decode($response, true);

        if (isset($aiRecords['choices'][0]['message']['content'])) {
            return $aiRecords['choices'][0]['message']['content'];
        }

        throw new Exception('No valid response from OpenAI API');
    }

    public function generateEmbedding(string $text): array
    {
        $response = $this->openAi->embeddings([
            'model' => 'text-embedding-ada-002',
            'input' => $text,
        ]);

        // Direct array access without intermediate variable
        $data = json_decode($response, true);
        return $data['data'][0]['embedding'] ?? [];
    }

    public function estimateTokenCount($text)
    {
        if (!$text || !isset($text[0])) return 0;

        // Cache token counts for identical texts
        $hash = md5($text);
        if (!isset($this->tokenCache[$hash])) {
            $this->tokenCache[$hash] = Tiktoken::count($text);
        }
        
        return $this->tokenCache[$hash];
    }

    /**
     * Clear token cache to prevent memory issues
     */
    public function clearCache(): void
    {
        $this->tokenCache = [];
    }
}