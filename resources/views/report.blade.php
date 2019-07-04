<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{config('app.name')}}">
    <meta name="format-detection" content="telephone=no">
    <title>{{config('app.name')}}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-flat-top.min.css" integrity="sha256-ccqv0kmMDZn320TFE6NLfelCkGZdSZNs/MuhAo6QZ/w=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js" integrity="sha256-EPrkNjGEmCWyazb3A/Epj+W7Qm2pB9vnfXw+X6LImPM=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha256-YLGeXaapI0/5IgZopewRJcFXomhRMlYYjugPLSyNjTY=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" integrity="sha256-BtbhCIbtfeVWGsqxk1vOHEYXS6qcvQvLMZqjtpWUEx8=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.css" integrity="sha256-VVbO1uqtov1mU6f9qu/q+MfDmrqTfoba0rAE07szS20=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css" integrity="sha256-aa0xaJgmK/X74WM224KMQeNQC2xYKwlAt08oZqjeF0E=" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{asset('css/grid.css')}}">
    <link rel="stylesheet" href="{{asset('css/report.css')}}">
</head>
<body ng-app="Report" ng-cloak ng-controller="ReportController">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/moment.min.js" integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha256-CjSoeELFOcH0/uxWu6mC/Vlrc1AARqbm/jiiImDGV3s=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js" integrity="sha256-zI6VVO07NPmVW11q3RQE42YbRmJIkkunrcQ9LEYxJsQ=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.7.8/angular.min.js" integrity="sha256-23hi0Ag650tclABdGCdMNSjxvikytyQ44vYGo9HyOrU=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-colorschemes@0.4.0/dist/chartjs-plugin-colorschemes.min.js" integrity="sha256-Ctym065YsaugUvysT5nHayKynbiDGVpgNBqUePRAL+0=" crossorigin="anonymous"></script>
    
    <div class="navbar navbar-expand-md navbar-dark bg-dark" role="navigation">
        <a class="navbar-brand" href="">{{config('app.name')}}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                @foreach($menus as $menu)
                @include('partials.menu-item')
                @endforeach
            </ul>
            <nav class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{route('setup')}}">
                        <i class="fa fa-cogs"></i>
                    </a>
                </li>
            </nav>
        </div>
    </div>
    @include('partials.' . Str::slug($report->type))
    <script>
        $('.navbar .dropdown-item').on('click', function (e) {
            if(!e.target.href) {
                var $el = $(this).children('.dropdown-toggle');
                var $parent = $el.offsetParent(".dropdown-menu");
                $(this).parent("li").toggleClass('open');

                if (!$parent.parent().hasClass('navbar-nav')) {
                    if ($parent.hasClass('show')) {
                        $parent.removeClass('show');
                        $el.next().removeClass('show');
                        $el.next().css({"top": -999, "left": -999});
                    } else {
                        $parent.parent().find('.show').removeClass('show');
                        $parent.addClass('show');
                        $el.next().addClass('show');
                        if($el[0]) {
                            $el.next().css({"top": $el[0].offsetTop, "left": $parent.outerWidth() - 4});
                        }
                    }
                    e.preventDefault();
                    e.stopPropagation();
                }
            }
        });

        $('.navbar .dropdown').on('hidden.bs.dropdown', function () {
            $(this).find('li.dropdown').removeClass('show open');
            $(this).find('ul.dropdown-menu').removeClass('show open');
        });
    </script>

    <script>
        const _token = '{{csrf_token()}}';
        const _dataPath = '{{route('home')}}/api/data';
        const _report = @json($report);
    </script>
    <script src="{{asset('js/schemes.js')}}"></script>
    <script src="{{asset('js/functions.js')}}"></script>
    <script src="{{asset('js/report.js')}}"></script>

</body>
</html>