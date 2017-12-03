<?php

namespace sldb\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use sldb\Models\Loja;
use sldb\Services\LojaService;
use sldb\Services\RelatorioService;

class RelatorioController extends Controller
{

    private $relatorioService;
    private $lojaService;

    public function __construct(RelatorioService $relatorioService, LojaService $lojaService)
    {
        $this->relatorioService = $relatorioService;
        $this->lojaService = $lojaService;
    }

    public function usuariosCadastrados()
    {
        $usuarios = $this->relatorioService->geraRelatorioUsuariosCadastrados();
        return view('relatorio.usuarios')->withUsuarios($usuarios)->with('totalDeRegistros', $usuarios->count());
    }

    public function lojistasCadastrados()
    {
        $lojas = $this->relatorioService->geraRelatorioLojasCadastradas();
        return view('relatorio.lojistas')->withLojas($lojas)->with('totalDeRegistros', $lojas->count());
    }

    public function produtosCadastrados()
    {
        $produtos = $this->relatorioService->geraRelatorioProdutosCadastrados();
        return view('relatorio.produtos')->withProdutos($produtos)->with('totalDeRegistros', $produtos->count());
    }

    public function lojasMaisVenderamView()
    {
        return view('relatorio.lojas-mais-venderam')
            ->with('display', 'none');
    }

    public function lojasMaisVenderam()
    {
        $parametros = Request::except('_token');

        $parametros['dataIni'] = $this->convertDateToSqlFormat($parametros['dataIni']);
        $parametros['dataFim'] = $this->convertDateToSqlFormat($parametros['dataFim']);

        if($parametros['dataIni'] > $parametros['dataFim'])
            return response()->json(['error' => 'A Data Inicial nao pode ser maior que a Data Final.'], 400);


        $lojas = $this->relatorioService->geraRelatorioLojasMaisVenderam($parametros);

        foreach ($lojas as $loja) {
            $loja->comissao_site = $loja->valor_vendas * 0.10;
        }

        return $lojas;
    }

    public function produtosMaisPesquisadosView()
    {
        return view('relatorio.produtos-mais-pesquisados')
            ->with('display', 'none');
    }

    public function produtosMaisPesquisados()
    {
        $parametros = Request::except('_token');
        $parametros['dataIni'] = $this->convertDateToSqlFormat($parametros['dataIni']);
        $parametros['dataFim'] = $this->convertDateToSqlFormat($parametros['dataFim']);

        if($parametros['dataIni'] > $parametros['dataFim'])
            return response()->json(['error' => 'A Data Inicial nao pode ser maior que a Data Final.'], 400);


        $produtos = $this->relatorioService->geraRelatorioProdutosMaisPesquisados($parametros);
        $lista = $this->relatorioService->geraRelatorioProdutosMaisPesquisadosCloud($parametros);
        
        return $produtos;
        return $lista 
    }

    public function faturamentoLojaView($idLoja)
    {
        $lojas = [];
        if(Auth::user()->perfil_id==3) {
             $lojas = $this->lojaService->listaPorId(100, Auth::id());
        }

        return view('relatorio.faturamento-loja')
            ->with('lojas', sizeof($lojas)>0?$lojas:[])
            ->with('loja_id', $idLoja)
            ->with('display', 'none');
    }

    public function faturamentoLoja()
    {
        $parametros = Request::except('_token');
        $parametros['dataIni'] = $this->convertDateToSqlFormat($parametros['dataIni']);
        $parametros['dataFim'] = $this->convertDateToSqlFormat($parametros['dataFim']);

        if($parametros['dataIni'] > $parametros['dataFim'])
            return response()->json(['error' => 'A Data Inicial nao pode ser maior que a Data Final.'], 400);


        $faturamentoLoja = $this->relatorioService->geraRelatorioFaturamentoLoja($parametros);

        return $faturamentoLoja;


/*        if(Auth::user()->perfil_id==3) {
            $produtos = $this->relatorioService->geraRelatorioProdutosMaisVendidosPorLoja($parametros);
            $lojas = $this->lojaService->listaPorId(100, Auth::id());
        } else {
            $produtos = $this->relatorioService->geraRelatorioProdutosMaisVendidosGeral($parametros);
        }*/

/*        foreach ($produtos as $produto) {
            $produto->valor_total_vendas = ($produto->valor_produto * $produto->total);
        }*/

/*        return view('relatorio.faturamento-loja')
            ->withProdutos($produtos)
            ->withLojas($lojas)
            ->with('dataIni', Request::input('dataIni'))
            ->with('dataFim', Request::input('dataFim'))
            ->with('lojaSelecionada', $this->lojaService->buscaPorId(Request::input('lojaId')))
            ->with('display', 'block');*/
    }

    private function convertDateToSqlFormat($date) {

        $dataFormatada = explode("/", $date);
        $year = $dataFormatada[2];
        $month = $dataFormatada[1];
        $day = $dataFormatada[0];

        return Carbon::createFromDate($year, $month, $day);
    }
}
