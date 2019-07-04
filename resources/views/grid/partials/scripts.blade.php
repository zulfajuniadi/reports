<script>
    function refreshTable() {
        $('.loading-blocker').show();
        $.get(window.location.origin + window.location.pathname + '/body' + window.location.search, function(response){
            $('table').html(response);
            $('.loading-blocker').hide();
        }).then(function(){},function(){
            $('.loading-blocker').hide();
        })
    }

    function download() {
        window.location.href = window.location.origin + window.location.pathname + '/download' + window.location.search;
    }

    function toggleFilters() {
        $('.filter-pane').toggleClass('show');
    }

    function sort(field) {
        angular.element('[ng-controller]').scope().sort(field)
    }

    $(document).ready(function(){
        $('.table-wrapper').css('height', window.innerHeight - $('.table-wrapper').position().top - (window.gridOffsetTop || 0));
        refreshTable();
    })
</script>