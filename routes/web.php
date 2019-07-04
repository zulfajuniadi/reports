<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'ReportController@index')->name('home');
Route::get('/reports/{slug}', 'ReportController@report')->name('report');
Route::get('/reports/{slug}/body', 'ReportController@getBody');
Route::get('/reports/{slug}/filters', 'ReportController@getFilters');
Route::get('/reports/{slug}/download', 'ReportController@export')->name('download');
Route::get('/reports/{slug}/preview', 'ReportController@preview');

Route::get('/api/data/chart/{id}', 'Setup\\SetupController@showCharts');
Route::get('/api/data/chart/{id}/data', 'Setup\\SetupController@getChartData');
Route::get('/api/data/grid/{id}', 'Setup\\SetupController@showDataGrid');
Route::get('/api/data/grid/{id}/body', 'Setup\\PreviewController@getBody');
Route::get('/api/data/grid/{id}/filters', 'Setup\\PreviewController@getFilters');

Route::group(['namespace' => 'Setup', 'prefix' => 'setup'], function () {
    Route::get('', 'SetupController@index')->name('setup');

    Route::get('/data-grids', 'SetupController@dataGrids');
    Route::get('/data-grids/{id}', 'SetupController@showDataGrid');
    Route::post('/data-grids', 'SetupController@createDataGrid');
    Route::post('/data-grids/{id}', 'SetupController@updateDataGrid');
    Route::get('/data-grids/{id}/delete', 'SetupController@deleteDataGrid');
    Route::get('/data-grids/{id}/refresh-fields', 'SetupController@refreshFields');
    
    Route::get('/charts', 'SetupController@charts');
    Route::get('/charts/{id}', 'SetupController@showCharts');
    Route::post('/charts', 'SetupController@createCharts');
    Route::post('/charts/{id}', 'SetupController@updateCharts');
    Route::get('/charts/{id}/delete', 'SetupController@deleteCharts');
    Route::get('/charts/{id}/refresh-fields', 'SetupController@refreshFields');

    Route::get('/reports', 'SetupController@reports');
    Route::post('/reports', 'SetupController@createReport');
    Route::get('/reports/{id}', 'SetupController@report');
    Route::post('/reports/{id}', 'SetupController@updateReport');
    Route::get('/reports/{id}/delete', 'SetupController@deleteReport');

    Route::get('/views', 'SetupController@getViews');

    Route::get('/menus', 'SetupController@menus');
    Route::post('/menus', 'SetupController@saveMenus');

    Route::get('/preview/chart-data/{id}', 'PreviewController@getChartData');
    Route::get('/preview/grid/{id}', 'PreviewController@showGrid');
    Route::get('/preview/grid/{id}/body', 'PreviewController@getBody');
    Route::get('/preview/grid/{id}/dump', 'PreviewController@getDump');
    Route::get('/preview/grid/{id}/filters', 'PreviewController@getFilters');
});
