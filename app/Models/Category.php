<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 
        'name', 
        'logo_url', 
        'domain', 
        'is_active', 
        'config',
        'favicon_url',
        'site_description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image_url',
        'primary_color',
        'secondary_color',
        'last_deployed_at',
        'deploy_status',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
        'last_deployed_at' => 'datetime',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
