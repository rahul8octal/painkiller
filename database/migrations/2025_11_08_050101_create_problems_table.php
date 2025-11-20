<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('problems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->nullable()->constrained()->nullOnDelete();
            $table->string('external_id')->index();       // e.g., Reddit post ID
            $table->string('title')->nullable();
            $table->longText('body');
            $table->string('url')->nullable();
            $table->string('author')->nullable();
            $table->integer('votes')->default(0);
            $table->json('tags')->nullable();             // ["SaaS","Healthcare"]
            $table->json('signals')->nullable();          // {"search_volume": 1200}
            $table->json('scores')->nullable();           // {"urgency": 8, "frequency": 7, ...}
            $table->integer('total_score')->default(0);
            $table->enum('status', [
                'raw',
                'normalized',
                'scored',
                'enriched',
                'matched',
                'published'
            ])->default('raw');

            $table->unique(['source_id', 'external_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problems');
    }
};
