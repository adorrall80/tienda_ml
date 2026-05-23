<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('security_blocked_terms', function (Blueprint $table) {
            $table->id();
            $table->string('term')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        $now = now();
        $terms = collect(config('security.blocked_text_terms', []))
            ->map(fn ($term) => [
                'term' => mb_strtolower(trim((string) $term)),
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->filter(fn ($row) => $row['term'] !== '')
            ->unique('term')
            ->values()
            ->all();

        if ($terms !== []) {
            DB::table('security_blocked_terms')->insert($terms);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_blocked_terms');
    }
};
