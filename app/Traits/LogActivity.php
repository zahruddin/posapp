<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogActivity
{
    public function logActivityWithOriginal($model, $originalModel, $action)
    {
        try {
            $userId = Auth::id();
            $tableName = $model->getTable();
            $recordId = $model->getKey();

            // Ambil nama outlet
            $outletName = $model->outlet ? $model->outlet->nama_outlet : 'Outlet tidak ditemukan';

            // Buat deskripsi perubahan
            $description = $this->buildDescriptionFromOriginal($originalModel, $model, $action);

            ActivityLog::create([
                'user_id'    => $userId,
                'action'     => $action,
                'table_name' => $tableName,
                'record_id'  => $recordId,
                'description'=> $description . ' | Outlet: ' . $outletName,
            ]);
        } catch (\Exception $e) {
            logger('Log Activity Error: ' . $e->getMessage());
        }
    }

    private function buildDescriptionFromOriginal($original, $updated, $action)
    {
        $before = $original->toArray();
        $after = $updated->toArray();

        $ignoreFields = ['updated_at', 'created_at'];

        $changes = [];
        foreach ($after as $key => $value) {
            if (array_key_exists($key, $before) && !in_array($key, $ignoreFields) && $before[$key] != $value) {
                $changes[] = "$key: {$before[$key]} → {$value}";
            }
        }

        if (empty($changes)) {
            return ucfirst($action) . ' data tanpa perubahan.';
        }

        // Ambil nama produk jika ada
        $productName = $after['nama_produk'] ?? $before['nama_produk'] ?? 'Produk tidak diketahui';

        // Tambahkan nama produk ke deskripsi
        return "{$productName}: " . implode(', ', $changes);
    }



    

}
