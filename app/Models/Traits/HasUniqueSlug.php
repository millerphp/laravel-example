<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait HasUniqueSlug
{
    protected static function bootHasUniqueSlug()
    {
        static::creating(function (Model $model) {
            $model->generateUniqueSlug();
        });
    }

    protected function generateUniqueSlug(): void
    {
        $slug = Str::slug($this->title);
        $originalSlug = $slug;
        $count = 2;

        while (static::where('slug', $slug)
            ->where('id', '!=', $this->id ?? 0)
            ->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $this->slug = $slug;
    }
} 