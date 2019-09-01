<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'content' => 'array'
    ];

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescriptionAttribute()
    {
        $content = array_map(function ($block) {
            return array_get($block, 'data.text');
        }, $this->content['blocks']);

        $description = implode($content, ' ');

        return mb_strimwidth($description, 0, 255, "...");

    }
}
