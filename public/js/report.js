angular.module('Report', [])
    .controller('ChartController', function ($scope, $http) {
        $scope.chart = {};
        $scope.chartData = {};
        $scope.$watch('col', function () {
            $chartData = {};
            $http.get(_dataPath + '/chart/' + $scope.col.id).then(function (response) {
                $scope.chart = response.data;
                $scope.refreshData();
            });
        }, true);

        $scope.refreshData = function () {
            return $http.get(_dataPath + '/chart/' + $scope.chart.id + '/data').then(function (response) {
                $scope.chartData = response.data;
            });
        }
    })

    .controller('GridController', function ($scope, $http) {
        $scope.gridData = {};
        $scope.height = window.innerHeight - 220;
        $scope.$watch('grid', function () {
            if ($scope.grid.id) {
                $gridData = {};
                $http.get(_dataPath + '/grid/' + $scope.grid.id).then(function (response) {
                    $scope.gridData = response.data;
                }, function(){
                    return;
                });
            }
        }, true);
    })

    .controller('ReportController', function ($scope) {
        $scope.report = _report;
    })


    .controller('FilterController', function ($scope, $http, $interpolate, $sce) {
        $scope.newFilterType = null;
        $scope.newFilterValue = [];
        $scope.filters = [];
        $scope.defaultFilters = [];
        $scope.availableFilters = [];
        $scope.appliedFilters = [];
        $scope.status = 'Loading...';
        $scope.editingFilter = {};
        $scope.editingFilterSource = {};
        $scope.datas = [];
        $scope.headers = [];
        $scope.currentSort = null;
        $scope.currentSortDir = 'asc';
        $http.get(window.location.origin + window.location.pathname + '/filters' + window.location.search)
            .then(function (response) {
                $scope.filters = Object.deepCopy(response.data);
                $scope.availableFilters = Object.deepCopy(response.data);
                $scope.status = 'Filters';
                parseQueryString();
                response.data.forEach(function(filter){ 
                    if(filter.is_default) {
                        $scope.defaultFilters.push({
                            field: filter.field,
                            type: filter.type,
                            name: filter.name,
                            value: filter.value,
                        });
                    } 
                });
                $scope.defaultFilters.forEach(function(defaultFilter){
                    var found = $scope.appliedFilters.filter(function(appliedFilter){
                        return appliedFilter.name == defaultFilter.name;
                    }).pop();
                    if(!found) {
                        $scope.appliedFilters.push({
                            field: defaultFilter.field,
                            type: defaultFilter.type,
                            name: defaultFilter.name,
                            value: defaultFilter.value
                        });
                    }
                });
                
                if (location.search.length < 2) {
                    $scope.runFilter(true);
                }
                hideAppliedFilters();
                $scope.refreshTable();
            });

        function parseQueryString() {
            $scope.appliedFilters = [];
            var filters = {};
            location.search.slice(1).split('&').forEach(function (str) {
                var parts = str.split('=');
                if (parts.length == 2) {
                    var key = decodeURIComponent(parts[0]);
                    if (parts[0] == 'sort_by_columns') {
                        $scope.currentSort = parts[1];
                    } else if (parts[0] == 'sort_by_directions') {
                        $scope.currentSortDir = parts[1];
                    } else if (key.indexOf('[]') > -1) {
                        key = key.substr(0, key.length - 2);
                        if (!filters[key]) {
                            filters[key] = [];
                        }
                        filters[key].push(decodeURIComponent(parts[1]));
                    } else {
                        filters[key] = decodeURIComponent(parts[1]);
                    }
                }
            });
            Object.keys(filters).forEach(function (key) {
                $scope.availableFilters.forEach(function (filter) {
                    if (filter.field == key) {
                        $scope.appliedFilters.push({
                            field: filter.field,
                            type: filter.type,
                            name: filter.name,
                            value: filters[key]
                        });
                    }
                });
            });
        }

        $scope.addFilter = function () {
            $scope.appliedFilters.unshift({
                field: $scope.newFilterType.field,
                type: $scope.newFilterType.type,
                name: $scope.newFilterType.name,
                value: $scope.newFilterValue
            });
            $scope.newFilterType = null;
            $scope.newFilterValue = [];
            hideAppliedFilters();
        }

        function hideAppliedFilters() {
            $scope.appliedFilters.forEach(function (filter) {
                var foundIndex = -1;
                $scope.availableFilters.forEach(function (available, index) {
                    if (available.field == filter.field) {
                        foundIndex = index;
                    }
                });
                if (foundIndex > -1) {
                    $scope.availableFilters.splice(foundIndex, 1);
                }
            });
        }

        $scope.runFilter = function (replaceHistory) {
            var qs = [];
            $scope.appliedFilters.forEach(function (filter) {
                switch (filter.type) {
                    case 'Drop Down':
                    case 'Search':
                        var val = encodeURIComponent(filter.value);
                        qs.push(`${filter.field}=${val}`);
                        break;
                    case 'Multiple Drop Down':
                        filter.value.forEach(function (val) {
                            val = encodeURIComponent(val);
                            qs.push(`${filter.field}[]=${val}`);
                        });
                        break;
                    case 'Date Range':
                        filter.value.forEach(function (val) {
                            val = encodeURIComponent(val);
                            qs.push(`${filter.field}[]=${val}`);
                        });
                        break;
                    default:
                        console.log(filter.type);
                }
            });

            if ($scope.currentSort) {
                var foundIndex = -1;
                qs.forEach(function (params, index) {
                    if (params.indexOf('sort_by_columns') > -1) {
                        foundIndex = index;
                    }
                });
                if (foundIndex > -1) {
                    qs.splice(foundIndex, 1);
                }
                foundIndex = -1;
                qs.forEach(function (params, index) {
                    if (params.indexOf('sort_by_directions') > -1) {
                        foundIndex = index;
                    }
                });
                if (foundIndex > -1) {
                    qs.splice(foundIndex, 1);
                }
                qs.push('sort_by_columns=' + $scope.currentSort);
                qs.push('sort_by_directions=' + $scope.currentSortDir);
            } else {
                var foundIndex = -1;
                qs.forEach(function (params, index) {
                    if (params.indexOf('sort_by_columns') > -1) {
                        foundIndex = index;
                    }
                });
                if (foundIndex > -1) {
                    qs.splice(foundIndex, 1);
                }
                foundIndex = -1;
                qs.forEach(function (params, index) {
                    if (params.indexOf('sort_by_directions') > -1) {
                        foundIndex = index;
                    }
                });
                if (foundIndex > -1) {
                    qs.splice(foundIndex, 1);
                }
            }

            qs = '?' + qs.join('&');
            if (qs.length == 1) {
                qs = '';
            }
            updateDownloadLink();
            if (replaceHistory === true) {
                window.history.replaceState({}, document.title, window.location.origin + window.location.pathname + qs);
            } else {
                window.history.pushState({}, document.title, window.location.origin + window.location.pathname + qs);
                $scope.refreshTable();
            }
        }

        $scope.clearFilters = function () {
            $scope.appliedFilters = Object.deepCopy(angular.copy($scope.defaultFilters));
            $scope.availableFilters = Object.deepCopy(angular.copy($scope.filters));
            hideAppliedFilters();
        }

        $scope.removeFilter = function (filter) {
            $scope.appliedFilters.splice($scope.appliedFilters.indexOf(filter), 1);
            var foundIndex = -1;
            $scope.filters.forEach(function (available, index) {
                if (available.field == filter.field) {
                    foundIndex = index;
                }
            });
            if (foundIndex > -1) {
                $scope.availableFilters.push(
                    Object.deepCopy(angular.copy($scope.filters[foundIndex]))
                );
            }
        }

        $scope.editFilter = function (filter) {
            var foundIndex = -1;
            $scope.filters.forEach(function (available, index) {
                if (available.field == filter.field) {
                    foundIndex = index;
                }
            });
            if (foundIndex > -1) {
                $scope.editingFilter = filter;
                $scope.editingFilterSource = Object.deepCopy(angular.copy($scope.filters[foundIndex]));
            }
        }

        $scope.saveEditingFilter = function () {
            $scope.editingFilter = {};
            $scope.editingFilterSource = {};
        }

        $scope.sort = function (column) {
            if ($scope.currentSort != column) {
                $scope.currentSort = column;
                $scope.currentSortDir = 'asc';
            } else {
                switch ($scope.currentSortDir) {
                    case 'asc':
                        $scope.currentSortDir = 'desc';
                        break;
                    case 'desc':
                        $scope.currentSort = null;
                        break;
                }
            }
            $scope.$applyAsync(function () {
                $scope.runFilter();
            });
        }

        $scope.$watch('newFilterType', function () {
            $scope.newFilterValue = null;
        }, true);

        function updateDownloadLink() {
            $('#downloadLink').attr('href', window.location.origin + window.location.pathname + '/download' + location.search);
        }

        $scope.refreshTable = function refreshTable () {
            $('.loading-blocker').show();
            $http.get(window.location.origin + window.location.pathname + '/body' + window.location.search).then(function(response){
                    $scope.data = response.data;
                    $('.loading-blocker').hide();
            },function(){
                $('.loading-blocker').hide();
            })
        }

        $http.get(window.location.origin + window.location.pathname + '/headers' + window.location.search).then(function(response){
            $scope.headers = response.data;
            $scope.$applyAsync();
        })

        updateDownloadLink();
    })

    .directive('dateRangePicker', function () {
        return {
            scope: {
                value: '='
            },
            link: function (scope, element) {
                var instance = element.daterangepicker({
                    timePicker: true,
                    opens: 'left',
                    locale: {
                        format: 'Do MMM hh:mm A'
                    },
                }, function (start, end, label) {
                    scope.$apply(function () {
                        scope.value = [
                            start.format('YYYY-MM-DD HH:mm:ss'),
                            end.format('YYYY-MM-DD HH:mm:ss')
                        ]
                    });
                    // scope.$parent.$parent.$parent.$apply();
                }).data('daterangepicker');
                if (scope.value && scope.value.length == 2) {
                    instance.setStartDate(moment(scope.value[0])._d);
                    instance.setEndDate(moment(scope.value[1])._d);
                }
            }
        }
    })

    .directive('asyncButton', function ($timeout) {
        return {
            scope: {
                asyncButton: '&',
                defaultIcon: '=',
            },
            link: function (scope, element) {
                element.html('<i class="fa fa-' + scope.defaultIcon + '"></i>');
                element.click(function () {
                    element.html('<i class="spinner-border spinner-border-sm" role="status"></i>');
                    scope.asyncButton().then(function () {
                        $timeout(function () {
                            element.html('<i class="fa fa-check"></i>');
                        }, 500);
                    }, function () {
                        $timeout(function () {
                            element.html('<i class="fa fa-times"></i>');
                        }, 500);
                    }).finally(function () {
                        $timeout(function () {
                            element.html('<i class="fa fa-' + scope.defaultIcon + '"></i>');
                        }, 1500);
                    });
                });
            }
        }
    })

    .directive('chartJs', function () {
        return {
            scope: {
                chartJs: '=',
                data: '=',
            },
            controller: function ($scope, $element, $http, $timeout) {
                var ctx = $element[0].getContext('2d');
                var chart = null;

                function buildChart() {
                    if (chart)
                        chart.destroy();
                    if (!$scope.data.labels) {
                        $element.parent().addClass('no-data');
                        return;
                    };
                    if (!$scope.data.labels.length) {
                        $element.parent().addClass('no-data');
                        return;
                    };
                    $element.parent().removeClass('no-data');
                    switch ($scope.chartJs.type) {
                        case 'Pie Chart':
                            var opts = Object.deepCopy(chartOpts.doughnut);
                            opts.plugins.colorschemes.scheme = $scope.chartJs.color_scheme;
                            chart = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: $scope.data.labels,
                                    datasets: [{
                                        data: $scope.data.datasets,
                                        borderWidth: 0
                                    }]
                                },
                                options: opts
                            });
                            break;
                        case 'Doughnut Chart':
                            var opts = Object.deepCopy(chartOpts.doughnut);
                            opts.plugins.colorschemes.scheme = $scope.chartJs.color_scheme;
                            chart = new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: $scope.data.labels,
                                    datasets: [{
                                        data: $scope.data.datasets,
                                        borderWidth: 0
                                    }]
                                },
                                options: opts
                            });
                            break;
                        case 'Bar Chart':
                            var datasets = [];
                            $scope.data.datasets.forEach(function (val, index) {
                                datasets.push({
                                    label: $scope.data.labels[index],
                                    data: [val]
                                })
                            })
                            var opts = Object.deepCopy(chartOpts.bar);
                            opts.plugins.colorschemes.scheme = $scope.chartJs.color_scheme;
                            chart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: [$scope.chartJs.name],
                                    datasets: datasets
                                },
                                options: opts
                            });
                            break;
                        case 'Line Chart':
                        case 'Stacked Bar Chart':
                        case 'Bar Chart - Multiple Series':
                            var xAxis = {};
                            var yAxis = {};
                            var dataSets = {};
                            var dataVals = {};

                            $scope.data.labels.forEach(function (label, index) {
                                xAxis[label[0]] = label[0];
                                yAxis[label[1]] = label[1];
                                dataVals[label[0] + label[1]] = $scope.data.datasets[index];
                            });

                            xAxis = Object.keys(xAxis);
                            yAxis = Object.keys(yAxis);

                            xAxis.forEach(function (xAxisKey) {
                                dataSets[xAxisKey] = {};
                                yAxis.forEach(function (yAxisKey) {
                                    dataSets[xAxisKey][yAxisKey] = dataVals[xAxisKey + yAxisKey] || 0;
                                });
                            });

                            var labels = xAxis;
                            var datasets = {};
                            yAxis.forEach(function (yKey) {
                                if (!datasets[yKey]) {
                                    datasets[yKey] = {
                                        label: yKey,
                                        data: []
                                    }
                                }
                                Object.keys(dataSets).map(function (set, index) {
                                    datasets[yKey].data[index] = parseInt(dataSets[set][yKey], 10) || 0;
                                });
                            })

                            datasets = Object.keys(datasets).map(function (key) {
                                return datasets[key];
                            });

                            var type = 'bar';
                            var opts = Object.deepCopy(chartOpts.stackedBar);
                            if ($scope.chartJs.type == 'Line Chart') {
                                type = 'line';
                                opts = Object.deepCopy(chartOpts.line);
                                datasets.forEach(function (set) {
                                    set.fill = false;
                                })
                            } else if ($scope.chartJs.type == 'Bar Chart - Multiple Series') {
                                opts.scales.yAxes[0].stacked = false;
                                opts.scales.xAxes[0].stacked = false;
                                opts.scales.xAxes[0].categoryPercentage = 0.9;
                            }
                            opts.plugins.colorschemes.scheme = $scope.chartJs.color_scheme;

                            chart = new Chart(ctx, {
                                type: type,
                                data: {
                                    labels: labels,
                                    datasets: datasets
                                },
                                options: opts
                            });
                            break;
                    }
                }

                $scope.$watch('data', buildChart, true);
                $scope.$watch('chartJs', buildChart, true);


                var chartOpts = {
                    doughnut: {
                        maintainAspectRatio: false,
                        legend: {
                            display: false,
                        },
                        plugins: {
                            colorschemes: {
                                scheme: 'brewer.RdYlGn11',
                            }
                        },
                        layout: {
                            padding: {
                                top: 10,
                                right: 10,
                                bottom: 10,
                                left: 10
                            }
                        }
                    },
                    line: {
                        maintainAspectRatio: false,
                        legend: {
                            display: false,
                        },
                        plugins: {
                            colorschemes: {
                                scheme: 'brewer.RdYlGn11',
                            }
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                },
                                gridLines: {
                                    display: false,
                                },
                                display: false,
                            }],
                            xAxes: [{
                                gridLines: {
                                    display: false,
                                },
                                barPercentage: 1,
                                categoryPercentage: 1,
                                display: false,
                                stacked: true
                            }],
                        },
                        layout: {
                            padding: {
                                top: 10,
                                right: 10,
                                bottom: 10,
                                left: 10
                            }
                        },
                        elements: {
                            line: {
                                tension: 0
                            }
                        }
                    },
                    stackedBar: {
                        maintainAspectRatio: false,
                        legend: {
                            display: false,
                        },
                        plugins: {
                            colorschemes: {
                                scheme: 'brewer.RdYlGn11',
                            }
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                },
                                gridLines: {
                                    display: false,
                                },
                                display: false,
                                stacked: true
                            }],
                            xAxes: [{
                                gridLines: {
                                    display: false,
                                },
                                barPercentage: 1,
                                categoryPercentage: 1,
                                display: false,
                                stacked: true
                            }],
                        },
                        layout: {
                            padding: {
                                top: 10,
                                right: 10,
                                bottom: 10,
                                left: 10
                            }
                        }
                    },
                    bar: {
                        maintainAspectRatio: false,
                        legend: {
                            display: false,
                        },
                        plugins: {
                            colorschemes: {
                                scheme: 'brewer.RdYlGn11',
                            }
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                },
                                gridLines: {
                                    display: false,
                                },
                                display: false
                            }],
                            xAxes: [{
                                gridLines: {
                                    display: false,
                                },
                                barPercentage: 1,
                                categoryPercentage: 1,
                                display: false
                            }],
                        },
                        layout: {
                            padding: {
                                top: 10,
                                right: 10,
                                bottom: 10,
                                left: 10
                            }
                        }
                    }
                };
            }
        }
    });
