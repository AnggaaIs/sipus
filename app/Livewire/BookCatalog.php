<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\Ddc;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.guest', ['title' => 'Katalog Buku - SIPUS'])]
class BookCatalog extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: null)]
    public ?int $ddcId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDdcId(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $books = Book::query()
            ->with([
                'authors:id,name',
                'category:id,name,color',
                'ddc:id,code,name',
            ])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($bookQuery) {
                    $bookQuery
                        ->where('title', 'like', "%{$this->search}%")
                        ->orWhere('isbn', 'like', "%{$this->search}%")
                        ->orWhereHas('ddc', function ($ddcQuery) {
                            $ddcQuery
                                ->where('code', 'like', "%{$this->search}%")
                                ->orWhere('name', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('authors', function ($authorQuery) {
                            $authorQuery->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->ddcId !== null, function ($query) {
                $query->where('ddc_id', $this->ddcId);
            })
            ->latest()
            ->paginate(15);

        $ddcs = Ddc::query()
            ->orderBy('code', 'asc')
            ->get(['id', 'code', 'name']);

        return view('livewire.book-catalog', [
            'books' => $books,
            'ddcs' => $ddcs,
        ]);
    }

    public function paginationView()
    {
        return 'vendor.pagination.livewire';
    }
}
