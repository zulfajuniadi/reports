<div class="container-fluid" ng-controller="FilterController">
    <h3 class="mb-2" id="report-header">{{report.name||'Reports'}}<br /><small ng-if="report.description">{{report.description}}</small>
    </h3>
    <div class="row">
        <div class="col-md-3">
            <div class="row" ng-repeat="row in report.data.rows">
                <div class="col-md-{{col.width}} mt-2" ng-style="{height: row.height+'px'}"
                    ng-repeat="col in row.cols">
                    <div style="height: 100%" class="col-wrapper">
                        <div ng-init="col=col;row=row" ng-if="col.id" ng-controller="ChartController"
                            class="chart-container" height="100%"
                            ng-style="{'background-color': chart.bg_color}">
                            <div ng-if="chart.id" class="chart-title text-light"
                                ng-class="{absleft: chart.label_pos=='left', absright: chart.label_pos=='right'}">
                                {{chart.name}}
                            </div>
                            <button ng-if="chart.id" class="btn btn-sm reload-btn" async-button="refreshData()"
                                default-icon="'sync'"
                                ng-class="{absleft: chart.label_pos=='left', absright: chart.label_pos=='right'}"></button>
                            <canvas chart-js="chart" data="chartData" height="{{row.height+'px'}}"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <?=$renderer->render($gridTitle)?>
        </div>
    </div>
</div>
<script>
    window.gridOffsetTop = 200;
</script>