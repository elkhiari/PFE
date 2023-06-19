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
        Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            $table->string("titre");
            $table->string("description");
            $table->decimal("prix",8,2);
            $table->unsignedBigInteger("ville");
            $table->unsignedBigInteger("user");
            $table->unsignedBigInteger("categorie");
            $table->foreign("ville")->references('id')->on('villes')->onDelete('cascade');
            $table->foreign("user")->references('id')->on('users')->onDelete('cascade');
            $table->foreign('categorie')->references('id')->on('categories')->onDelete('cascade');
            $table->json('images');
            $table->boolean('validated')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annonces');
    }
};
