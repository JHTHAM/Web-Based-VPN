<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandaloneClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('standalone_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('ip_address')->nullable();
            $table->string('label')->nullable();
            $table->enum('status', ['online', 'offline'])->default('offline');
            $table->string('ovpn_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('standalone_clients');
    }
}
