<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_inventories', function (Blueprint $table) 
        {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('user_id');
            $table->string('type'); 
            $table->string('category');
            $table->string('sub_category');
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->text('location');
            $table->string('city');
            $table->string('state');
            $table->string('country')->default('India');
            $table->string('pin_code')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('price_unit')->default('sqft');
            $table->string('price_display')->nullable(); 
            $table->json('specifications')->nullable();
            $table->json('amenities')->nullable();
            $table->json('nearby_locations')->nullable(); 

            $table->json('social_links')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->json('gallery_images')->nullable();
            $table->string('video_link')->nullable();
            $table->string('brochure_path')->nullable();
            $table->string('floor_plan_path')->nullable();
            $table->string('site_map_path')->nullable();
            $table->string('price_list_path')->nullable();
            $table->string('contact_number_1');
            $table->string('contact_number_2')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('email')->nullable();
            
            $table->json('style_settings')->nullable();
            $table->string('status')->default('active');
            $table->boolean('featured')->default(false);

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
             
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
