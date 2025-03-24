<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function tableName(): string
    {
        return Config::get('rodels.cache.table');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = $this->tableName();
        if (! Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->increments('id');
                $table->string('url');
                $table->mediumText('headers');
                $table->mediumText('responseRaw');
                $table->timestamps();

                $table->index('created_at');
                $table->index('updated_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableName());
    }
};
