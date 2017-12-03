<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//definir rotas do site
Route::get('/', 'SiteController@index')->name('site.index');
Route::get('/Categoria/{categoria}', 'SiteController@listaProdutosPorCategoria')->name('site.produtos.categoria');
Route::get('/Produto/{nome}', 'SiteController@exibeDetalhesProduto')->name('site.produto.detalhe');
Route::get('/Loja/{nome}', 'SiteController@exibeDetalhesLoja')->name('site.loja.detalhe');
Route::get('/painel', 'SiteController@painel')->name('site.painel');
Route::get('/quemsomos', 'SiteController@quemSomos')->name('site.quemsomos');
Route::get('/vendanasldb', 'SiteController@vendaNasSldb')->name('site.venda.sldb');
Route::get('/pesquisa', 'SiteController@pesquisa')->name('site.pesquisa');
Route::get('/cadastro-loja', 'SiteController@cadastroLoja')->name('site.cadastro.loja');
Route::get('/carrinho', 'SiteController@carrinho');

//definir rotas do carrinho de compras
Route::post('/carrinho/adiciona', 'CartController@insereProdutoCarrinho')->name('carrinho.adiciona.produto');
Route::get('/carrinho/remove/{idProduto}', 'CartController@removeProdutoCarrinho')->name('carrinho.remove.produto');
Route::get('/carrinho/esvazia', 'CartController@esvaziaCarrinho')->name('carrinho.esvazia');
Route::get('/carrinho/checkout', 'CartController@checkoutCarrinho')->name('carrinho.checkout');
Route::get('/carrinho/atualiza/{idProduto}/{qtd}', 'CartController@atualizaValoresCarrinho')->name('carrinho.atualiza.valores');
Route::get('/carrinho/calcula-frete/{cep}', 'CartController@calculaFrete')->name('carrinho.calcula.frete');
Route::post('/carrinho/finalizar-compra', 'CartController@finalizarCompra')->name('carrinho.finalizar.compra');
Route::post('/carrinho/salvar-endereco-entrega', 'CartController@salvaEnderecoEntrega')->name('carrinho.salvar.entrega');
Route::post('/carrinho/salvar-pagamento', 'CartController@salvaPagamento')->name('carrinho.salvar.pagamento');
Route::get('/compra-finalizada', 'CartController@compraFinalizada')->name('carrinho.compra.finalizada');

//rotas de autenticacao
Auth::routes();

//definir rotas do usuario
Route::group(['prefix' => 'usuarios'], function()
{
    Route::get('/', 'UsuarioController@lista')->name('usuario.lista');
    Route::post('/', 'UsuarioController@lista')->name('usuario.lista'); //utilizado parqa paginacao
    Route::get('/novo', 'UsuarioController@novo')->name('usuario.novo');
    Route::post('/adiciona', 'UsuarioController@adiciona')->name('usuario.adiciona');
    Route::get('/mostra/{id}', 'UsuarioController@mostra')->name('usuario.mostra');
    Route::get('/edita/{id}', 'UsuarioController@edita')->name('usuario.edita');
    Route::post('/atualiza', 'UsuarioController@atualiza')->name('usuario.atualiza');
    Route::get('/remove/{id}', 'UsuarioController@remove')->name('usuario.remove');
    Route::get('/{id}/pedidos', 'UsuarioController@listaCompras')->name('usuario.pedidos');
    Route::get('/compra/{compraId}', 'UsuarioController@mostraDetalhesCompra')->name('usuario.compra.detalhe');
});


//definir rotas da loja
Route::group(['prefix' => 'lojas'], function()
{
    Route::get('/', 'LojaController@lista')->name('loja.lista');
    Route::post('/', 'LojaController@lista')->name('loja.lista'); //utilizado parqa paginacao
    Route::get('/novo', 'LojaController@novo')->name('loja.novo');
    Route::post('/adiciona', 'LojaController@adiciona')->name('loja.adiciona');
    Route::get('/mostra/{id}', 'LojaController@mostra')->name('loja.mostra');
    Route::get('/edita/{id}', 'LojaController@edita')->name('loja.edita');
    Route::post('/atualiza', 'LojaController@atualiza')->name('loja.atualiza');
    Route::get('/remove/{id}', 'LojaController@remove')->name('loja.remove');
    Route::get('/em-aprovacao', 'LojaController@listaLojasAprovacao')->name('loja.aprovacao');
    Route::get('/aprovar/{id}', 'LojaController@aprovarLoja')->name('loja.aprovar');
});

//definir rotas dos produtos
Route::group(['prefix' => 'produtos'], function()
{
    Route::get('/', 'ProdutoController@lista')->name('produto.lista');
    Route::post('/', 'ProdutoController@lista')->name('produto.lista'); //utilizado parqa paginacao
    Route::get('/novo', 'ProdutoController@novo')->name('produto.novo');
    Route::post('/adiciona', 'ProdutoController@adiciona')->name('produto.adiciona');
    Route::get('/mostra/{id}', 'ProdutoController@mostra')->name('produto.mostra');
    Route::get('/edita/{id}', 'ProdutoController@edita')->name('produto.edita');
    Route::post('/atualiza', 'ProdutoController@atualiza')->name('produto.atualiza');
    Route::get('/remove/{id}', 'ProdutoController@remove')->name('produto.remove');
    Route::get('/em-aprovacao', 'ProdutoController@listaProdutosAprovacao')->name('produto.aprovacao');
    Route::get('/aprovar/{id}', 'ProdutoController@aprovarProduto')->name('produto.aprovar');
});

//definir rotas dos relatorios
Route::group(['prefix' => 'relatorios'], function ()
{

    Route::get('/usuarios', 'RelatorioController@usuariosCadastrados')->name('relatorio.usuario.cadastrado');
    Route::get('/lojistas', 'RelatorioController@lojistasCadastrados')->name('relatorio.lojista.cadastrado');
    Route::get('/produtos', 'RelatorioController@produtosCadastrados')->name('relatorio.produto.cadastrado');

    Route::get('/lojas-mais-vendas-filtro', 'RelatorioController@lojasMaisVenderamView')->name('relatorio.loja.mais.venda.view');
    Route::post('/lojas-mais-vendas', 'RelatorioController@lojasMaisVenderam')->name('relatorio.loja.mais.venda');

    Route::get('/produtos-mais-buscados-filtro', 'RelatorioController@produtosMaisPesquisadosView')->name('relatorio.produto.mais.pesquisado.view');
    Route::post('/produtos-mais-buscados', 'RelatorioController@produtosMaisPesquisados')->name('relatorio.produto.mais.pesquisado');
    

    Route::get('/faturamento-loja-filtro/{idLoja}', 'RelatorioController@faturamentoLojaView')->name('relatorio.faturamento.loja.view');
    Route::post('/faturamento-loja', 'RelatorioController@faturamentoLoja')->name('relatorio.faturamento.loja');

});
