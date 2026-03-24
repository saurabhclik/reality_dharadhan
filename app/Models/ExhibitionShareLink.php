<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExhibitionShareLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'exhibition_id',
        'share_code',
        'user_id',
        'device_id',
        'expires_at',
        'max_uses',
        'used_count',
        'is_active'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateShareCode($exhibitionId)
    {
        $unique = false;
        $code = '';
        
        while (!$unique)
        {
            $code = strtoupper(substr(md5(uniqid($exhibitionId . time(), true)), 0, 8));
            $exists = self::where('share_code', $code)->exists();
            if (!$exists) 
            {
                $unique = true;
            }
        }
        
        return $code;
    }
}