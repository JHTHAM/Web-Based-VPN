<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoutersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('routers', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();
        $table->string('ip_address')->nullable();
        $table->string('label')->nullable();
        $table->enum('status', ['online', 'offline'])->default('offline');
        $table->string('ovpn_path')->nullable(); // path to .ovpn file
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
        Schema::dropIfExists('routers');
    }
}
