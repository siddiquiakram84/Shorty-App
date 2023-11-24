<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'url_id', 'ip_address', 'user_agent', 'os', 'device', 'browser','user_location', 'accessed_at',
    ];

    public function url()
    {
        return $this->belongsTo(Url::class);
    }
}
