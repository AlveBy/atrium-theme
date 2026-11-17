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
 * The cached module that links to other records (ParcelLinksTest).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcels', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('sort')->default(500);
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('maker_id')->nullable();
            $table->string('keeper')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->timestamps();
        });

        Schema::create('parcel_brand', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parcel_id');
            $table->foreignId('brand_id');
        });
    }
};
