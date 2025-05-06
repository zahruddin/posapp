<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleSheetService
{
    protected $client;
    protected $service;
    protected $spreadsheetId;
    protected $headers = [
        'ID Transaksi',
        'Waktu',
        'Kasir',
        'Outlet',
        'Produk',
        'Jumlah',
        'Harga',
        'Total',
        'Metode Bayar',
        'Status'
    ];

    public function __construct()
    {
        try {
            $this->spreadsheetId = config('services.google.sheet_id');
            if (empty($this->spreadsheetId)) {
                throw new \Exception('Google Sheet ID not configured');
            }

            $credentialsPath = storage_path('app/google/credentials.json');
            if (!file_exists($credentialsPath)) {
                throw new \Exception('Google credentials file not found');
            }

            $this->client = new Client();
            $this->client->setApplicationName('POS App - Sheets Integration');
            $this->client->setScopes([Sheets::SPREADSHEETS]);
            $this->client->setAuthConfig($credentialsPath);
            $this->client->setAccessType('offline');

            $this->service = new Sheets($this->client);
        } catch (\Exception $e) {
            Log::error('GoogleSheetService initialization error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function sheetExists(string $outletName): bool
    {
        try {
            $spreadsheet = $this->service->spreadsheets->get($this->spreadsheetId);
            foreach ($spreadsheet->getSheets() as $sheet) {
                if ($sheet->getProperties()->getTitle() === $outletName) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            Log::error('GoogleSheetService sheetExists error: ' . $e->getMessage());
            return false;
        }
    }

    public function createSheet(string $outletName): bool
    {
        try {
            // Create sheet
            $body = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
                'requests' => [
                    'addSheet' => [
                        'properties' => [
                            'title' => $outletName,
                            'gridProperties' => [
                                'frozenRowCount' => 1
                            ]
                        ]
                    ]
                ]
            ]);

            $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $body);

            // Add headers
            $this->writeData([
                $this->headers
            ], $outletName . '!A1');

            return true;
        } catch (\Exception $e) {
            Log::error('GoogleSheetService createSheet error: ' . $e->getMessage());
            return false;
        }
    }

    public function writeData(array $values, string $range): bool
    {
        try {
            if (empty($values)) {
                Log::error('GoogleSheetService: No values provided');
                return false;
            }

            // Format numeric values
            foreach ($values as &$row) {
                foreach ($row as &$value) {
                    if (is_numeric($value)) {
                        $value = number_format($value, 0, ',', '.');
                    }
                }
            }

            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);

            $params = [
                'valueInputOption' => 'RAW'
            ];

            $result = $this->service->spreadsheets_values->append(
                $this->spreadsheetId,
                $range,
                $body,
                $params
            );

            Log::info('GoogleSheetService: Data written successfully', [
                'range' => $range,
                'rows' => count($values)
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('GoogleSheetService writeData error: ' . $e->getMessage());
            return false;
        }
    }

    public function getNextRow(string $outletName): int
    {
        try {
            $range = $outletName . '!A:A';
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            return count($values) + 1;
        } catch (\Exception $e) {
            Log::error('GoogleSheetService getNextRow error: ' . $e->getMessage());
            return 2; // Return 2 as row 1 contains headers
        }
    }
}
