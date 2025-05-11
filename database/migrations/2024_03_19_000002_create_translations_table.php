<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locale_id')->constrained()->onDelete('cascade');
            $table->string('key')->index();
            $table->text('value');
            $table->string('device_type')->default('desktop'); // mobile, tablet, desktop
            $table->string('group')->default('general');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['locale_id', 'key', 'device_type', 'group']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
}; 