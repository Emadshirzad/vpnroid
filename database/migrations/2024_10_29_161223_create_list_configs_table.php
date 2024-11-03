<?php

use App\Models\Type;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('list_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')->constrained('type_configs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('prepare')->constrained('types')->onDelete('cascade')->onUpdate('cascade');
            $table->string('prepare_id');
            $table->text('config')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_configs');
    }
};
