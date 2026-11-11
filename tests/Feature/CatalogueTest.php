<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;
use Tests\LibraryTestCase;

class CatalogueTest extends LibraryTestCase
{
    public function test_public_home_catalogue_and_guest_forms_render_with_empty_data(): void
    {
        foreach (['/', '/katalog', '/login', '/daftar'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_guest_can_search_by_title_author_and_isbn_and_filter_category_together(): void
    {
        $technology = $this->category();
        $economics = $this->category('Ekonomi');
        $match = $this->book($technology, ['judul' => 'Basis Data Relasional', 'pengarang' => 'Siti Basis']);
        $other = $this->book($economics, ['judul' => 'Ekonomi Makro', 'isbn' => '9786020000002']);

        foreach (['Basis Data', 'Siti Basis', $match->isbn] as $query) {
            $this->get(route('katalog.index', ['q' => $query, 'kategori' => $technology->id_kategori]))
                ->assertOk()->assertSee($match->judul)->assertDontSee($other->judul);
        }

        $this->get(route('katalog.index', ['q' => 'Basis', 'kategori' => $economics->id_kategori]))
            ->assertOk()->assertViewHas('buku', fn ($books) => $books->total() === 0);
    }

    public function test_catalogue_paginates_and_preserves_filter_links(): void
    {
        $category = $this->category();
        for ($index = 0; $index < 25; $index++) {
            $this->book($category, [
                'judul' => 'Buku Teruji '.str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                'isbn' => '978602'.str_pad((string) $index, 7, '0', STR_PAD_LEFT),
            ]);
        }

        $response = $this->get(route('katalog.index', ['q' => 'Teruji', 'kategori' => $category->id_kategori]))->assertOk();
        $paginator = $response->viewData('buku');
        $this->assertSame(25, $paginator->total());
        $this->assertLessThan(25, $paginator->count());
        $this->assertStringContainsString('q=Teruji', $paginator->nextPageUrl());
        $this->assertStringContainsString('kategori='.$category->id_kategori, $paginator->nextPageUrl());
        $this->get($paginator->nextPageUrl())->assertOk();
    }

    public function test_zero_is_a_real_search_term_instead_of_an_empty_filter(): void
    {
        $category = $this->category();
        $match = $this->book($category, ['judul' => 'Pemrograman dari 0', 'isbn' => '9786123456789']);
        $other = $this->book($category, ['judul' => 'Pengantar Akuntansi', 'isbn' => '9786123456788']);

        $this->get(route('katalog.index', ['q' => '0']))->assertOk()
            ->assertSee($match->judul)->assertDontSee($other->judul);
    }

    public function test_public_book_detail_shows_stock_and_shelf_and_missing_book_returns_404(): void
    {
        $category = $this->category();
        $available = $this->book($category, ['jumlah_stok' => 7, 'lokasi_rak' => 'Lantai 2 Rak B-12']);
        $empty = $this->book($category, ['isbn' => '9786020000002', 'jumlah_stok' => 0]);
        $this->get(route('katalog.show', $available))->assertOk()->assertSee('Tersedia')->assertSee('7')->assertSee('Lantai 2 Rak B-12');
        $this->get(route('katalog.show', $empty))->assertOk()->assertSee('Habis');
        $this->get('/katalog/999999')->assertNotFound();
    }

    public function test_seed_data_contains_one_admin_five_categories_twenty_books_and_is_idempotent(): void
    {
        config(['library.initial_admin_password' => 'TestAdmin123!']);
        $this->seed();
        $this->seed();

        $this->assertSame(1, User::where('role', 'admin')->count());
        $this->assertDatabaseHas('t_user', ['email' => 'admin@gajayana.ac.id', 'role' => 'admin']);
        $this->assertSame(5, Kategori::count());
        $this->assertSame(20, Buku::count());
    }

    public function test_seeder_does_not_create_an_admin_without_a_private_initial_password(): void
    {
        config(['library.initial_admin_password' => null]);

        $this->seed();

        $this->assertSame(0, User::where('role', 'admin')->count());
    }
}
