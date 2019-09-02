<?php

namespace App;

use EditorJS\EditorJS;
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
        }, $this->content['blocks'] ?? []);

        $description = implode($content, ' ');

        return mb_strimwidth($description, 0, 255, "...");

    }

    /**
     * Get the html version of the content.
     *
     * @return string
     */
    public function getHtmlAttribute()
    {
        $blocks = array_get($this->content, 'blocks', []);
        $output = '';

        foreach ($blocks as $block) {
            switch ($block['type']) {
                case 'paragraph':
                $text = array_get($block, 'data.text');
                $output .= "<p>{$text}</p>";
                break;

                case 'header':
                $level = array_get($block, 'data.level');
                $text = array_get($block, 'data.text');
                $output .= "<h{$level}>{$text}</h{$level}>";
                break;
            }
        }

        return $output;
    }
}
