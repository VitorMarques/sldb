@include('painel.header')

<body>
    <div id="app">

        <input type="hidden" id="page-id" name="pageId" value="{{$pageId or null}}">

        @include('painel.navigation')

        <div class="container-fluid" style="padding-left: 0px">
            <div class="row">

                <div class="col-md-12">
                    <div class="col-md-2" style="padding-left: 0">
                        @include('painel.menu')
                    </div>
                    <div class="col-md-9" style="margin-top: 100px;">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                @yield('heading')
                            </div>
                            <div class="panel-body">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/functions.js') }}"></script>
    <script src="{{ asset('js/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ asset('js/canvasjs.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('locales/bootstrap-datepicker.pt-BR.min.js') }}"></script>
    <script src="{{ asset('js/highcharts.js') }}"></script>
    <script src="{{ asset('js/wordcloud.js') }}"></script>
</body>
</html>
