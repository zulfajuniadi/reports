<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Setup</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item" ui-sref-active="datagrids">
                    <a class="nav-link" ui-sref="datagrids">Data Grids</a>
                </li>
                <li class="nav-item" ui-sref-active="charts">
                    <a class="nav-link" ui-sref="charts">Charts</a>
                </li>
                <li class="nav-item" ui-sref-active="reports">
                    <a class="nav-link" ui-sref="reports">Reports</a>
                </li>
                <li class="nav-item" ui-sref-active="menu">
                    <a class="nav-link" ui-sref="menu">Menu</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{route('home')}}">
                        <i class="fa fa-desktop"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>