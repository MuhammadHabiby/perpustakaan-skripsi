<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
                $originalSlug = $category->slug;
                $count = 1;
                while (static::where('slug', $category->slug)->exists()) {
                    $category->slug = "{$originalSlug}-" . $count++;
                }
            }
        });

        static::updating(function (Category $category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
                $originalSlug = $category->slug;
                $count = 1;
                while (static::where('slug', $category->slug)->where('id', '!=', $category->id)->exists()) {
                    $category->slug = "{$originalSlug}-" . $count++;
                }
            }
        });
    }
}
