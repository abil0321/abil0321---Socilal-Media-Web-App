<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
    protected $table = 'posts';
    protected $fillable = ['user_id', 'content', 'image_url'];

    public function comment():HasMany
    {
        return $this->hasMany(Comment::class, 'post_id')->orderBy('updated_at', 'desc');
    }

    public function like():HasMany
    {
        return $this->hasMany(Like::class, 'post_id')->orderBy('created_at', 'desc');
    }
}
