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
     * Buat Prompt
     */
    private function buatPrompt($soal, JawabanEssay $jawabanEssay): string
    {
        $prompt = "Kamu adalah asisten penilaian essay yang objektif dan adil untuk sistem kuis online.\n\n";
        
        // Pertanyaan (strip HTML)
        $pertanyaan = strip_tags($soal->pertanyaan);
        $prompt .= "PERTANYAAN:\n{$pertanyaan}\n\n";
        
        // Jawaban acuan
        if (!empty($soal->jawaban_acuan)) {
            $prompt .= "JAWABAN ACUAN/KUNCI:\n{$soal->jawaban_acuan}\n\n";
        }
        
        // Rubrik penilaian
        if (!empty($soal->rubrik_penilaian)) {
            $prompt .= "RUBRIK PENILAIAN:\n{$soal->rubrik_penilaian}\n\n";
        } else {
            $prompt .= "KRITERIA PENILAIAN:\n";
            $prompt .= "1. Relevansi dengan pertanyaan (30%)\n";
            $prompt .= "2. Kelengkapan jawaban (30%)\n";
            $prompt .= "3. Kejelasan dan struktur penjelasan (25%)\n";
            $prompt .= "4. Tata bahasa (15%)\n\n";
        }
        
        // Jawaban siswa
        $prompt .= "JAWABAN SISWA:\n{$jawabanEssay->jawaban_siswa}\n\n";
        
        // Instruksi
        $prompt .= "TUGAS ANDA:\n";
        $prompt .= "1. Nilai jawaban siswa secara objektif berdasarkan kriteria di atas\n";
        $prompt .= "2. Berikan skor dalam skala 0-100 (integer)\n";
        $prompt .= "3. Berikan feedback konstruktif dan membantu (2-4 kalimat)\n";
        $prompt .= "4. Fokus pada substansi jawaban, bukan panjang teks\n\n";
        
        $prompt .= "FORMAT OUTPUT (STRICT JSON, NO MARKDOWN):\n";
        $prompt .= "{\n";
        $prompt .= '  "skor": 85,'."\n";
        $prompt .= '  "feedback": "Jawaban sudah relevan dengan pertanyaan dan cukup lengkap. Penjelasan cukup jelas namun bisa lebih detail. Tata bahasa baik."'."\n";
        $prompt .= "}\n\n";
        
        $prompt .= "PENTING:\n";
        $prompt .= "- Output HANYA JSON murni tanpa ```json atau markdown\n";
        $prompt .= "- Skor harus integer antara 0-100\n";
        $prompt .= "- Feedback dalam bahasa Indonesia yang baik\n";
        $prompt .= "- Jangan tambahkan teks apapun selain JSON\n";
        
        return $prompt;
    }

    /**
     * Kirim ke Gemini API
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
                    "temperature" => 0.3,
                    "maxOutputTokens" => 1000,
                    "topP" => 0.8,
                    "topK" => 40,
                ]
            ]);

        if (!$response->successful()) {
            throw new \Exception("Gemini API Error: " . $response->status() . " - " . $response->body());
        }

        return $response->json();
    }

    /**
     * Parse Response
     */
    private function parseResponse(array $responseData): array
    {
        $text = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';
        
        if (empty($text)) {
            throw new \Exception("Response AI kosong");
        }
        
        // Bersihkan markdown
        $text = preg_replace('/```json\n?/', '', $text);
        $text = preg_replace('/```\n?/', '', $text);
        $text = trim($text);
        
        // Decode JSON
        $hasil = json_decode($text, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON Parse Error', [
                'error' => json_last_error_msg(),
                'raw_text' => $text
            ]);
            throw new \Exception("Format response tidak valid: " . json_last_error_msg());
        }
        
        // Validasi
        if (!isset($hasil['skor']) || !isset($hasil['feedback'])) {
            throw new \Exception("Response tidak lengkap (missing skor/feedback)");
        }
        
        // Validasi skor
        $skor = (float) $hasil['skor'];
        $skor = max(0, min(100, $skor)); // Clamp 0-100
        
        return [
            'skor' => $skor,
            'feedback' => $hasil['feedback']
        ];
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
            'respon_ai' => json_encode($responseData, JSON_PRETTY_PRINT),
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
        Log::error('Gemini AI Error', [
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