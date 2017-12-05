<?php
/**
 * Created by PhpStorm.
 * User: Vitor
 * Date: 30/08/2017
 * Time: 10:17
 */

namespace sldb\Services;

use sldb\Models\Categoria;
use sldb\Models\Compra;
use sldb\Models\Foto;
use sldb\Models\PesquisaProduto;
use sldb\Models\Produto;


use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class ProdutoService extends Service
{

    public function listaTodos()
    {
        return Produto::all();
    }

    public function listaRecentes()
    {
        return Produto::where('fl_ativo', '=', true)->paginate(20);
    }

    public function lista($loja_id, $qtdItens, $textoPesquisa=null)
    {
        return Produto::where('loja_id', '=', $loja_id)
            ->where('nome', 'like', '%'.$textoPesquisa.'%')
            ->paginate($qtdItens);
    }

    public function listaCategorias() {
        return Categoria::all();
    }

    public function atualiza($id, $request)
    {
        Produto::where('id', $id)->update($request);
    }

    public function adiciona($request)
    {
        return Produto::create($request);
    }

    public function salvaCompra($dadosCompra)
    {
        return Compra::create($dadosCompra);
    }

    public function buscaPorId($id)
    {
        return Produto::find($id);
    }

    public function buscaPorNome($nome)
    {
        return Produto::where('nome', '=', $nome)
            ->where('fl_ativo', '=', true)
            ->first();
    }

    public function remove($id)
    {
        Produto::where('id', $id)->delete($id);
    }

    public function listaProdutosPorCategoria($descricaoCategoria)
    {
        $categoria = Categoria::where('descricao', '=', $descricaoCategoria)->first();
        $produtos = Produto::where('categoria_id', '=', $categoria->id)
            ->where('fl_ativo', '=', true)
            ->paginate(15);
        return $produtos;
    }

    public function listaProdutosSemelhantes($produto)
    {
        $produtos = Produto::where('nome', '<>', $produto->nome)
            ->where('categoria_id', '=', $produto->categoria_id)
            ->where('fl_ativo', '=', true)
            ->paginate(10);
        return $produtos;
    }

    public function listaProdutosEmAprovacao()
    {
        return Produto::where('fl_ativo', '=', false)->paginate(100);
    }

    public function aprovaProduto($id)
    {
        $produto = $this->buscaPorId($id);
        $produto->fl_ativo = true;
        $produto->save();
    }

    public function listaProdutosLoja($id)
    {
        $produtos = Produto::where('loja_id', '=', $id)
            ->where('fl_ativo', '=', true)
            ->paginate(10);

        return $produtos;
    }

    /*
     * SELECT t2.*
FROM tb_loja t1, tb_produto t2
WHERE (bairro = ''
      OR cidade = ''
       OR t2.nome like '%Johnnie%')
AND t1.id = t2.loja_id;
     * */

    public function pesquisaProdutos($texto)
    {

        $textoArray = [];
        $textoArray['texto'] = $texto;

        PesquisaProduto::create($textoArray);

        $produtos = Produto::where('nome', 'like', '%'.$texto.'%')
            ->where('fl_ativo', '=', true)
            ->paginate(20);

/*        $produtos = DB::table('tb_produto')
            ->join('tb_loja', 'loja_id', '=', 'tb_loja.id')
            ->select('tb_produto.*')
            ->orWhere('bairro', '=', $texto)
            ->orWhere('cidade', '=', $texto)
            ->orWhere('tb_produto.nome', 'like', '%'.$texto.'%')->paginate(20);*/
        
         DB::table('tb_pesquisa_produto')->insert(
                ['text' => $texto, 'created_at' => now(), 'updated_at' => now()]
            );


        return $produtos;
    }

    public function getFotoProduto($foto_id)
    {
        return Foto::where('id', '=', $foto_id)->first();
    }

}
