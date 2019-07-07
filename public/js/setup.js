angular.module('setup', ['ui.router', 'ui.tree', 'Report'])
    .config(function ($stateProvider, $urlRouterProvider, $httpProvider) {

        $httpProvider.interceptors.push(function ($q) {
            return {
                request: function (config) {
                    if (config.data) {
                        config.data._token = _token;
                    }
                    Pace.start();
                    return config;
                },

                response: function (results) {
                    Pace.stop();
                    return $q.resolve(results);
                },

                responseError: function (rejection) {
                    Pace.stop();
                    return $q.reject(rejection);
                }
            };
        });

        const resolvers = {
            menus: function ($http) {
                return $http.get(_basePath + '/menus')
                    .then(function (response) {
                        return response.data;
                    });
            },
            dataGrids: function ($http) {
                return $http.get(_basePath + '/data-grids')
                    .then(function (response) {
                        return response.data;
                    });
            },
            charts: function ($http) {
                return $http.get(_basePath + '/charts')
                    .then(function (response) {
                        return response.data;
                    });
            },
            chart: function ($stateParams, $http, $q) {
                if ($stateParams.chart_id) {
                    return $http.get(_basePath + '/charts/' + $stateParams.chart_id)
                        .then(function (response) {
                            return response.data;
                        });
                }
                return $q.resolve({});
            },
            reports: function ($http) {
                return $http.get(_basePath + '/reports')
                    .then(function (response) {
                        return response.data;
                    });
            },
            report: function ($stateParams, $http, $q) {
                if ($stateParams.report_id) {
                    return $http.get(_basePath + '/reports/' + $stateParams.report_id)
                        .then(function (response) {
                            return response.data;
                        }, function(){
                            return null;
                        });
                }
                return $q.resolve({});
            },
        }

        $stateProvider
            .state({
                name: 'menu',
                url: '/menu',
                templateUrl: _templateBasePath + '/menu.html',
                resolve: {
                    menus: resolvers.menus,
                    reports: resolvers.reports,
                },
                controller: 'MenuController'
            })
            .state({
                name: 'datagrids',
                url: '/datagrids?grid_id',
                params: {
                    grid_id: null,
                },
                resolve: {
                    reports: resolvers.reports,
                    dataGrids: resolvers.dataGrids,
                },
                controller: 'DataGridsController',
                templateUrl: _templateBasePath + '/datagrids.html',
            })
            .state({
                name: 'charts',
                url: '/charts?chart_id',
                params: {
                    chart_id: null,
                },
                resolve: {
                    charts: resolvers.charts,
                    chart: resolvers.chart,
                },
                controller: 'ChartsController',
                templateUrl: _templateBasePath + '/charts.html',
            })
            .state({
                name: 'reports',
                url: '/reports?report_id',
                params: {
                    report_id: null,
                },
                resolve: {
                    dataGrids: resolvers.dataGrids,
                    charts: resolvers.charts,
                    reports: resolvers.reports,
                    report: resolvers.report,
                },
                controller: 'ReportsController',
                templateUrl: _templateBasePath + '/reports.html',
            });
        $urlRouterProvider.otherwise('/datagrids');
    })

    .run(function ($rootScope) {
        $rootScope.filterTypes = [
            'Search',
            'Drop Down',
            'Multiple Drop Down',
            'Date Range',
        ];
        $rootScope.summaryTypes = [
            'Month',
        ];
        $rootScope.chartTypes = [
            'Bar Chart',
            'Pie Chart',
            'Doughnut Chart',
            'Stacked Bar Chart',
            'Bar Chart - Multiple Series',
            'Line Chart',
        ];
        $rootScope.reportTypes = [
            'Grid only',
            'Grid with charts on the left',
            'Grid with charts on the right',
            'Charts only',
        ];
    })

    .controller('ChartsController', function ($scope, $http, $q, $stateParams, $state, charts, chart) {
        $scope.charts = charts;
        $scope.chart = chart;
        $scope.chartData = {};

        $('#newGridModal').on('save', function (event, data) {
            $http.post(_basePath + '/charts', data).then(function (response) {
                $state.go('charts', {
                    chart_id: response.data.id
                })
            })
        });

        $scope.createNewChart = function () {
            $('#newGridModal').trigger('reset-data').modal('show');
        }

        $scope.saveChart = function () {
            if ($scope.chart.id) {
                return $http.post(_basePath + '/charts/' + $scope.chart.id, angular.copy($scope.chart));
            }
            return $q.reject();
        }

        $scope.deleteChart = function () {
            return $q(function (success, reject) {
                if ($scope.chart.id) {
                    alerts.confirm('This will remove the chart from all reports!')
                        .then(function (response) {
                            if (response.value) {
                                return $http.get(_basePath + '/charts/' + $scope.chart.id + '/delete')
                                    .then(function () {
                                        $state.go('charts', {
                                            chart_id: null,
                                        });
                                    });
                            }
                            return reject();
                        });
                }
                return reject();
            })
        }

        $scope.refreshData = function () {
            return $http.get(_dataPath + '/chart/' + $scope.chart.id + '/data').then(function (response) {
                $scope.chartData = response.data;
            });
        }

        if ($scope.chart.id)
            $scope.refreshData();
    })

    .controller('ReportsController', function ($scope, dataGrids, charts, reports, report, $http, $q, $stateParams, $state) {
        $scope.dataGrids = dataGrids;
        $scope.charts = charts;
        $scope.reports = reports;
        $scope.report = report;
        $scope.selectedReportId = report.id + '';

        $('#newReportModal').on('save', function (event, data) {
            if (data.id) {
                if ($scope.report.id == data.id) {
                    $scope.report.name = data.name;
                    $scope.report.description = data.description;
                    $scope.report.type = data.type;
                    $scope.saveReport();
                }
            } else {
                $http.post(_basePath + '/reports', data).then(function (response) {
                    $state.go('reports', {
                        report_id: response.data.id
                    })
                })
            }
        });

        $scope.createNewReport = function () {
            $('#newReportModal').trigger('reset-data').modal('show');
        }

        $scope.editReport = function () {
            $('#newReportModal').trigger('set-data', $scope.report).modal('show');
        }

        $scope.showReport = function () {
            $state.go('reports', {
                report_id: $scope.selectedReportId
            })
        }

        $scope.saveReport = function () {
            return $http.post(_basePath + '/reports/' + $scope.report.id, angular.copy($scope.report)).then(function (response) {
                $state.reload();
            })
        }

        $scope.deleteReport = function () {
            alerts.confirm('This will remove this from all menus!').then(function (response) {
                if (response.value) {
                    return $http.get(_basePath + '/reports/' + $scope.report.id + '/delete')
                        .then(function () {
                            $state.go('reports', {
                                report_id: null,
                            });
                        });
                }

            })
        }

        $scope.canAddChart = function (row) {
            return row.cols.reduce(function (then, now) {
                return then + now.width;
            }, 0) < 12;
        }

        $scope.addRow = function () {
            $scope.report.data.rows.push({
                height: 200,
                cols: []
            });
        }

        $scope.addChart = function (row, width) {
            row.cols.push({
                type: 'chart',
                id: null,
                width: width
            });
        }

        $scope.editingColumn = {};
        $scope.editingRow = {};
        $scope.editCol = function (column, row) {
            $scope.editingColumn = column;
            $scope.editingRow = row;
            $('#colModal').trigger('set-data', {
                row: {
                    height: row.height
                },
                column: angular.copy(column)
            }).modal('show');
        }

        $('#colModal').on('save', function (event, data) {
            Object.assign($scope.editingColumn, data.column);
            Object.assign($scope.editingRow, data.row);
            $scope.saveReport();
        });

    })

    .controller('NewReportController', function ($scope, $http, $timeout) {
        $('#newReportModal').on('reset-data', function () {
            $scope.$applyAsync(function () {
                $scope.resetData();
            })
        });

        $('#newReportModal').on('set-data', function (event, data) {
            $scope.$applyAsync(function () {
                $scope.id = data.id;
                $scope.type = data.type;
                $scope.name = data.name;
                $scope.description = data.description;
            })
        });

        $scope.id = null;
        $scope.type = 'Grid only';
        $scope.name = 'New Report';
        $scope.description = '';
        $scope.resetData = function () {
            $scope.id = null;
            $scope.type = 'Grid only';
            $scope.name = 'New Report';
            $scope.description = '';
        }

        $scope.done = function () {
            $('#newReportModal').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('#newReportModal').trigger('save', {
                id: $scope.id,
                type: $scope.type,
                name: $scope.name,
                description: $scope.description,
            });
        }
    })

    .controller('ColumnModalController', function ($scope, $http, $timeout) {
        $('#colModal').on('set-data', function (event, data) {
            $scope.$applyAsync(function () {
                $scope.data = data;
            })
        });

        $scope.data = {};

        $scope.done = function () {
            $('#colModal').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('#colModal').trigger('save', $scope.data);
        }
    })

    .controller('DataGridsController', function ($scope, dataGrids, $http, $q, $stateParams, $state, reports) {
        $scope.reports = reports;
        $scope.dataGrids = dataGrids;
        $scope.currentGridId = $stateParams.grid_id;
        $scope.grid = {};
        $scope.field = {};
        if ($scope.currentGridId) {
            $http.get(_basePath + '/data-grids/' + $scope.currentGridId).then(function (response) {
                $scope.grid = response.data;
            });
        }

        $('#newGridModal').on('save', function (event, data) {
            $http.post(_basePath + '/data-grids', data).then(function (response) {
                $state.go('datagrids', {
                    grid_id: response.data.id
                })
            })
        });

        $scope.createNewGrid = function () {
            $('#newGridModal').trigger('reset-data').modal('show');
        }

        $scope.show = function (grid) {
            $state.go('datagrids', {
                grid_id: grid
            })
        }

        $scope.saveGrid = function () {
            if ($scope.grid.id) {
                return $http.post(_basePath + '/data-grids/' + $scope.grid.id, angular.copy($scope.grid));
            }
            return $q.reject();
        }

        $scope.deleteGrid = function () {
            return $q(function (success, reject) {
                if ($scope.grid.id) {
                    alerts.confirm('This will remove the grid from all reports!')
                        .then(function (response) {
                            if (response.value) {
                                return $http.get(_basePath + '/data-grids/' + $scope.grid.id + '/delete')
                                    .then(function () {
                                        $state.go('datagrids', {
                                            grid_id: null,
                                        });
                                    });
                            }
                            return reject();
                        });
                }
                return reject();
            })
        }

        $scope.refreshFields = function () {
            return $q(function (success, reject) {
                return $http.get(_basePath + '/data-grids/' + $scope.grid.id + '/refresh-fields')
                    .then(function (response) {
                        $scope.grid = response.data;
                        success();
                    }, function () {
                        reject()
                    });
            });
        }

        $scope.moveUp = function () {
            var index = $scope.grid.fields.indexOf($scope.field);
            if (index > 0) {
                $scope.grid.fields.splice(index, 1);
                $scope.grid.fields.splice(index - 1, 0, $scope.field);
                $scope.grid.fields.forEach(function (field, index) {
                    field.sort_order = index;
                });
            }
        }

        $scope.canMoveUp = function (field) {
            return $scope.grid.fields.indexOf(field) > 0;
        }

        $scope.canMoveDown = function (field) {
            return $scope.grid.fields.indexOf(field) < $scope.grid.fields.length - 1;
        }

        $scope.moveDown = function () {
            var index = $scope.grid.fields.indexOf($scope.field);
            if (index < $scope.grid.fields.length - 1) {
                $scope.grid.fields.splice(index, 1);
                $scope.grid.fields.splice(index + 1, 0, $scope.field);
                $scope.grid.fields.forEach(function (field, index) {
                    field.sort_order = index;
                });
            }
        }

        $scope.viewGrid = function (id) {
            window.open(location.origin + location.pathname + '/preview/grid/' + id, '_blank');
        }
    })

    .controller('NewGridController', function ($scope, $http, $timeout) {
        $scope.views = [];
        $http.get(_basePath + '/views')
            .then(function (response) {
                $scope.views = response.data;
            });
        $('#newGridModal').on('reset-data', function () {
            $scope.$applyAsync(function () {
                $scope.resetData();
            })
        });

        $scope.view_name = null;
        $scope.name = '';
        $scope.resetData = function () {
            $scope.view_name = null;
            $scope.name = '';
        }

        $scope.done = function () {
            $('#newGridModal').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('#newGridModal').trigger('save', {
                view_name: $scope.view_name,
                name: $scope.name,
            });
        }
    })

    .controller('MenuController', function ($scope, menus, $http, reports, $timeout) {
        $scope.menus = menus || [];
        $scope.editing = null;
        $scope.reports = reports;
        if ($scope.menus.length > 0) {
            $scope.editing = $scope.menus[0];
        }

        $scope.addMenu = function (parent) {
            const newMenu = {
                id: makeid(),
                title: 'New Menu',
                type: 'link',
                href: '#',
                is_enabled: '1',
                nodes: []
            };
            if (!parent) {
                $scope.menus.push(newMenu);
            } else {
                parent.nodes.push(newMenu);
            }
            $scope.editing = newMenu;
        }

        $scope.saveMenus = function () {
            return $http.post(_basePath + '/menus', {
                menus: angular.copy($scope.menus)
            });
        }

        $scope.setEditing = function (node) {
            $scope.editing = node;
        }

        function spliceNode(nodes, node) {
            if (nodes.indexOf(node) > -1) {
                nodes.splice(node, 1);
            } else {
                nodes.forEach(function (newNode) {
                    spliceNode(newNode.nodes, node);
                });
            }
        }

        $scope.deleteEditing = function (node) {
            alerts.confirm('This will remove the menu').then(function (response) {
                if (response.value) {
                    $scope.$apply(function () {
                        spliceNode($scope.menus, node);
                        $scope.editing = null;
                    });
                }
            })
        }
    })

    .directive('colorScheme', function () {
        return {
            scope: {
                colorScheme: '=',
                limit: '='
            },
            link: function (scope, element) {
                scope.limitColors = parseInt(scope.limit, 10) || null;
                scope.result = $('<div class="color-scheme-result mb-1"></div>');
                scope.picker = $('<select class="form-control"><select>');
                var moveDownBtn = $(`
                <div class="input-group-append">
                    <button class="btn btn-primary" type="button" class="input-group-text">
                        <i class="fa fa-arrow-down"></i>
                    </button>
                </div>`);
                var moveUpBtn = $(`
                <div class="input-group-append">
                    <button class="btn btn-primary" type="button" class="input-group-text">
                        <i class="fa fa-arrow-up"></i>
                    </button>
                </div>`);
                scope.inputGroup = $(`<div class="input-group mb-3"></div>`);
                scope.inputGroup.append(scope.picker);
                scope.inputGroup.append(moveUpBtn);
                scope.inputGroup.append(moveDownBtn);

                var schemeNames = [];
                var optionsString = '';
                Object.keys(colorSchemes).forEach(function (scheme) {
                    if (scope.limitColors) {
                        if (colorSchemes[scheme].length == scope.limitColors) {
                            optionsString += '<option value="' + scheme + '">' + scheme + '</option>';
                            schemeNames.push(scheme);
                        }

                    } else {
                        optionsString += '<option value="' + scheme + '">' + scheme + '</option>';
                        schemeNames.push(scheme);
                    }
                })
                var schemeLength = schemeNames.length;
                moveUpBtn.find('button').click(function () {
                    var index = schemeNames.indexOf(scope.colorScheme);
                    if (index < 1) {
                        return false;
                    }
                    index--;
                    scope.picker.val(schemeNames[index]).trigger('change');
                });
                moveDownBtn.find('button').click(function () {
                    var index = schemeNames.indexOf(scope.colorScheme);
                    if (index + 1 >= schemeLength) {
                        return false;
                    }
                    index++;
                    scope.picker.val(schemeNames[index]).trigger('change');
                });

                element.append(scope.inputGroup);
                element.append(scope.result);
                scope.picker.html(optionsString);
                scope.picker.val(scope.colorScheme);

                scope.picker.on('change', function () {
                    scope.$applyAsync(function () {
                        scope.colorScheme = scope.picker.val();
                        var colorDivs = '';
                        if (colorSchemes[scope.colorScheme]) {
                            colorSchemes[scope.colorScheme].forEach(function (color) {
                                colorDivs += '<div style="background-color:' + color + '">&nbsp;</div>'
                            })
                        }
                        scope.result.html(colorDivs);

                        var index = schemeNames.indexOf(scope.colorScheme);
                        if (index < 1) {
                            moveUpBtn.find('button').attr('disabled', 'disabled');
                        } else {
                            moveUpBtn.find('button').removeAttr('disabled', 'disabled');
                        }
                        if (index + 1 >= schemeLength) {
                            moveDownBtn.find('button').attr('disabled', 'disabled');
                        } else {
                            moveDownBtn.find('button').removeAttr('disabled', 'disabled');
                        }
                    })
                })

                scope.picker.trigger('change');
            }
        }
    })
