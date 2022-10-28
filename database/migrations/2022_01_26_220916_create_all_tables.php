<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('cpf')->unique();
            $table->string('password');
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('owner_id');
            $table->foreign('owner_id')
                ->on('users')
            ->references('id');
        });

        Schema::create('unit_peoples', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('unit_id');
            $table->date('birthdate');
            $table->foreign('unit_id')
                ->on('units')
            ->references('id');
        });

        Schema::create('unit_vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->string('title');
            $table->string('color');
            $table->string('plate');
            $table->foreign('unit_id')
                ->on('units')
            ->references('id');
        });

        Schema::create('unit_pets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->string('name');
            $table->string('race');
            $table->foreign('unit_id')
                ->on('units')
            ->references('id');
        });

        Schema::create('walls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('body');
            $table->datetime('created_at');
        });

        Schema::create('wall_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wall_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('wall_id')
                ->on('walls')
            ->references('id');
            $table->foreign('user_id')
                ->on('users')
            ->references('id');
        });

        Schema::create('docs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('file_url');
        });

        Schema::create('billets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->string('title');
            $table->string('file_url');
            $table->foreign('unit_id')
                ->on('units')
            ->references('id');
        });

        Schema::create('warnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->string('title');
            $table->enum('status', ['in_review', 'resolved'])->default('in_review');
            $table->date('created_at');
            $table->text('photos');
            $table->foreign('unit_id')
                ->on('units')
            ->references('id');
        });

        Schema::create('found_and_lost', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['lost', 'recovered'])->default('lost');
            $table->string('photo');
            $table->string('description');
            $table->string('where');
            $table->date('created_at');
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->boolean('allowed')->default(1);
            $table->string('title');
            $table->string('cover');
            $table->string('days');
            $table->time('start_time');
            $table->time('end_time');
        });

        Schema::create('area_disabled_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('area_id');
            $table->date('day');
            $table->foreign('area_id')
                ->on('areas')
            ->references('id');
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('area_id');
            $table->datetime('reservation_at');
            $table->foreign('unit_id')
                ->on('units')
            ->references('id');
            $table->foreign('area_id')
                ->on('areas')
            ->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('units');
        Schema::dropIfExists('unit_peoples');
        Schema::dropIfExists('unit_vehicles');
        Schema::dropIfExists('unit_pets');
        Schema::dropIfExists('walls');
        Schema::dropIfExists('wall_likes');
        Schema::dropIfExists('docs');
        Schema::dropIfExists('billets');
        Schema::dropIfExists('warnings');
        Schema::dropIfExists('found_and_lost');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('area_disabled_days');
        Schema::dropIfExists('reservations');
    }
}
