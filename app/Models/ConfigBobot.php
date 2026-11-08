<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigBobot extends Model
{
    use HasFactory;

    protected $table = 'config_bobots';

    protected $fillable = [
        'bobot_dudi',
        'bobot_guru',
        'bobot_sidang',
        'updated_by',
    ];

    protected $casts = [
        'bobot_dudi'   => 'decimal:2',
        'bobot_guru'   => 'decimal:2',
        'bobot_sidang' => 'decimal:2',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function getActive()
    {
        $config = static::first();

        if (!$config) {
            $config = static::create([
                'bobot_dudi'   => 50.00,
                'bobot_guru'   => 40.00,
                'bobot_sidang' => 10.00,
            ]);
        }

        return $config;
    }
}