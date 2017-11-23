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

//desabilita os campos do endereco se ja houver um endereco cadastrado
$(document).ready(function () {

/*   var identificadorEndereco = $('#identificador').val();

   if(identificadorEndereco!=null && identificadorEndereco!='' && identificadorEndereco!=undefined) {
       $('#form-endereco-entrega :input').attr('disabled', true);
   }*/

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

    var dataPoints = [];

    $.each(dados, function (index, value) {
        dataPoints[index] = {'y': parseInt(value.total), 'label': value.nome_fantasia}
    });

    dataPoints.sort(compare);

    var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,

        title:{
            text:"Lojas com Maior Numero de Vendas"
        },
        axisX:{
            interval: 1
        },
        axisY2:{
            interlacedColor: "rgba(1,77,101,.2)",
            gridColor: "rgba(1,77,101,.1)",
            title: "Numero de Vendas"
        },
        data: [{
            type: "bar",
            name: "Lojas",
            axisYType: "secondary",
            color: "#014D65",
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
            text:"Produtos mais Pesquisados no Site"
        },
        data: [{
            type: "pie",
            startAngle: 240,
            yValueFormatString: "##0\"x\"",
            indexLabel: "{label} {y}",
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