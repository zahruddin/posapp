<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

trait LogActivityAuto
{
    protected static function bootLogActivityAuto()
    {
        static::updating(function ($model) {
            $original = $model->getOriginal(); // Data sebelum update
            $changes = $model->getDirty(); // Data yang berubah saja

            $description = '';
            foreach ($changes as $field => $newValue) {
                $oldValue = $original[$field] ?? null;
                $description .= "Field '$field' diubah dari '$oldValue' menjadi '$newValue'. ";
            }

            if ($description) {
                ActivityLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => 'update',
                    'table_name' => $model->getTable(),
                    'record_id'  => $model->getKey(),
                    'description'=> trim($description),
                ]);
            }
        });

        static::created(function ($model) {
            ActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'create',
                'table_name' => $model->getTable(),
                'record_id'  => $model->getKey(),
                'description'=> 'Menambahkan data baru di ' . $model->getTable(),
            ]);
        });

        static::deleted(function ($model) {
            ActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'delete',
                'table_name' => $model->getTable(),
                'record_id'  => $model->getKey(),
                'description'=> 'Menghapus data di ' . $model->getTable() . ' dengan ID ' . $model->getKey(),
            ]);
        });
    }
}
