@if($render_header)
{!! $renderer->renderFilters() !!}
<h3 id="grid-title">
    {{$title}}&nbsp;
</h3>
@endif
<div class="datagrid-wrapper mt-3">
    {!! $renderer->renderBody() !!}
</div>
{!! $renderer->renderScripts() !!}