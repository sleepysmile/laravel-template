<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Шаблон миграции для создания таблицы
 * 
 * Замените [table_name] на имя таблицы
 * Добавьте необходимые поля в метод up()
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('[table_name]', function (Blueprint $table) {
            $table->id();
            
            // Основные типы полей:
            // $table->string('name');                    // VARCHAR(255)
            // $table->string('name', 100);               // VARCHAR(100)
            // $table->text('description');               // TEXT
            // $table->integer('count');                  // INTEGER
            // $table->bigInteger('big_count');           // BIGINT
            // $table->boolean('is_active');              // BOOLEAN
            // $table->decimal('price', 8, 2);            // DECIMAL(8,2)
            // $table->float('rating');                   // FLOAT
            // $table->date('birth_date');                // DATE
            // $table->dateTime('published_at');          // DATETIME
            // $table->timestamp('verified_at');          // TIMESTAMP
            // $table->json('metadata');                  // JSON
            // $table->uuid('uuid');                      // UUID
            
            // Nullable поля:
            // $table->string('optional_field')->nullable();
            
            // Поля с значением по умолчанию:
            // $table->boolean('is_active')->default(true);
            // $table->integer('count')->default(0);
            
            // Уникальные поля:
            // $table->string('email')->unique();
            
            // Внешние ключи:
            // $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // или
            // $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Индексы:
            // $table->index('email');
            // $table->index(['first_name', 'last_name']);
            
            $table->timestamps();  // created_at, updated_at
            // $table->softDeletes();  // deleted_at (для soft delete)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('[table_name]');
    }
};
