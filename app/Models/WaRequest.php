<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Rating;

class WaRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'category_id',
        'problem',
        'problem_type',
        'urgency',
        'description',
        'status',
        'selected_plumber_id',
        'completed_at',
        'rating',
        'rating_comment'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function selectedPlumber()
    {
        return $this->belongsTo(User::class, 'selected_plumber_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class, 'request_id');
    }

    /**
     * Get available statuses
     */
    public static function getAvailableStatuses(): array
    {
        return [
            'broadcasting' => 'Broadcasting to service providers',
            'active' => 'Service provider assigned',
            'in_progress' => 'Work in progress',
            'completed' => 'Job completed',
            'cancelled' => 'Request cancelled'
        ];
    }
}
