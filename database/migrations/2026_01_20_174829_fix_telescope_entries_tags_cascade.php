<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixTelescopeEntriesTagsCascade extends Migration
{
    /**
     * Get the migration connection name.
     */
    public function getConnection()
    {
        return config('telescope.storage.database.connection');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $schema = Schema::connection($this->getConnection());

        $schema->table('telescope_entries_tags', function (Blueprint $table) {
            $table->dropForeign(['entry_uuid']);

            $table->foreign('entry_uuid')
                  ->references('uuid')
                  ->on('telescope_entries')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $schema = Schema::connection($this->getConnection());

        $schema->table('telescope_entries_tags', function (Blueprint $table) {
            $table->dropForeign(['entry_uuid']);

            $table->foreign('entry_uuid')
                  ->references('uuid')
                  ->on('telescope_entries');
        });
    }
}
