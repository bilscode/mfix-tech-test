<?php

namespace App\Http\Controllers;

use App\Author;
use App\Book;
use App\Http\Requests\PostBookRequest;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BooksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except([
            'index'
        ]);
        $this->middleware('auth.admin')->except([
            'index'
        ]);
    }

    public function index(Request $request)
    {
        // @TODO implement
        $sortDirection = 'ASC';
        $books = Book::query();
        if ($request->has('title')) {
            $books->where('title', 'like', '%'.$request->title.'%');
        }
        if ($request->has('authors')) {
            $authors = explode(',', $request->authors);
            $books->whereHas('authors', function ($query) use ($authors) {
                $query->whereIn('id', $authors);
            });
        }
        if ($request->has('sortColumn')) {
            if ($request->has('sortDirection')) {
                if (in_array($request->sortDirection, ['ASC', 'DESC'])) {
                    $sortDirection = $request->sortDirection;
                }
            }
            if ($request->sortColumn == 'avg_review') {
                $books->withCount(['reviews as avg_review' => function($query) {
                    $query->select(DB::raw('coalesce(avg(review),0)'));
                }])->orderBy($request->sortColumn, $sortDirection);
            } else {
                $books->orderBy($request->sortColumn, $sortDirection);
            }
        }
        return BookResource::collection($books->paginate(15));
    }

    public function store(PostBookRequest $request)
    {
        // @TODO implement
        $data = $request->validated();
        $authors = Author::whereIn('id', $data['authors'])->get();
        $book = new Book();
        $book->isbn = $data['isbn'];
        $book->title = $data['title'];
        $book->description = $data['description'];
        $book->published_year = $data['published_year'];
        $book->save();
        $book->authors()->saveMany($authors);
        return new BookResource($book);
    }
}
