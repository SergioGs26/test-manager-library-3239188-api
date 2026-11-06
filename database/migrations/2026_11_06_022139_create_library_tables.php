
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS library');
        // 1. Categories
        Schema::create('category', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100)->unique();
        });

        // 2. Books
        Schema::create('book', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 250);
            $table->string('author', 200);
            $table->char('isbn', 13);

            $table->foreignUuid('category_id')
                ->constrained('category')
                ->restrictOnDelete();

            $table->integer('total_copies');
            $table->integer('available_copies');
            $table->string('cover_key', 512)->nullable();
            $table->timestampTz('deleted_at')->nullable();

            $table->index('category_id');
        });

        // 3. Members
        Schema::create('member', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('full_name', 200);
            $table->string('document_number', 30)->unique();
        });

        // 4. Users
        Schema::create('user', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username', 120)->unique();
            $table->string('password_hash', 512);
            $table->string('role', 20);
        });

        // 5. Loans
        Schema::create('loan', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('member_id')
                ->constrained('member')
                ->restrictOnDelete();

            $table->foreignUuid('loaned_by_user_id')
                ->constrained('user')
                ->restrictOnDelete();

            $table->timestampTz('loaned_at');
        });

        // 6. Loan items
        Schema::create('loan_item', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('loan_id')
                ->constrained('loan')
                ->cascadeOnDelete();

            $table->foreignUuid('book_id')
                ->constrained('book')
                ->restrictOnDelete();

            $table->string('book_title', 250);
            $table->char('isbn', 13);
            $table->string('category_name', 100);
            $table->timestampTz('due_at');
            $table->timestampTz('returned_at')->nullable();

            $table->unique(['loan_id', 'book_id']);
        });

        // ISBN must be unique among active books.
        DB::statement(
            'CREATE UNIQUE INDEX books_isbn_active_unique
             ON book (isbn)
             WHERE deleted_at IS NULL'
        );

        // Available copies cannot be negative or exceed total copies.
        DB::statement(
            'ALTER TABLE book
             ADD CONSTRAINT ck_book_available_copies
             CHECK (
                 available_copies >= 0
                 AND available_copies <= total_copies
             )'
        );

        // Initial categories.
        DB::table('category')->insert([
            [
                'id' => '11111111-1111-4111-8111-111111111111',
                'name' => 'Novela',
            ],
            [
                'id' => '22222222-2222-4222-8222-222222222222',
                'name' => 'Ciencia',
            ],
            [
                'id' => '33333333-3333-4333-8333-333333333333',
                'name' => 'Tecnología',
            ],
            [
                'id' => '44444444-4444-4444-8444-444444444444',
                'name' => 'Historia',
            ],
            [
                'id' => '55555555-5555-4555-8555-555555555555',
                'name' => 'Infantil',
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_item');
        Schema::dropIfExists('loan');
        Schema::dropIfExists('user');
        Schema::dropIfExists('member');
        Schema::dropIfExists('book');
        Schema::dropIfExists('category');
    }
};
