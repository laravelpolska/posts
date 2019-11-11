<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', Carbon::now());
    }
}
