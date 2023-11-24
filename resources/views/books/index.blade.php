@extends('layouts.app')

@section('content')
    <h1 class="mb-10 text-2xl">Books</h1>

    <form method="GET" action="{{ route('books.index')}}" class="mb-4 flex gap-2 items-center">
        <input class="input h-10" type="text" name="title" placeholder="Search by title" value="{{ request('title') }}">
        <input type="hidden" name="filter" value="{{ request('filter') }}">
        <button type="submit" class="btn h-10">Search</button>
        <a href="{{ route('books.index') }}" class="btn h-10">Clear</a>
    </form>

    <div class="filter-container mb-4 flex">
        @php
            $filters = [
                '' => 'Latest',
                'popular_last_month' => 'Popular Last Month',
                'popular_last_6months' => 'Popular Last 6 Months',
                'highest_rated_last_month' => 'Highest Rated Last Month',
                'highest_rated_last_6months' => 'Highest Rated Last 6 Months',
            ];
        @endphp

        @foreach ($filters as $key => $label)
            <a href="{{ route('books.index', [...request()->query(), 'filter' => $key]) }}" class="{{ request('filter') === $key || (request('filter') === null && $key === '') ? 'filter-item-active' : 'filter-item'}}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <ul>
        @forelse ($books as $book)
        <li class="mb-4">
            <div class="book-item">
              <div
                class="flex flex-wrap items-center justify-between">
                <div class="w-full flex-grow sm:w-auto">
                  <a href="{{ route('books.show', $book) }}" class="book-title">{{$book->title}}</a>
                  <span class="book-author">by {{$book->author}}</span>
                </div>
                <div>
                  <div class="book-rating">
                    {{ number_format($book->review_avg_rating, 1) }}
                    <x-star-rating :rating="$book->review_avg_rating" />
                  </div>
                  <div class="book-review-count">
                    out of {{$book->review_count}} {{ Str::plural('review', $book->review_count)}}
                  </div>
                </div>
              </div>
            </div>
          </li>
        @empty
        <li class="mb-4">
            <div class="empty-book-item">
              <p class="empty-text">No books found</p>
              <a href="{{route('books.index')}}" class="reset-link">Reset criteria</a>
            </div>
          </li>
        @endforelse
    </ul>
    {{ $books->links() }}
@endsection


<!-- 
The form in the index.blade.php file allows users to search for books by title. Here's how it works and how it interacts with the controller and model to update the book list:

1. When the form is submitted, it sends a GET request to the books.index route, which is handled by a controller method.
2. The action attribute of the form specifies the route URL where the form data will be sent. In this case, it is {route('books.index') }}, which corresponds to the index method of the BooksController.
3. The method attribute of the form is set to "GET", indicating that the form data will be appended to the URL as query parameters.
4. The input field with the name "title" is used to enter the search query. The value attribute is set to { request('title') }}, which populates the input field with the current value of the "title" query parameter, if it exists.
5. The "Search" button triggers the form submission when clicked.
6. The "Reset" button redirects to the books.index route without any query parameters, effectively resetting the search criteria.
7. In the BooksController, the index method handles the GET request to the books.index route. It retrieves the search query from the request's query parameters using request('title').
8. The index method then uses the search query to filter the book records in the database. It may use the where method or a query scope to apply the filtering logic.
9. The filtered book records are passed to the index view as a variable named $books.
10. In the index.blade.php file, the forelse directive is used to iterate over the $books variable. For each book, it displays the book's title, author, average rating, and review count.
11. If no books are found (i.e., the $books variable is empty), the empty directive is used to display a message indicating that no books were found.
12. The updated book list is rendered in the browser when the page is loaded or when the form is submitted.

In summary, the form allows users to search for books by title. The form data is sent to the controller, which filters the book records based on the search query. 
The filtered book records are then passed to the view, where they are displayed in the book list.
-->