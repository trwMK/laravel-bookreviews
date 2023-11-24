<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['review', 'rating'];

    public function book() {
        return $this->belongsTo(Book::class);
    }

    //this method is called when the model is booted and it is called only once
    //when the model is booted, it will listen for the updated and deleted events
    //and when the updated or deleted events are fired, it will delete the cache
    //for the book that is updated or deleted
    protected static function booted()
    {
        static::updated(fn(Review $review) => cache()->forget("book:" . $review->book_id));
        static::deleted(fn(Review $review) => cache()->forget("book:" . $review->book_id));
        static::created(fn(Review $review) => cache()->forget("book:" . $review->book_id));
    }
}
