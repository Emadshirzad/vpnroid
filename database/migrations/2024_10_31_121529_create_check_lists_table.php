<?php

use App\Models\Operator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('check_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('config_id')->constrained('list_configs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignIdFor(Operator::class)->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->integer('healthy')->nullable();
            $table->integer('down')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_lists');
    }
};
