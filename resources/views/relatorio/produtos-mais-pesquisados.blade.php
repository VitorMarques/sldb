@extends('template.painel')

@section('heading')

        <strong>Relat&oacute;rio de Termos Mais Pesquisados</strong>

@endsection

@section('content')

    @if(Session::has('flash_message'))
        <div class="alert alert-success">
            <strong>{{ Session::get('flash_message') }}</strong>
        </div>
    @endif

    @if(Session::has('error'))
        <div class="alert alert-danger alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>{{ Session::get('error') }}</strong>
        </div>
    @endif

    <h3>Informe os filtros que deseja utilizar:</h3>
    <div class="col-sm-12">
        <form>
            {{csrf_field()}}
            <div class="input-group-sm date col-sm-2">
                <label for="dataIni">Data Inicial</label>
                <input type="text" class="form-control" name="dataIni" id="dataIni" required>
            </div>
            <div class="form-group-sm col-sm-2">
                <label for="dataFim">Data Final</label>
                <input type="text" class="form-control" name="dataFim" id="dataFim" required>
            </div>
            <div>
                <button type="button"
                        class="btn btn-primary btn-sm"
                        style="margin-top: 25px"
                        onclick="geraRelatorioProdutosMaisPesquisados('{{action('RelatorioController@produtosMaisPesquisados')}}', '{{csrf_token()}}')">
                    Gerar Relat&oacute;rio
                </button>
            </div>
        </form>
    </div>

    <div id="relatorioProdutosMaisPesquisados" class="col-sm-12" style="margin-top:80px;display: {{$display}}">
        <div id="chartContainer" style="height: 370px; width: 100%;"></div>
    </div>
    
     <div id="relatorioProdutosMaisPesquisadosCloud" class="col-sm-12" style="margin-top:80px;display: {{$display}}">
        <div id="chartContainerCloud" style="height: 370px; width: 100%;"></div>
    </div>

@endsection
