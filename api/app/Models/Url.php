<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Url
 * 
 * @property integer $id
 * @property string $link
 * @property integer $user_id
 * @property User $user
 * @property Collection $tags
 * @property Collection $likes
 * @property string $created_at
 * @property string $updated_at
 * 
 * @package App\Models
 */
class Url extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'urls_tags_pivot', 'url_id', 'tag_id');
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'urls_likes_pivot', 'url_id', 'user_id');
    }
}
