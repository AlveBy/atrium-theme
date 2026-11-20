<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Stand-ins for the modules a skin's home page reads (HomeContentTest).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->boolean('active')->default(true);
            $table->integer('sort')->default(500);
            $table->json('phones')->nullable();
            $table->string('address')->nullable();
            $table->text('detail_text')->nullable();
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->boolean('published')->default(true);
            $table->dateTime('posted_at')->nullable();
            $table->string('reviewable_type')->nullable();
            $table->unsignedBigInteger('reviewable_id')->nullable();
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')->nullable();
            $table->string('name');
            $table->string('code');
            $table->boolean('active')->default(true);
            $table->integer('sort')->default(500);
            $table->timestamps();
        });

        Schema::create('forms', function (Blueprint $table): void {
            $table->id();
            $table->string('code');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }
};
