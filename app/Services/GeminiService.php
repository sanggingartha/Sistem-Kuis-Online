<?php
// app/Services/GeminiService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\JawabanEssay;
use App\Models\PenilaianAi;

class GeminiService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1/models';
    private string $model = 'gemini-2.5-flash';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    /**
     * Test Koneksi
     */
    public function testConnection(): array
    {
        try {
            $response = Http::timeout(30)->post(
                "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    "contents" => [
                        [
                            "parts" => [
                                ["text" => "Halo Gemini, ini test koneksi dari WanaQuiz"]
                            ]
                        ]
                    ]
                ]
            );
            
            return [
                'success' => true,
                'data' => $response->json()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * MAIN: Nilai Jawaban Essay
     */
    public function nilaiJawabanEssay(JawabanEssay $jawabanEssay): array
    {
        $startTime = microtime(true);
        
        try {
            // Update status
            $jawabanEssay->update(['status_penilaian' => 'sedang_proses']);

            // Load relasi
            $jawabanEssay->load('soal');
            $soal = $jawabanEssay->soal;
            
            // Buat prompt
            $prompt = $this->buatPrompt($soal, $jawabanEssay);

            // Kirim ke Gemini
            $responseData = $this->kirimKeGemini($prompt);
            
            // Parse response
            $hasil = $this->parseResponse($responseData);
            
            // Hitung waktu proses (ms)
            $waktuProses = (int) round((microtime(true) - $startTime) * 1000);

            // Simpan hasil
            $this->simpanHasil($jawabanEssay, $hasil, $prompt, $responseData, $waktuProses);

            return [
                'success' => true,
                'skor' => $hasil['skor'],
                'feedback' => $hasil['feedback'],
                'waktu_proses' => $waktuProses,
            ];

        } catch (\Exception $e) {
            $this->handleError($jawabanEssay, $e, $prompt ?? '');
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Buat Prompt - ULTRA COMPACT VERSION
     */
    private function buatPrompt($soal, JawabanEssay $jawabanEssay): string
    {
        $pertanyaan = strip_tags($soal->pertanyaan);
        $kunci = $soal->jawaban_acuan ?? 'Tidak ada kunci';
        $jawaban = $jawabanEssay->jawaban_siswa;
        
        // PROMPT SANGAT RINGKAS untuk hemat token
        $prompt = "Nilai essay ini 0-100:\n\n";
        $prompt .= "Q: {$pertanyaan}\n";
        $prompt .= "Kunci: {$kunci}\n";
        $prompt .= "Jawaban: {$jawaban}\n\n";
        $prompt .= "Output JSON saja:\n";
        $prompt .= '{"skor":85,"feedback":"Singkat 1-2 kalimat"}';
        
        return $prompt;
    }

    /**
     * Kirim ke Gemini API - FIXED WITH MORE TOKENS
     */
    private function kirimKeGemini(string $prompt): array
    {
        $response = Http::timeout(60)
            ->post("{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}", [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $prompt]
                        ]
                    ]
                ],
                "generationConfig" => [
                    "temperature" => 0.1,
                    "maxOutputTokens" => 1000,  // DINAIKKAN dari 300 ke 1000
                    "topP" => 0.9,
                    "topK" => 20,
                ]
            ]);

        if (!$response->successful()) {
            throw new \Exception("Gemini API Error: " . $response->status() . " - " . $response->body());
        }

        return $response->json();
    }

    /**
     * Parse Response - WITH INCOMPLETE JSON HANDLER
     */
    private function parseResponse(array $responseData): array
    {
        // Log full response
        Log::info('=== GEMINI FULL RESPONSE ===', [
            'response' => json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        ]);

        // Validasi struktur dasar
        if (!isset($responseData['candidates'][0]['content']['parts'])) {
            throw new \Exception("Response tidak valid dari Gemini AI");
        }

        // Ekstrak text
        $parts = $responseData['candidates'][0]['content']['parts'];
        $rawText = '';
        foreach ($parts as $part) {
            if (isset($part['text'])) {
                $rawText .= $part['text'];
            }
        }
        
        if (empty($rawText)) {
            throw new \Exception("Response kosong dari AI");
        }

        Log::info('=== RAW TEXT ===', ['text' => $rawText]);

        // CEK FINISH REASON
        $finishReason = $responseData['candidates'][0]['finishReason'] ?? 'UNKNOWN';
        Log::info('=== FINISH REASON ===', ['reason' => $finishReason]);

        // Clean JSON
        $cleaned = $this->aggressiveCleanJson($rawText);
        
        Log::info('=== CLEANED TEXT ===', ['cleaned' => $cleaned]);

        // HANDLE INCOMPLETE JSON (jika MAX_TOKENS)
        if ($finishReason === 'MAX_TOKENS' && !$this->isCompleteJson($cleaned)) {
            Log::warning('=== INCOMPLETE JSON DETECTED ===');
            $cleaned = $this->fixIncompleteJson($cleaned);
            Log::info('=== FIXED JSON ===', ['fixed' => $cleaned]);
        }

        // Parse JSON
        $decoded = json_decode($cleaned, true);
        
        if ($decoded === null) {
            $jsonError = json_last_error_msg();
            Log::error('=== JSON DECODE FAILED ===', [
                'error' => $jsonError,
                'cleaned_text' => $cleaned,
                'raw_text' => substr($rawText, 0, 500)
            ]);
            throw new \Exception("Gagal decode JSON: {$jsonError}");
        }

        // Validasi fields
        if (!isset($decoded['skor'])) {
            throw new \Exception("Field 'skor' tidak ada");
        }
        if (!isset($decoded['feedback'])) {
            throw new \Exception("Field 'feedback' tidak ada");
        }

        // Normalize skor
        $skor = is_numeric($decoded['skor']) ? (float)$decoded['skor'] : 0;
        $skor = max(0, min(100, $skor));

        // Validate feedback
        $feedback = trim($decoded['feedback']);
        if (empty($feedback)) {
            $feedback = "Jawaban dinilai dengan skor {$skor}";
        }

        Log::info('=== PARSING SUCCESS ===', [
            'skor' => $skor,
            'feedback' => $feedback
        ]);

        return [
            'skor' => $skor,
            'feedback' => $feedback
        ];
    }

    /**
     * Check if JSON is complete
     */
    private function isCompleteJson(string $text): bool
    {
        $openBraces = substr_count($text, '{');
        $closeBraces = substr_count($text, '}');
        $openQuotes = substr_count($text, '"');
        
        // JSON lengkap jika:
        // 1. Jumlah { dan } sama
        // 2. Jumlah " genap
        return ($openBraces === $closeBraces) && ($openQuotes % 2 === 0);
    }

    /**
     * Fix incomplete JSON
     */
    private function fixIncompleteJson(string $text): string
    {
        // Jika terpotong di tengah string feedback
        if (substr_count($text, '"') % 2 !== 0) {
            // Tutup string yang belum ditutup
            $text .= '"';
        }
        
        // Tutup JSON object jika belum
        $openBraces = substr_count($text, '{');
        $closeBraces = substr_count($text, '}');
        
        while ($closeBraces < $openBraces) {
            $text .= '}';
            $closeBraces++;
        }
        
        return $text;
    }

    /**
     * AGGRESSIVE JSON CLEANING
     */
    private function aggressiveCleanJson(string $text): string
    {
        // 1. Remove markdown
        $text = preg_replace('/```json\s*/i', '', $text);
        $text = preg_replace('/```\s*/i', '', $text);
        
        // 2. Remove AI prefixes
        $text = preg_replace('/^.*?Here.*?:/im', '', $text);
        $text = preg_replace('/^.*?berikut.*?:/im', '', $text);
        $text = preg_replace('/^.*?JSON.*?:/im', '', $text);
        
        // 3. Extract from first { to last }
        if (preg_match('/(\{.*\})/s', $text, $matches)) {
            $text = $matches[1];
        }
        
        // 4. Remove text before first {
        $firstBrace = strpos($text, '{');
        if ($firstBrace !== false && $firstBrace > 0) {
            $text = substr($text, $firstBrace);
        }
        
        // 5. Clean whitespace but preserve spaces in strings
        $text = trim($text);
        
        return $text;
    }

    /**
     * Simpan Hasil
     */
    private function simpanHasil(
        JawabanEssay $jawabanEssay, 
        array $hasil, 
        string $prompt, 
        array $responseData, 
        int $waktuProses
    ): void {
        // Update jawaban essay
        $jawabanEssay->nilaiOlehAI($hasil['skor'], $hasil['feedback']);

        // Log ke penilaian_ai
        PenilaianAi::create([
            'jawaban_essay_id' => $jawabanEssay->id,
            'prompt_dikirim' => $prompt,
            'respon_ai' => json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'waktu_proses' => $waktuProses,
            'status_request' => 'sukses',
            'model_versi' => $this->model,
            'token_digunakan' => $responseData['usageMetadata']['totalTokenCount'] ?? null,
        ]);

        // Update total nilai hasil kuis
        $jawabanEssay->hasilKuis->updateTotalNilai();
    }

    /**
     * Handle Error
     */
    private function handleError(JawabanEssay $jawabanEssay, \Exception $exception, string $prompt): void
    {
        // Update status error
        $jawabanEssay->update(['status_penilaian' => 'error']);

        // Log error ke database
        PenilaianAi::create([
            'jawaban_essay_id' => $jawabanEssay->id,
            'prompt_dikirim' => $prompt,
            'respon_ai' => $exception->getMessage(),
            'status_request' => 'gagal',
            'error_message' => $exception->getMessage(),
            'model_versi' => $this->model,
        ]);

        // Log untuk debugging
        Log::error('=== GEMINI AI ERROR ===', [
            'jawaban_essay_id' => $jawabanEssay->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    /**
     * Nilai Batch Essay
     */
    public function nilaiBatchEssay(int $hasilKuisId): array
    {
        $jawabanEssays = JawabanEssay::where('hasil_kuis_id', $hasilKuisId)
            ->whereIn('status_penilaian', ['belum_dinilai', 'error'])
            ->get();

        $results = [
            'total' => $jawabanEssays->count(),
            'success' => 0,
            'failed' => 0,
            'details' => []
        ];

        foreach ($jawabanEssays as $jawaban) {
            $result = $this->nilaiJawabanEssay($jawaban);
            
            if ($result['success']) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
            
            $results['details'][] = [
                'id' => $jawaban->id,
                'result' => $result
            ];
        }

        return $results;
    }
}