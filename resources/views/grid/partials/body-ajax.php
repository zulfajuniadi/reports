
<div class="filter-wrapper" ng-cloak>
    <div class="loading-blocker">
        Loading...
    </div>
    <div class="filter-pane">
        <div class="filter-pane-wrapper">
            <div class="float-right">
                <button class="btn btn-warning btn-sm" ng-if="appliedFilters.length&&!editingFilter.type" ng-click="clearFilters()">
                    Clear All Filters
                </button>
            </div>
            <h4 class="mb-2">{{status}}</h4>
            <div ng-if="!editingFilter.type">
                <div ng-if="availableFilters.length">
                    <select ng-placeholder="Add New Filter" ng-model="$parent.$parent.newFilterType" class="form-control-sm btn-block">
                        <option ng-value="filter" ng-repeat="filter in availableFilters">{{filter.name}}</option>
                    </select>
                </div>
                <div ng-if="newFilterType" class="mt-1">
                    <select ng-model="$parent.$parent.$parent.newFilterValue" class="form-control-sm btn-block" ng-if="$parent.$parent.newFilterType.type == 'Drop Down'">
                        <option ng-value="value" ng-repeat="value in newFilterType.values">{{value}}</option>
                    </select>
                    <select ng-model="$parent.$parent.$parent.newFilterValue" class="form-control form-control-sm btn-block" multiple ng-if="$parent.$parent.newFilterType.type == 'Multiple Drop Down'">
                        <option ng-value="value" ng-repeat="value in newFilterType.values">{{value}}</option>
                    </select>
                    <input type="search" ng-model="$parent.$parent.$parent.newFilterValue" class="form-control form-control-sm" ng-if="$parent.$parent.newFilterType.type == 'Search'">
                    <input type="text" date-range-picker class="form-control form-control-sm" value="$parent.$parent.$parent.newFilterValue" ng-if="$parent.$parent.newFilterType.type == 'Date Range'">
                </div>
                <div ng-if="newFilterValue" class="mt-1">
                    <button class="btn btn-sm btn-primary btn-block" ng-click="addFilter()">
                        <i class="fa fa-plus"></i> Add Filter
                    </button>
                </div>
            </div>
            <div class="list-group mt-2">
                <div class="list-group-item text-left" ng-repeat="filter in appliedFilters">
                    <div ng-if="editingFilter.field != filter.field">
                        <div class="float-right btn-group filter-item-btns">
                            <button class="btn btn-primary" ng-click="editFilter(filter)">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger" ng-click="removeFilter(filter)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                        <div>
                            {{filter.name}}
                        </div>
                        <div class="blockquote-footer">
                            <ul class="list-unstyled" ng-if="filter.values.splice">
                                <li ng-repeat="value in filter.values">{{value}}</li>
                            </ul>
                            <div ng-if="!filter.values.splice">{{filter.values}}</div>
                        </div>
                    </div>
                    <div ng-if="editingFilter.field == filter.field">
                        <div>
                            {{filter.name}}
                        </div>
                        <div class="mt-2">
                            <select ng-model="editingFilter.values" class="form-control-sm btn-block" ng-if="editingFilterSource.type == 'Drop Down'">
                                <option ng-value="value" ng-repeat="value in editingFilterSource.values">{{value}}</option>
                            </select>
                            <select ng-model="editingFilter.values" class="form-control form-control-sm btn-block" multiple ng-if="editingFilterSource.type == 'Multiple Drop Down'">
                                <option ng-value="value" ng-repeat="value in editingFilterSource.values">{{value}}</option>
                            </select>
                            <input type="search" ng-model="editingFilter.values" class="form-control form-control-sm" ng-if="editingFilterSource.type == 'Search'">
                            <input type="text" date-range-picker class="form-control form-control-sm" value="editingFilter.values" ng-if="editingFilterSource.type == 'Date Range'">
                        </div>
                        <button class="btn btn-block btn-sm btn-primary mt-2" ng-click="saveEditingFilter()" ng-if="editingFilter.values">
                            Update Filter
                        </button>
                    </div>
                </div>
            </div>
            <button class="btn btn-block btn-sm btn-primary mt-2" ng-click="runFilter()" ng-if="filters.length&&!editingFilter.type">
                Run Filter
            </button>
        </div>
    </div>
    
    <div class="table-wrapper">
        <table class="table table-bordered table-sm table-striped table-hover" id="exporttable">
            <tr>
                <th class="text-center">Loading...</th>
            </tr>
        </table>
    </div>
</div>