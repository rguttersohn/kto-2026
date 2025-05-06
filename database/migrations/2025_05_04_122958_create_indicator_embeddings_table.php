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
        Schema::connection('supabase')->create('indicators.indicator_embeddings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('indicator_id')->constrained('indicators.indicators', 'id')->cascadeOnDelete();
            
        });

        DB::connection('supabase')->statement('ALTER TABLE indicators.indicator_embeddings ADD COLUMN embedding vector(384)');

        DB::connection('supabase')->statement("
            CREATE INDEX IF NOT EXISTS indicator_embeddings_embedding_idx
            ON indicators.indicator_embeddings
            USING ivfflat (embedding vector_cosine_ops)
            WITH (lists = 100);
        ");

        DB::connection('supabase')->statement("ANALYZE indicators.indicator_embeddings;");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('supabase')->dropIfExists('indicators.indicator_embeddings');
    }
};
