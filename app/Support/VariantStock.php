<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class VariantStock
{
    public static function getOrCreateColorOption(?string $name, ?string $value): ?int
    {
        if (empty($name) && empty($value)) return null;

        $name = trim($name ?? ($value ?? 'Unnamed'));
        $value = trim($value ?? ($name ?? '#000000'));

        $record = DB::table('color_options')->where('value', $value)->first();
        if ($record instanceof \stdClass && isset($record->id)) {
            return (int) $record->id;
        }

        return DB::table('color_options')->insertGetId([
            'name' => $name,
            'value' => $value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public static function upsertOption(string $table, ?string $value): ?int
    {
        if (empty($value)) return null;
        $v = trim($value);
        if ($v === '' || strtoupper($v) === 'TBD') {
            return null;
        }

        $record = DB::table($table)->where('value', $v)->first();
        if ($record instanceof \stdClass && isset($record->id)) {
            return (int) $record->id;
        }

        return DB::table($table)->insertGetId([
            'name' => $v,
            'value' => $v,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

