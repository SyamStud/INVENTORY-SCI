<?php

namespace App\Services;

use Exception;
use CURLFile;

class GotenbergService
{
    protected string $apiUrl;

    public function __construct()
    {
        // Set URL API Gotenberg
        $this->apiUrl = 'http://localhost:3000/forms/libreoffice/convert';
    }

    /**
     * Konversi file DOCX ke PDF menggunakan Gotenberg.
     *
     * @param string $inputPath Path file DOCX yang akan dikonversi
     * @param string $outputPath Path untuk menyimpan file PDF hasil konversi
     * @return void
     * @throws Exception
     */
    public function convertDocxToPdf(string $inputPath, string $outputPath): void
    {
        // Periksa apakah file input ada
        if (!file_exists($inputPath)) {
            throw new Exception("File input tidak ditemukan: $inputPath");
        }

        // Inisialisasi cURL
        $curl = curl_init();

        // Set opsi cURL
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiUrl,               // URL endpoint Gotenberg
            CURLOPT_POST => true,                      // Metode POST
            CURLOPT_RETURNTRANSFER => true,            // Kembalikan hasil sebagai string
            CURLOPT_POSTFIELDS => [
                'files' => new CURLFile($inputPath),   // Kirim file DOCX
            ],
        ]);

        // Kirim request ke Gotenberg API
        $response = curl_exec($curl);

        // Periksa apakah ada error pada cURL
        if (curl_errno($curl)) {
            throw new Exception("cURL Error: " . curl_error($curl));
        }

        // Tutup cURL
        curl_close($curl);

        // Simpan hasil ke file PDF
        if (file_put_contents($outputPath, $response) === false) {
            throw new Exception("Gagal menyimpan file output: $outputPath");
        }
    }
}
