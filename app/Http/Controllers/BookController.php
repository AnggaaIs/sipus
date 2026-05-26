<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Contracts\View\View;

class BookController extends Controller
{
    public function show(Book $book): View
    {
        $book->loadMissing([
            'authors:id,name',
            'category:id,name,color',
            'ddc:id,code,name',
            'publisher:id,name',
        ]);

        return view('books.show', [
            'book' => $book,
        ]);
    }
}
