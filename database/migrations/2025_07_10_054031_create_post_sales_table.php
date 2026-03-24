<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('post_sales', function (Blueprint $table) 
        {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('sales_person_id');
            $table->string('applicant_name');
            $table->string('applicant_number');
            $table->string('project_name');
            $table->string('unit_number');
            $table->string('project_category');
            $table->string('project_sub_category');
            $table->date('dob');
            $table->date('doa');
            $table->string('email')->nullable();
            $table->text('permanent_address');
            $table->text('current_address');
            $table->json('checklist')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_sales');
    }
};
