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
 * Tables of the test modules, shaped like the ones the Bitrix migrator makes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_sections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')->nullable();
            $table->string('name');
            $table->string('code');
            $table->boolean('active')->default(true);
            $table->integer('sort')->default(500);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('brands', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('xml_id');
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('section_id')->nullable();
            $table->string('name');
            $table->string('code')->nullable();
            $table->boolean('active')->default(true);
            $table->dateTime('active_from')->nullable();
            $table->dateTime('active_to')->nullable();
            $table->integer('sort')->default(500);
            $table->string('preview_picture')->nullable();
            $table->text('preview_text')->nullable();
            $table->string('preview_text_type')->default('html');
            $table->string('detail_picture')->nullable();
            $table->text('detail_text')->nullable();
            $table->string('detail_text_type')->default('html');
            $table->string('article')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('color')->nullable();
            $table->unsignedBigInteger('brand')->nullable();
            $table->string('orderable')->nullable();
            $table->text('specs')->nullable();
            $table->json('photos')->nullable();
            $table->string('manual')->nullable();
            $table->timestamps();
        });

        Schema::create('products_related', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id');
            $table->foreignId('related_id');
        });

        Schema::create('posts', function (Blueprint $table): void {
            $table->id();
            $table->json('name');
            $table->json('code');
            $table->json('detail_text')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('sort')->default(500);
            $table->timestamps();
        });

    }
};
