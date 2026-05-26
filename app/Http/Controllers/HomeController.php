<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $baseBookQuery = Book::query()
            ->with([
                'authors:id,name',
                'category:id,name,color',
            ]);

        $latestBooks = (clone $baseBookQuery)
            ->latest()
            ->take(5)
            ->get();

        $mostBorrowedBooks = (clone $baseBookQuery)
            ->withSum('loanItems', 'quantity')
            ->orderByDesc('loan_items_sum_quantity')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        if ($mostBorrowedBooks->every(fn (Book $book): bool => (int) ($book->loan_items_sum_quantity ?? 0) === 0)) {
            $mostBorrowedBooks = (clone $baseBookQuery)
                ->orderByDesc('available_copies')
                ->latest()
                ->take(5)
                ->get();
        }

        return view('welcome', [
            'latestBooks' => $latestBooks,
            'mostBorrowedBooks' => $mostBorrowedBooks,
        ]);
    }
}
