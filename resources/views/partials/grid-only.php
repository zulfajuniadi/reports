<div class="container-fluid" ng-controller="FilterController">
    <div class="float-right" id="filters">
        <button class="btn btn-primary btn-sm" onclick="reloadGridData()">
            <i class="fa fa-redo"></i>
        </button>
        <button class="btn btn-primary btn-sm" onclick="download()">
            <i class="fa fa-file-download"></i> &nbsp;xls
        </button>
        <button class="btn btn-primary btn-sm" onclick="toggleFilters()">
            <i class="fa fa-filter"></i>
            <span id="filter-count">{{appliedFilters.length}}</span>
        </button>
    </div>
    <h3 class="mb-sm-2" id="report-header">{{report.name||'Reports'}}<br /><small ng-if="report.description">{{report.description}}</small></h3>
    <?=$renderer->render($gridTitle, false)?>
</div>
<script>
    window.gridOffsetTop = 160;
</script>
<style>
    #filters {
        padding-top: 46px;
    }
</style>