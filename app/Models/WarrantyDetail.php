<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarrantyDetail extends Model
{
    protected $fillable = [
        'warranty_id',
        'device_id',
        'customer_id',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date->isPast();
    }

    public function getComputedStatusAttribute(): string
    {
        return $this->end_date->isPast() ? 'expired' : 'active';
    }
}
