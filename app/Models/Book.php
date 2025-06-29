<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category_id',
        'book_id',
        'author_id',
        'publisher_id',
        'isbn',
        'publication_year',
        'synopsis',
        'cover_image',
        'location',
        'slug'
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Book $book) {
            if (empty($book->slug)) {
                $book->slug = Str::slug($book->title);
                $originalSlug = $book->slug;
                $count = 1;
                while (static::where('slug', $book->slug)->exists()) {
                    $book->slug = "{$originalSlug}-" . $count++;
                }
            }
        });

        static::updating(function (Book $book) {
            if ($book->isDirty('title')) {
                $book->slug = Str::slug($book->title);
                $originalSlug = $book->slug;
                $count = 1;
                while (static::where('slug', $book->slug)->where('id', '!=', $book->id)->exists()) {
                    $book->slug = "{$originalSlug}-" . $count++;
                }
            }
        });
    }
}
