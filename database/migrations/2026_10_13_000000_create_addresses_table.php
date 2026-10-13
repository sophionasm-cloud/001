<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('addresses')) {
            Schema::create('addresses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('type')->default('Home');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('address_line1');
                $table->string('address_line2')->nullable();
                $table->string('city');
                $table->string('state')->nullable();
                $table->string('zip_code');
                $table->string('country')->default('United States');
                $table->string('phone')->nullable();
                $table->boolean('is_default')->default(false);
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
