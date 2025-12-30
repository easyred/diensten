<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeleRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'postal_code',
        'city',
        'contacted_date',
        'status',
        'message',
    ];

    protected $casts = [
        'contacted_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
