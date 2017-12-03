$(document).ready(function () {
    // Remove Search if user Resets Form or hits Escape!
    $('body, .navbar-collapse form[role="search"] button[type="reset"]').on('click keyup', function(event) {
        if (event.which == 27 && $('.navbar-collapse form[role="search"]').hasClass('active') ||
            $(event.currentTarget).attr('type') == 'reset') {
            closeSearch();
        }
    });

    function closeSearch() {
        var $form = $('.navbar-collapse form[role="search"].active')
        $form.find('input').val('');
        $form.removeClass('active');
    }

    // Show Search if form is not active // event.preventDefault() is important, this prevents the form from submitting
    $(document).on('click', '.navbar-collapse form[role="search"]:not(.active) button[type="submit"]', function(event) {
        event.preventDefault();
        var $form = $(this).closest('form'),
            $input = $form.find('input');
        $form.addClass('active');
        $input.focus();

    });
    // ONLY FOR DEMO // Please use $('form').submit(function(event)) to track from submission
    // if your form is ajax remember to call `closeSearch()` to close the search container
    $(document).on('click', '.navbar-collapse form[role="search"].active button[type="submit"]', function(event) {
        event.preventDefault();
        var $form = $(this).closest('form'),
            $input = $form.find('input');
        $('#showSearchTerm').text($input.val());
        closeSearch()
    });
});



function initMap() {
    map = new GMaps({
        div: '#map',
        lat: -12.043333,
        lng: -77.028333
    });

    GMaps.geocode({
        address: $('#endereco_loja').val() + ' - ' + $('#bairro_loja').val(),
        callback: function(results, status) {
            if (status === 'OK') {
                var latlng = results[0].geometry.location;
                map.setCenter(latlng.lat(), latlng.lng());
                map.setZoom(16);
                map.addMarker({
                    lat: latlng.lat(),
                    lng: latlng.lng(),
                    title: $('#nome_loja').val()
                });
            }
        }
    });
}

$(document).ready(function () {

    var options = {
        autoclose: true,
        format: 'dd/mm/yyyy',
        language: 'pt-BR',
        todayHighlight: true
    };

    $('#dataIni').datepicker(options);
    $('#dataFim').datepicker(options);

});

$(document).ready(function(){
    $('#cep').mask('99999-999');
    $('#cnpj').mask('99.999.999/9999');
    $('#cpf_representante').mask('999.999.999-99');
    $('#cpf').mask('999.999.999-99');
    $('#telefone').mask('(99)9999-9999?9');
    $('#telefone2').mask('(99)9999-9999?9');
    $('#input-calcular-cep').mask('99999-999');
    $('#cartaoNumero').mask('9999-9999-9999-9999');
    $('#cartaoCodigoSeguranca').mask('999');
    $('#cartaoValidade').mask('99/99');
    $('#dataNascimento').mask('99/99/9999');
});

$(document).ready(function () {
    $('div.alert').delay(2000).slideUp(200);
});

$(document).ready(function defineLinkMenuAtivo() {
    var pageId = $('#page-id').val();
    $('#lnk-' + pageId).addClass('active');
});


$body = $("body");

$(document).on({
    ajaxStart: function() { $body.addClass("loading"); },
    ajaxStop: function() { $body.removeClass("loading"); }
});

$(document).ready(function () {

    if($('#retirarLoja').val()==='on') {
        $('#retirarLojaFinalizarCompra').attr('checked', 'checked');
        desabilitaFormEndereco(true);
    }

});

function confirmarExclusao(urlExclusao) {

    if(confirm("Deseja realmente realizar a exclusão?")) {
        window.location = urlExclusao;
    }
}

function desabilitaFormEndereco(checked) {
    $('#form-endereco-entrega :input').attr('disabled', checked);
    $('#retirarLojaFinalizarCompra').attr('disabled', true);
}

function desabilitaRadiosFrete(input) {

    if (input.checked) {
        $('#valores-frete :input').attr('checked', false);
        $('#valores-frete :input').attr('disabled', true);

        $('#valor-total').html('<strong>R$ ' + (parseFloat($('#valor-subtotal-input').val().replace(',','')) + '</strong>'));
        $('#valor-total-input').val(parseFloat($('#valor-subtotal-input').val().replace(',','')));

    } else {
        $('#valores-frete :input').attr('disabled', false);
    }
}

