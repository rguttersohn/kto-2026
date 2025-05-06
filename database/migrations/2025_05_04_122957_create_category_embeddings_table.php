<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('supabase')->create('indicators.category_embeddings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('category_id')->constrained('indicators.categories', 'id')->cascadeOnDelete();
            
        });

        DB::connection('supabase')->statement('ALTER TABLE indicators.category_embeddings ADD COLUMN embedding vector(384)');

        DB::connection('supabase')->statement("
            CREATE INDEX IF NOT EXISTS category_embeddings_embedding_idx
            ON indicators.category_embeddings
            USING ivfflat (embedding vector_cosine_ops)
            WITH (lists = 100);
        ");

        DB::connection('supabase')->statement("ANALYZE indicators.category_embeddings;");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('supabase')->dropIfExists('indicators.category_embeddings');
    }
};
