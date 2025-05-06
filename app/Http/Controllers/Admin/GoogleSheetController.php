<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleSheetService;
use App\Models\Sale;
use Illuminate\Http\Request;

class GoogleSheetController extends Controller
{
    protected $googleSheetService;

    public function __construct(GoogleSheetService $googleSheetService)
    {
        $this->googleSheetService = $googleSheetService;
    }

    /**
     * Manually export transaction data to Google Spreadsheet.
     */
    public function exportTransactions()
    {
        // Fetch transaction data to export
        $sales = Sale::with('salesDetails')->get();

        // Prepare data for Google Sheets
        $values = [];
        // Header row
        $values[] = ['Sale ID', 'Date', 'Customer', 'Total', 'Details'];

        foreach ($sales as $sale) {
            $details = $sale->salesDetails->map(function ($detail) {
                return $detail->product_name . ' x ' . $detail->quantity;
            })->implode(', ');

            $values[] = [
                $sale->id,
                $sale->created_at->toDateTimeString(),
                $sale->customer_name ?? '',
                $sale->total_amount,
                $details,
            ];
        }

        $success = $this->googleSheetService->writeData($values);

        if ($success) {
            return redirect()->back()->with('success', 'Transactions exported to Google Spreadsheet successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to export transactions to Google Spreadsheet.');
        }
    }
}
