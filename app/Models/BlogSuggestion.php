<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogSuggestion extends Model
{
    protected $fillable = [
        'source_url', 'source_name', 'original_title',
        'title_en', 'title_it', 'text_en', 'text_it',
        'meta_description_en', 'meta_description_it',
        'cover_image_url', 'cover_image_path', 'status',
    ];
}
