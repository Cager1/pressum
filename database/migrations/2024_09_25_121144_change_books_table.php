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
        Schema::table('books', function (Blueprint $table) {
            // drop column contact
            $table->dropColumn('contact');
            // insert columns author_email author_google_scolar and author_orcid
            $table->string('author_email')->nullable()->after('impressum');
            $table->string('author_google_scholar')->nullable()->after('impressum');
            $table->string('author_orcid')->nullable()->after('impressum');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            // drop columns author_email author_google_scolar and author_orcid
            $table->dropColumn('author_email');
            $table->dropColumn('author_google_scolar');
            $table->dropColumn('author_orcid');
            // insert column contact
            $table->longText('contact')->nullable()->after('impressum');
        });
    }
};
