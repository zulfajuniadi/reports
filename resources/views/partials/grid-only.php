<div class="container-fluid" ng-controller="FilterController">
    <?=$renderer->renderFilters()?>
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