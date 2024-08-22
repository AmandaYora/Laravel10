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
        Schema::create('menus', function (Blueprint $table) {
            $table->id('menu_id'); // Auto increment primary key
            $table->string('menu'); // Menu name
            $table->string('menu_slug'); // Menu slug
            $table->string('menu_icon')->nullable(); // Menu icon (nullable)
            $table->string('menu_redirect')->nullable(); // Redirect URL (nullable)
            $table->integer('menu_sort')->default(0); // Menu sort order, default is 0
            $table->foreignId('menu_type_id')->constrained('menu_types', 'menu_type_id'); // Foreign key to 'menu_type_id' in 'menu_types'
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign(['menu_type_id']);
        });

        Schema::dropIfExists('menus');
    }
};
