<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_produto', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('loja_id')->unsigned()->nullable(false);
            $table->integer('categoria_id')->unsigned()->nullable(false);
            $table->integer('foto_id')->unsigned()->nullable(false);
            $table->string('nome');
            $table->longText('descricao');
            $table->decimal('preco', 10, 2);
            $table->integer('quantidade');
            $table->timestamps();
        });

        Schema::table('tb_produto', function (Blueprint $table)
        {
            $table->foreign('loja_id')
                ->references('id')
                ->on('tb_loja')
                ->onDelete('cascade');
        });

        Schema::table('tb_produto', function (Blueprint $table)
        {
            $table->foreign('categoria_id')
                ->references('id')
                ->on('tb_categoria')
                ->onDelete('cascade');

        });

        Schema::table('tb_produto', function (Blueprint $table) {

            $table->foreign('foto_id')
                ->references('id')
                ->on('tb_foto')
                ->onDelete('cascade');
        });

        Schema::table('tb_produto', function (Blueprint $table) {

            $table->boolean('fl_ativo')->default(false)->after('quantidade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_produto');
        Schema::table('tb_produto', function (Blueprint $table) {
            $table->dropColumn('fl_ativo');
        });
    }
}
