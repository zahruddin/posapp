<?php

namespace App\Observers;

use App\Models\Sale;
use App\Services\GoogleSheetService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SaleObserver
{
    protected $googleSheetService;

    public function __construct(GoogleSheetService $googleSheetService)
    {
        $this->googleSheetService = $googleSheetService;
    }

    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale)
    {
        DB::afterCommit(function () use ($sale) {
            // Check if this sale has already been processed
            $cacheKey = "sale_processed_{$sale->id}";
            if (Cache::has($cacheKey)) {
                Log::info('Sale already processed, skipping', ['sale_id' => $sale->id]);
                return;
            }

            try {
                // Mark this sale as being processed
                Cache::put($cacheKey, true, now()->addMinutes(5));

                // Load fresh data with all relationships
                $sale = Sale::with(['details', 'outlet', 'user'])
                           ->where('id', $sale->id)
                           ->first();

                if (!$this->validateSaleData($sale)) {
                    return;
                }

                $outletName = $sale->outlet->nama_outlet;

                // Create sheet if it doesn't exist
                if (!$this->googleSheetService->sheetExists($outletName)) {
                    if (!$this->googleSheetService->createSheet($outletName)) {
                        Log::error('Failed to create sheet', ['outlet' => $outletName]);
                        return;
                    }
                }

                // Prepare data rows
                $rows = [];
                foreach ($sale->details as $detail) {
                    $rows[] = [
                        $sale->id,
                        $sale->created_at->format('Y-m-d H:i:s'),
                        $sale->user->name,
                        $outletName,
                        $detail->nama_produk,
                        $detail->jumlah,
                        $detail->harga_produk,
                        $detail->total,
                        $sale->metode_bayar ?? '-',
                        $sale->status_bayar ?? 'Lunas'
                    ];
                }

                // Write data
                $range = $outletName . '!A' . $this->googleSheetService->getNextRow($outletName);
                if (!$this->googleSheetService->writeData($rows, $range)) {
                    Log::error('Failed to write data', [
                        'sale_id' => $sale->id,
                        'outlet' => $outletName
                    ]);
                    Cache::forget($cacheKey); // Allow retry if failed
                    return;
                }

                Log::info('Sale recorded successfully', [
                    'sale_id' => $sale->id,
                    'outlet' => $outletName,
                    'rows' => count($rows)
                ]);

            } catch (\Exception $e) {
                Log::error('Error processing sale', [
                    'sale_id' => $sale->id ?? null,
                    'error' => $e->getMessage()
                ]);
                Cache::forget($cacheKey); // Allow retry if failed
            }
        });
    }

    /**
     * Validate sale data
     */
    protected function validateSaleData(Sale $sale): bool
    {
        if (!$sale) {
            Log::error('Sale not found');
            return false;
        }

        if (!$sale->outlet) {
            Log::error('Outlet not found', ['sale_id' => $sale->id]);
            return false;
        }

        if (!$sale->user) {
            Log::error('User not found', ['sale_id' => $sale->id]);
            return false;
        }

        if (!$sale->details || $sale->details->isEmpty()) {
            Log::error('No sale details found', ['sale_id' => $sale->id]);
            return false;
        }

        return true;
    }
}
