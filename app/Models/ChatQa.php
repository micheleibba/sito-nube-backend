<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatQa extends Model
{
    protected $fillable = ['chat_section_id', 'question', 'answer', 'sort_order'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ChatSection::class, 'chat_section_id');
    }
}