function validaDadosCarrinho() {

    if($('input[name=carrier]:checked').val()==undefined && $('input[name=retirarLoja]:checked').val()==undefined) {
        alert('Favor selecionar uma forma de envio do produto ou retirada na loja.')
    } else {
        $('#checkout-form').submit();
    }

    /*!$('input[name=retirarLoja]:checked') && */
}

function geraRelatorioLojasMaisVenderam(url, token) {

    var dataIni = $('#dataIni').val();
    var dataFim = $('#dataFim').val();

    if(!dataIni || !dataFim)
        alert('Favor informar o periodo para geracao do relatorio');

    var data = {'dataIni': dataIni, 'dataFim': dataFim, '_token': token};

    $.ajax({
        method: 'POST',
        url: url,
        data: data,
        success: function (data) {
            montaGraficoLojasMaisVenderam(data);
        },
        error: function (data) {
            alert(data.responseJSON.error);
        }
    });

}

function montaGraficoLojasMaisVenderam(dados) {

    if(dados.length<=0) {
        alert('Nao existem dados suficientes para gerar o relatorio do periodo informado!'); return;
    }

    $('#relatorioLojasMaisVendas').css('display', 'block');
    $('#relatorioLojasMaisVendasList').css('display', 'block');

    var dataPoints = [];
    var htmlTabela = '';
    var totalComissao = 0;
    var totalFaturamento = 0;

    $.each(dados, function (index, value) {
        dataPoints[index] = {'y': parseInt(value.valor_vendas), 'label': value.nome_fantasia}
        htmlTabela += '<tr>' +
            '<td>'+value.id_loja+'</td>' +
            '<td><strong><a class="text-info" href="/relatorios/faturamento-loja-filtro/'+value.id_loja+'">'+value.nome_fantasia+'</a></strong></td>' +
            '<td>R$ '+ parseFloat(value.valor_vendas).toLocaleString()+'</td>' +
            '<td>R$ '+parseFloat(value.comissao_site).toLocaleString()+'</td></tr>';
        totalComissao += parseFloat(value.comissao_site);
        totalFaturamento += parseFloat(value.valor_vendas);
    });

    htmlTabela += '<tr><td colspan="2"></td><td>Faturamento Total: <span class="text-success"><strong>R$ '+parseFloat(totalFaturamento).toLocaleString()   +'</strong></span></td><td>Comissão Total: <span class="text-success"><strong>R$ '+parseFloat(totalComissao).toLocaleString()   +'</strong></span></td></tr>';

    $('#listaLojaMaisVendas').html(htmlTabela);

    //dataPoints.sort(compare);

    var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        exportEnabled: true,
        theme: 'light1',
        title:{
            text:"Vendas por Loja"
        },
        data: [{
            type: "column",
            indexLabel: "R$ {y}",
            indexLabelFontColor: "#5A5757",
            indexLabelPlacement: "outside",
            yValueFormatString:  "R$#,##0.##",
            dataPoints: dataPoints
        }]
    });

    chart.render();

}

function geraRelatorioProdutosMaisPesquisados(url, token) {

    var dataIni = $('#dataIni').val();
    var dataFim = $('#dataFim').val();

    if(!dataIni || !dataFim)
        alert('Favor informar o periodo para geracao do relatorio');

    var data = {'dataIni': dataIni, 'dataFim': dataFim, '_token': token};

    $.ajax({
        method: 'POST',
        url: url,
        data: data,
        success: function (data) {
            montaGraficoProdutosMaisPesquisados(data);	    
        },
        error: function (data) {
            alert(data.responseJSON.error);
        }
    });

}


function montaGraficoProdutosMaisPesquisados(dados) {

    if(dados.length<=0) {
        alert('Nao existem dados suficientes para gerar o relatorio do periodo informado!'); return;
    }

    $('#relatorioProdutosMaisPesquisados').css('display', 'block');

    var dataPoints = [];

    $.each(dados, function (index, value) {
        dataPoints[index] = {'y': parseInt(value.total), 'label': value.produto}
    });

    dataPoints.sort(compare);

    var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
	
	title:{
		text:"Produtos mais pesquisados"
	},
	axisX:{
		interval: 1
	},
	axisY2:{
		interlacedColor: "rgba(1,77,101,.2)",
		gridColor: "rgba(1,77,101,.1)",
		title: "Número de pesquisas"
	},
	data: [{
		type: "bar",
		name: "termosPesquisados",
		axisYType: "secondary",
		color: "#014D65",
		dataPoints: dataPoints
	}]
});
chart.render();	
	
	montaGraficoProdutosMaisPesquisadosCloud(dados);

}

