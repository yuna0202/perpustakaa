<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\LibraryTestCase;

class LibraryManagementTest extends LibraryTestCase
{
    public function test_admin_can_upload_replace_and_remove_a_book_cover(): void
    {
        Storage::fake('public');
        $category = $this->category();
        $data = $this->bookData($category);
        $this->actingAs($this->user('admin'));

        $this->post(route('admin.buku.store'), $data + ['cover' => UploadedFile::fake()->image('cover.jpg')])
            ->assertRedirect()->assertSessionHasNoErrors();
        $book = Buku::firstOrFail();
        $first = $book->cover_path;
        $this->assertNotEmpty($first);
        Storage::disk('public')->assertExists($first);
        $this->get(route('katalog.show', $book))->assertOk()->assertSee(Storage::disk('public')->url($first));

        $this->put(route('admin.buku.update', $book), $data + ['cover' => UploadedFile::fake()->image('new.png')])
            ->assertRedirect()->assertSessionHasNoErrors();
        $second = $book->fresh()->cover_path;
        $this->assertNotSame($first, $second);
        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($second);

        $this->put(route('admin.buku.update', $book), $data + ['remove_cover' => '1'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertNull($book->fresh()->cover_path);
        Storage::disk('public')->assertMissing($second);
        $this->get(route('katalog.show', $book))->assertOk()->assertSee('Cover belum tersedia');

        $this->put(route('admin.buku.update', $book), $data + ['cover' => UploadedFile::fake()->image('last.jpg')])
            ->assertRedirect()->assertSessionHasNoErrors();
        $last = $book->fresh()->cover_path;
        $this->delete(route('admin.buku.destroy', $book))->assertRedirect();
        Storage::disk('public')->assertMissing($last);
    }

    public function test_cover_rejects_non_image_files(): void
    {
        Storage::fake('public');
        $category = $this->category();
        $this->actingAs($this->user('admin'))->post(route('admin.buku.store'), $this->bookData($category) + [
            'cover' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('cover');
        $this->assertDatabaseCount('t_buku', 0);
        $this->assertSame([], Storage::disk('public')->allFiles('buku/covers'));
    }

    public function test_category_crud_and_unique_name_validation(): void
    {
        $this->actingAs($this->user('admin'));
        $this->post(route('admin.kategori.store'), ['nama_kategori' => 'Ilmu Komputer'])
            ->assertRedirect()->assertSessionHas('success');
        $category = Kategori::firstOrFail();
        $this->post(route('admin.kategori.store'), ['nama_kategori' => 'Ilmu Komputer'])
            ->assertSessionHasErrors('nama_kategori');
        $this->put(route('admin.kategori.update', $category), ['nama_kategori' => 'Ilmu Komputer'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->put(route('admin.kategori.update', $category), ['nama_kategori' => 'Informatika'])
            ->assertRedirect()->assertSessionHas('success');
        $this->assertSame('Informatika', $category->fresh()->nama_kategori);
        $this->delete(route('admin.kategori.destroy', $category))->assertRedirect();
        $this->assertDatabaseMissing('t_kategori', ['id_kategori' => $category->id_kategori]);
    }

    public function test_category_used_by_a_book_cannot_be_deleted(): void
    {
        $category = $this->category();
        $book = $this->book($category);
        $this->actingAs($this->user('admin'))
            ->delete(route('admin.kategori.destroy', $category))
            ->assertRedirect()->assertSessionHas('error');

        $this->assertDatabaseHas('t_kategori', ['id_kategori' => $category->id_kategori]);
        $this->assertDatabaseHas('t_buku', ['id_buku' => $book->id_buku]);
    }

    public function test_book_crud_updates_public_catalogue_and_allows_unchanged_isbn_on_edit(): void
    {
        $category = $this->category();
        $data = $this->bookData($category);
        $this->actingAs($this->user('admin'));
        $this->post(route('admin.buku.store'), $data)->assertRedirect()->assertSessionHas('success');
        $book = Buku::firstOrFail();
        $this->get(route('katalog.show', $book))->assertOk()->assertSee($data['judul']);

        $this->put(route('admin.buku.update', $book), array_merge($data, [
            'judul' => 'Sistem Informasi Edisi Revisi', 'jumlah_stok' => 0,
        ]))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame(0, $book->fresh()->jumlah_stok);
        $this->get(route('katalog.show', $book))->assertOk()->assertSee('Sistem Informasi Edisi Revisi')->assertSee('Habis');
        $this->delete(route('admin.buku.destroy', $book))->assertRedirect();
        $this->get(route('katalog.show', $book))->assertNotFound();
    }

    public function test_all_required_book_fields_are_validated_and_old_input_is_preserved(): void
    {
        $this->actingAs($this->user('admin'));
        $this->from(route('admin.buku.create'))->post(route('admin.buku.store'), ['judul' => 'Judul tetap tersimpan'])
            ->assertRedirect(route('admin.buku.create'))
            ->assertSessionHasErrors([
                'id_kategori', 'pengarang', 'penerbit', 'isbn', 'tahun_terbit', 'jumlah_stok', 'lokasi_rak', 'tanggal_masuk',
            ])->assertSessionHasInput('judul', 'Judul tetap tersimpan');
        $this->assertDatabaseCount('t_buku', 0);
    }

    public function test_book_rejects_future_year_negative_stock_missing_category_and_duplicate_isbn(): void
    {
        $category = $this->category();
        $this->book($category);
        $this->actingAs($this->user('admin'))->post(route('admin.buku.store'), $this->bookData($category, [
            'tahun_terbit' => (string) (now()->year + 1),
            'jumlah_stok' => -1,
            'id_kategori' => 99999,
            'tanggal_masuk' => 'not-a-date',
        ]))->assertSessionHasErrors(['tahun_terbit', 'jumlah_stok', 'id_kategori', 'isbn', 'tanggal_masuk']);
        $this->assertDatabaseCount('t_buku', 1);
    }

    public function test_book_rejects_non_four_digit_year_and_fractional_stock(): void
    {
        $category = $this->category();
        $this->actingAs($this->user('admin'))->post(route('admin.buku.store'), $this->bookData($category, [
            'tahun_terbit' => '999', 'jumlah_stok' => 1.5,
        ]))->assertSessionHasErrors(['tahun_terbit', 'jumlah_stok']);
        $this->assertDatabaseCount('t_buku', 0);
    }

    public function test_book_update_cannot_take_another_books_isbn(): void
    {
        $category = $this->category();
        $original = $this->book($category);
        $other = $this->book($category, ['isbn' => '9786020000002']);
        $this->actingAs($this->user('admin'))->put(route('admin.buku.update', $other), $this->bookData($category))
            ->assertSessionHasErrors('isbn');
        $this->assertSame('9786020000002', $other->fresh()->isbn);
        $this->assertSame('9786020000001', $original->fresh()->isbn);
    }

    public function test_admin_dashboard_counts_pending_approved_books_and_empty_stock(): void
    {
        $this->member();
        $this->member(['status_verifikasi' => 'disetujui', 'QRcode' => str_repeat('e', 64)]);
        $this->member(['status_verifikasi' => 'ditolak']);
        $category = $this->category();
        $this->book($category);
        $this->book($category, ['isbn' => '9786020000002', 'jumlah_stok' => 0]);

        $this->actingAs($this->user('admin'))->get(route('admin.dashboard'))->assertOk()
            ->assertViewHas('pending', 1)
            ->assertViewHas('totalAnggota', 1)
            ->assertViewHas('totalBuku', 2)
            ->assertViewHas('stokHabis', 1);
    }

    public function test_all_admin_management_forms_render(): void
    {
        $category = $this->category();
        $book = $this->book($category);
        $member = $this->member();
        $this->actingAs($this->user('admin'));

        foreach ([
            route('admin.buku.index'), route('admin.buku.create'), route('admin.buku.edit', $book),
            route('admin.kategori.index'), route('admin.kategori.create'), route('admin.kategori.edit', $category),
            route('admin.pendaftar.index'), route('admin.pendaftar.show', $member),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
