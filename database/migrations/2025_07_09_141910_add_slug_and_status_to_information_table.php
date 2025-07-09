<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Information; 

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up(): void
    {
        Schema::table('information', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('id');
            $table->boolean('status')->default(true)->comment('1=enable, 0=disable');
        });

        // Generate slugs for existing records
        $this->generateSlugsForExistingRecords();
    }

    /**
     * Generate slugs for existing records
     */
    protected function generateSlugsForExistingRecords(): void
    {
        Information::withoutEvents(function () {
            Information::whereNull('slug')->each(function ($item) {
                $item->slug = $this->generateUniqueSlug($item->name); // Use appropriate field
                $item->save();
            });
        });
    }

    /**
     * Generate unique slug
     */
    protected function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $count = Information::where('slug', 'LIKE', "{$slug}%")->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('information', function (Blueprint $table) {
            $table->dropColumn(['slug', 'status']);
        });
    }
};
