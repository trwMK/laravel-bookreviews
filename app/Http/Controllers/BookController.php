<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //get the title query string from the request
        $title = $request->input("title");

        //get the filter query string from the request
        $filter = $request->input("filter", "");

        //filter the books by title using the scope -> return an array of books
        $books = Book::when($title, fn($query, $title) => $query->title($title));
        
        //filter the books by the filter query string
        $books = match($filter) {
            'popular_last_month' => $books->popularLastMonth(),
            'popular_last_6months' => $books->popularLast6Months(),
            'highest_rated_last_month' => $books->highestRatedLastMonth(),
            'highest_rated_last_6months' => $books->highestRatedLast6Months(),
            default => $books->latest()->withAvgRating()->withReviewsCount(),
        };

        //get the books from the database
        //$books = $books->get();

        //get the books from the cache if the books are already stored in the cache and it stores the books for 1 hour (3600 seconds)
        //otherwise get the books from the database and store them in the cache for 1 hour (3600 seconds)
        //$books = Cache::remember("books", 3600, fn() => $books->get()); //OR useing the cache() helper function
        //U need a unique key for each cache item, so u can use the current url as the key and it is more secure
        $cacheKey = 'books:' . $filter . ':' . $title;
        $books = 
         cache()->remember(
             $cacheKey, 
             3600, 
             fn() => 
            $books->paginate(10)->withQueryString()
        );

        
        //return the view with the filtered books
        return view("books.index", ["books" => $books]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $cacheKey = 'book:' . $id;
        $book = cache()->remember(
            $cacheKey, 
            3600, 
            fn() => 
            Book::with([
            "review" => fn ($query) => $query->latest(),
        ])->withAvgRating()->withReviewsCount()->findOrFail($id)
        );

        //$reviews = $book->review;

        return view("books.show", ["book" => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
