<div class="float-right" id="filters">
    <button class="btn btn-primary btn-sm" onclick="refreshTable()">
        <i class="fa fa-redo"></i>
    </button>
    <button class="btn btn-primary btn-sm" onclick="download()">
        <i class="fa fa-file-download"></i> &nbsp;xls
    </button>
    <button class="btn btn-primary btn-sm" onclick="toggleFilters()">
        <i class="fa fa-filter"></i>
        <span id="filter-count">@{{appliedFilters.length}}</span>
    </button>
</div>