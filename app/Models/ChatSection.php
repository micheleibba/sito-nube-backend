<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSection extends Model
{
    protected $fillable = ['name', 'slug', 'subtitle', 'sort_order', 'active'];

    public function qas(): HasMany
    {
        return $this->hasMany(ChatQa::class)->orderBy('sort_order');
    }
}
