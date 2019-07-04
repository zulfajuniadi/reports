<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reports Setup</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-flat-top.min.css" integrity="sha256-ccqv0kmMDZn320TFE6NLfelCkGZdSZNs/MuhAo6QZ/w=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js" integrity="sha256-EPrkNjGEmCWyazb3A/Epj+W7Qm2pB9vnfXw+X6LImPM=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha256-YLGeXaapI0/5IgZopewRJcFXomhRMlYYjugPLSyNjTY=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" integrity="sha256-BtbhCIbtfeVWGsqxk1vOHEYXS6qcvQvLMZqjtpWUEx8=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-tree/2.22.6/angular-ui-tree.min.css" integrity="sha256-Qq08HHJBd7ob4jFBnkjIbMyZURCOmtvq9mruWb7kuR4=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.css" integrity="sha256-Qc3yyFhqacL9loe3ItFKo9WaSdTwZhpZRMYBvEpR2Cw=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css" integrity="sha256-aa0xaJgmK/X74WM224KMQeNQC2xYKwlAt08oZqjeF0E=" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{asset('css/report.css')}}">
</head>
<body ng-app="setup">
    @include('setup.partials.menu')
    <div class="mt-4">
        <ui-view></ui-view>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha256-CjSoeELFOcH0/uxWu6mC/Vlrc1AARqbm/jiiImDGV3s=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.7.8/angular.min.js" integrity="sha256-23hi0Ag650tclABdGCdMNSjxvikytyQ44vYGo9HyOrU=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-router/1.0.22/angular-ui-router.min.js" integrity="sha256-h/82i+45nj45rnRUHrB7A7OVqYZgkUVL2cuklteNW+o=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-tree/2.22.6/angular-ui-tree.min.js" integrity="sha256-17l+pk8jl6HhqCxXxQpKRWrDwXT4yj/dO7MgIzYZGWI=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.all.js" integrity="sha256-BfIfo/K+ePw1iAn4BFfrfVXmXQPAmKtqeDwVIgCFqTU=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-colorschemes@0.4.0/dist/chartjs-plugin-colorschemes.min.js" integrity="sha256-Ctym065YsaugUvysT5nHayKynbiDGVpgNBqUePRAL+0=" crossorigin="anonymous"></script>
    <script>
        const _templateBasePath = '{{asset('templates')}}';
        const _token = '{{csrf_token()}}';
        const _basePath = '{{asset('setup')}}';
        const _dataPath = '{{route('home')}}/api/data';
    </script>
    <script src="{{asset('js/schemes.js')}}"></script>
    <script src="{{asset('js/functions.js')}}"></script>
    <script src="{{asset('js/report.js')}}"></script>
    <script src="{{asset('js/setup.js')}}"></script>
</body>
</html>