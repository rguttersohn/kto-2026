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
        Schema::create('indicators.indicator_embeddings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->vector('embedding', 384);
            $table->string('model')->default('gte-small');
            $table->foreignId('indicator_id')
                ->constrained('indicators.indicators')
                ->cascadeOnDelete();
            $table->index('indicator_id');
        });

        DB::statement('CREATE INDEX indicator_embeddings_embedding_idx ON indicators.indicator_embeddings USING hnsw (embedding vector_cosine_ops)');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicator_embeddings');
    }
};
