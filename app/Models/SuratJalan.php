<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SuratJalan extends Model
{
    protected $table = 'surat_jalan';

    protected $fillable = [
        'nomor_surat',
        'jenis_serah_terima',
        'pengirim_id',
        'dikirim_at',
        'penerima_id',
        'diterima_at',
        'status',
        'catatan',
    ];

    protected $casts = [
        'dikirim_at' => 'datetime',
        'diterima_at' => 'datetime',
    ];

    public function pengirim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SuratJalanItem::class, 'surat_jalan_id');
    }

    public function workOrders(): BelongsToMany
    {
        return $this->belongsToMany(WorkOrder::class, 'surat_jalan_items', 'surat_jalan_id', 'work_order_id')
                    ->withPivot('kondisi_serah_terima')
                    ->withTimestamps();
    }

    public static function generateNomorSurat(string $jenis): string
    {
        $dateStr = now()->format('Ymd');
        $prefixMap = [
            'sortir_to_produksi' => 'SJ-SP',
            'produksi_to_post_qc' => 'SJ-PP',
            'post_qc_to_office' => 'SJ-PO',
        ];

        $prefix = $prefixMap[$jenis] ?? 'SJ';
        
        $countToday = self::whereDate('created_at', now()->today())->count() + 1;
        
        return sprintf('%s-%s-%04d', $prefix, $dateStr, $countToday);
    }
}
