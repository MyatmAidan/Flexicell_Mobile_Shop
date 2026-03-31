<?php

namespace App\Observers;

use App\Models\Device;
use App\Support\VariantStock;

class DeviceObserver
{
    public function created(Device $device): void
    {
        if ($device->product_variant_id) {
            VariantStock::syncSingleVariantStock((int) $device->product_variant_id);
        }
    }

    public function updated(Device $device): void
    {
        if ($device->product_variant_id) {
            VariantStock::syncSingleVariantStock((int) $device->product_variant_id);
        }

        if ($device->wasChanged('product_variant_id') && $device->getOriginal('product_variant_id')) {
            VariantStock::syncSingleVariantStock((int) $device->getOriginal('product_variant_id'));
        }
    }

    public function deleted(Device $device): void
    {
        if ($device->product_variant_id) {
            VariantStock::syncSingleVariantStock((int) $device->product_variant_id);
        }
    }
}
