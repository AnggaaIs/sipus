<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    /**
     * Tampilkan daftar semua kategori untuk pengunjung.
     */
    public function index(): View
    {
        $categories = Category::query()
            ->withCount('books')
            ->orderBy('name')
            ->get();

        return view('categories.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Tampilkan buku-buku dalam satu kategori tertentu.
     */
    public function show(Category $category): View
    {
        $books = $category->books()
            ->with(['authors:id,name', 'category:id,name,color'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('categories.show', [
            'category' => $category,
            'books' => $books,
        ]);
    }
}