function montaGraficoProdutosMaisPesquisadosCloud(dados) {

    if(dados.length<=0) {
        alert('Nao existem dados suficientes para gerar o relatorio do periodo informado!'); return;
    }

    $('#relatorioProdutosMaisPesquisadosCloud').css('display', 'block');

    var text = '';
    var i = [];

    $.each(dados, function (index, value) {
	text[index] = value.produto	
    });
	
    $.each(dados, function (index, value) {
        i[index] = parseInt(value.total)
	    for (var k = 0; k <> i; k++) {
   		  text += value.produto+', '
		}
    });
	

    text.sort(compare);

        data = Highcharts.reduce(text, function (arr, word) {
            var obj = Highcharts.find(arr, function (obj) {
                return obj.name === word;
            });
            if (obj) {
                obj.weight += 1;
            } else {
                obj = {
                    name: word,
                    weight: 1
                };
                arr.push(obj);
            }
            return arr;
        }, []);

    Highcharts.chart('chartContainerCloud', {
        series: [{
            type: 'wordcloud',
            data: data,
            name: 'Ocorrências'
        }],
        title: {
            text: 'Nuvem de produtos pesquisados'
        }
    });

}

function geraRelatorioFaturamentoLoja(url, token) {

    var dataIni = $('#dataIni').val();
    var dataFim = $('#dataFim').val();
    var lojaId = $('#lojaId').val();

    if(!dataIni || !dataFim)
        alert('Favor informar o periodo para geracao do relatorio');

    var data = {'dataIni': dataIni, 'dataFim': dataFim, 'lojaId': lojaId, '_token': token};

    $.ajax({
        method: 'POST',
        url: url,
        data: data,
        success: function (data) {
            montaGraficoFaturamentoLoja(data);
        },
        error: function (data) {
            alert(data.responseJSON.error);
        }
    });

}

function montaGraficoFaturamentoLoja(dados) {

    if(dados.length<=0) {
        alert('Nao existem dados suficientes para gerar o relatorio do periodo informado!'); return;
    }

    $('#relatorioFaturamentoLoja').css('display', 'block');
    //$('#relatorioFaturamentoLojaList').css('display', 'block');

    var dataPoints = [];
    var htmlTabela = '';
    var faturamentoTotal = 0;

    $.each(dados, function (index, value) {
        dataPoints[index] = {'y': value.faturamento, 'x': new Date(value.ano, value.mes -1, 1), 'markerType': "circle", 'markerColor': "tomato"};
        htmlTabela += '<tr>' +
            '<td style="text-transform:capitalize;">'+value.nome_mes+'</td>' +
            '<td><strong><span class="text-success">R$ '+ parseFloat(value.faturamento).toLocaleString()+'</span></strong></td></tr>';
        faturamentoTotal += value.faturamento;
    });

    htmlTabela += '<tr><td>Total: </td><td><span class="text-danger"><strong>R$ '+parseFloat(faturamentoTotal).toLocaleString()+'</strong></span></td></tr>';

    $('#listaFaturamentoLoja').html(htmlTabela);

    var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        exportEnabled: true,
        theme: "light2",
        title:{
            text:"Faturamento Loja"
        },
        axisX: {
            interval: 1,
            intervalType: "month",
            valueFormatString: "YYYY-MM"
        },
        axisY:{
            includeZero: true,
            title: "Faturamento",
            valueFormatString: "R$###,###"
        },
        data: [{
            type: "line",
            showInLegend: true,
            xValueFormatString: "YYYY-MM",
            yValueFormatString: "R$###,###.##",
            markerSize: 12,
            name: "Valor Total de Vendas",
            dataPoints: dataPoints
        }]
    });

    chart.render();

}

/*Compara dois objetos do array de dados pela propriedade y que representa o valor total de vendas*/
function compare(a,b) {
    if (a.y < b.y)
        return -1;
    if (a.y > b.y)
        return 1;
    return 0;
}
