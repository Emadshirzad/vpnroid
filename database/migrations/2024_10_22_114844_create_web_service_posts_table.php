<?php

use App\Models\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('web_service_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Service::class)->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->text('link');
            $table->text('body');
            $table->text('key');
            $table->string('header');
            $table->boolean('is_encode')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_srvice_posts');
    }
};