<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaFlow extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'entry_keyword',
        'target_role',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the category this flow belongs to (if any)
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all nodes for this flow
     */
    public function nodes()
    {
        return $this->hasMany(WaNode::class, 'flow_id')->orderBy('sort');
    }
}
