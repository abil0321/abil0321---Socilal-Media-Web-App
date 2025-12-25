<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Like extends Model
{
    use SoftDeletes;
    protected $table = 'likes';
    protected $fillable = [
        'user_id',
        'post_id'
    ];
    public function post():BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'post_id');
    }
}
