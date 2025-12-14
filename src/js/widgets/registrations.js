"use strict"

// Class definition
var gaugeApplications = (function () {
    // Private methods


    // year
    var initYearly = function () {
        // Check if amchart library is included
        if (typeof am5 === "undefined") {
            return;
        }

        var element = document.getElementById("yearly-gauge");

        if (!element) {
            return;
        }

        var root;

        var init = function () {
            // Create root element
            root = am5.Root.new(element);

            // Hide amCharts 5 logo
            root._logo.dispose();

            // Set themes
            root.setThemes([am5themes_Animated.new(root)]);

            // Create chart
            var chart = root.container.children.push(
                am5radar.RadarChart.new(root, {
                    panX: false,
                    panY: false,
                    wheelX: "panX",
                    wheelY: "zoomX",
                    innerRadius: am5.percent(25),
                    startAngle: -90,
                    endAngle: 180,
                })
            );

            // Data
            var data = [{
                    category: "A1",
                    value: 55,
                    full: 100,
                    columnSettings: {
                        fillOpacity: 1,
                        fill: am5.color(KTUtil.getCssVariableValue('--bs-danger')),
                    },
                },
                {
                    category: "A2",
                    value: 92,
                    full: 100,
                    columnSettings: {
                        fillOpacity: 1,
                        fill: am5.color(KTUtil.getCssVariableValue('--bs-primary')),
                    },
                },
                {
                    category: "A3",
                    value: 68,
                    full: 100,
                    columnSettings: {
                        fillOpacity: 1,
                        fill: am5.color(KTUtil.getCssVariableValue('--bs-success')),
                    },
                },
            ];

            // Add cursor
            var cursor = chart.set(
                "cursor",
                am5radar.RadarCursor.new(root, {
                    behavior: "zoomX",
                })
            );

            cursor.lineY.set("visible", false);

            // Create axes and their renderers
            var xRenderer = am5radar.AxisRendererCircular.new(root, {
                //minGridDistance: 50
            });

            xRenderer.labels.template.setAll({
                radius: 10
            });

            xRenderer.grid.template.setAll({
                forceHidden: true,
            });

            var xAxis = chart.xAxes.push(
                am5xy.ValueAxis.new(root, {
                    renderer: xRenderer,
                    min: 0,
                    max: 100,
                    strictMinMax: true,
                    numberFormat: "#'%'",
                    tooltip: am5.Tooltip.new(root, {}),
                })
            );

            xAxis.get("renderer").labels.template.setAll({
                fill: am5.color(KTUtil.getCssVariableValue('--bs-gray-500')),
                fontWeight: "500",
                fontSize: 16,
            });

            var yRenderer = am5radar.AxisRendererRadial.new(root, {
                minGridDistance: 20,
            });

            yRenderer.labels.template.setAll({
                centerX: am5.p100,
                fontWeight: "500",
                fontSize: 18,
                fill: am5.color(KTUtil.getCssVariableValue('--bs-gray-500')),
                templateField: "columnSettings",
            });

            yRenderer.grid.template.setAll({
                forceHidden: true,
            });

            var yAxis = chart.yAxes.push(
                am5xy.CategoryAxis.new(root, {
                    categoryField: "category",
                    renderer: yRenderer,
                })
            );

            yAxis.data.setAll(data);

            // Create series
            var series1 = chart.series.push(
                am5radar.RadarColumnSeries.new(root, {
                    xAxis: xAxis,
                    yAxis: yAxis,
                    clustered: false,
                    valueXField: "full",
                    categoryYField: "category",
                    fill: root.interfaceColors.get("alternativeBackground"),
                })
            );

            series1.columns.template.setAll({
                width: am5.p100,
                fillOpacity: 0.08,
                strokeOpacity: 0,
                cornerRadius: 20,
            });

            series1.data.setAll(data);

            var series2 = chart.series.push(
                am5radar.RadarColumnSeries.new(root, {
                    xAxis: xAxis,
                    yAxis: yAxis,
                    clustered: false,
                    valueXField: "value",
                    categoryYField: "category",
                })
            );

            series2.columns.template.setAll({
                width: am5.p100,
                strokeOpacity: 0,
                tooltipText: "{category}: {valueX}%",
                cornerRadius: 20,
                templateField: "columnSettings",
            });

            series2.data.setAll(data);

            // Animate chart and series in
            series1.appear(1000);
            series2.appear(1000);
            chart.appear(1000, 100);
        }

        am5.ready(function () {
            init();
        });

        // Update chart on theme mode change
        KTThemeMode.on("kt.thememode.change", function () {
            // Destroy chart
            root.dispose();

            // Reinit chart
            init();
        });
    };

    // month
    var initMonthly = function () {
        // Check if amchart library is included
        if (typeof am5 === "undefined") {
            return;
        }

        var element = document.getElementById("monthly-gauge");

        if (!element) {
            return;
        }

        var root;

        var init = function () {
            // Create root element
            root = am5.Root.new(element);

            // Hide amCharts 5 logo
            root._logo.dispose();

            // Set themes
            root.setThemes([am5themes_Animated.new(root)]);

            // Create chart
            var chart = root.container.children.push(
                am5radar.RadarChart.new(root, {
                    panX: false,
                    panY: false,
                    wheelX: "panX",
                    wheelY: "zoomX",
                    innerRadius: am5.percent(25),
                    startAngle: -90,
                    endAngle: 180,
                })
            );

            // Data
            var data = [{
                    category: "A1",
                    value: 45,
                    full: 100,
                    columnSettings: {
                        fillOpacity: 1,
                        fill: am5.color(KTUtil.getCssVariableValue('--bs-danger')),
                    },
                },
                {
                    category: "A2",
                    value: 60,
                    full: 100,
                    columnSettings: {
                        fillOpacity: 1,
                        fill: am5.color(KTUtil.getCssVariableValue('--bs-primary')),
                    },
                },
                {
                    category: "A3",
                    value: 85,
                    full: 100,
                    columnSettings: {
                        fillOpacity: 1,
                        fill: am5.color(KTUtil.getCssVariableValue('--bs-success')),
                    },
                },
            ];

            // Add cursor
            var cursor = chart.set(
                "cursor",
                am5radar.RadarCursor.new(root, {
                    behavior: "zoomX",
                })
            );

            cursor.lineY.set("visible", false);

            // Create axes and their renderers
            var xRenderer = am5radar.AxisRendererCircular.new(root, {
                //minGridDistance: 50
            });

            xRenderer.labels.template.setAll({
                radius: 10
            });

            xRenderer.grid.template.setAll({
                forceHidden: true,
            });

            var xAxis = chart.xAxes.push(
                am5xy.ValueAxis.new(root, {
                    renderer: xRenderer,
                    min: 0,
                    max: 100,
                    strictMinMax: true,
                    numberFormat: "#'%'",
                    tooltip: am5.Tooltip.new(root, {}),
                })
            );

            xAxis.get("renderer").labels.template.setAll({
                fill: am5.color(KTUtil.getCssVariableValue('--bs-gray-500')),
                fontWeight: "500",
                fontSize: 16,
            });

            var yRenderer = am5radar.AxisRendererRadial.new(root, {
                minGridDistance: 20,
            });

            yRenderer.labels.template.setAll({
                centerX: am5.p100,
                fontWeight: "500",
                fontSize: 18,
                fill: am5.color(KTUtil.getCssVariableValue('--bs-gray-500')),
                templateField: "columnSettings",
            });

            yRenderer.grid.template.setAll({
                forceHidden: true,
            });

            var yAxis = chart.yAxes.push(
                am5xy.CategoryAxis.new(root, {
                    categoryField: "category",
                    renderer: yRenderer,
                })
            );

            yAxis.data.setAll(data);

            // Create series
            var series1 = chart.series.push(
                am5radar.RadarColumnSeries.new(root, {
                    xAxis: xAxis,
                    yAxis: yAxis,
                    clustered: false,
                    valueXField: "full",
                    categoryYField: "category",
                    fill: root.interfaceColors.get("alternativeBackground"),
                })
            );

            series1.columns.template.setAll({
                width: am5.p100,
                fillOpacity: 0.08,
                strokeOpacity: 0,
                cornerRadius: 20,
            });

            series1.data.setAll(data);

            var series2 = chart.series.push(
                am5radar.RadarColumnSeries.new(root, {
                    xAxis: xAxis,
                    yAxis: yAxis,
                    clustered: false,
                    valueXField: "value",
                    categoryYField: "category",
                })
            );

            series2.columns.template.setAll({
                width: am5.p100,
                strokeOpacity: 0,
                tooltipText: "{category}: {valueX}%",
                cornerRadius: 20,
                templateField: "columnSettings",
            });

            series2.data.setAll(data);

            // Animate chart and series in
            series1.appear(1000);
            series2.appear(1000);
            chart.appear(1000, 100);
        }

        am5.ready(function () {
            init();
        });

        // Update chart on theme mode change
        KTThemeMode.on("kt.thememode.change", function () {
            // Destroy chart
            root.dispose();

            // Reinit chart
            init();
        });
    };

    // Public methods
    return {
        init: function () {
            initYearly();
            initMonthly();
        },
    };
})();

// Class definition
var summaryApplications = (function () {
    var chart = {
        self: null,
        rendered: false,
    };

    // Private methods
    var initChart = function (chart) {
        var element = document.getElementById("summary-application");

        if (!element) {
            return;
        }

        var height = parseInt(KTUtil.css(element, "height"));
        var labelColor = KTUtil.getCssVariableValue("--bs-gray-900");
        var borderColor = KTUtil.getCssVariableValue("--bs-border-dashed-color");

        var options = {
            series: [{
                name: "Permohonan",
                data: [54, 42, 75, 110, 23, 87, 50],
            }, ],
            chart: {
                fontFamily: "inherit",
                type: "bar",
                height: height,
                toolbar: {
                    show: false,
                },
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: ["25%"],
                    borderRadius: 5,
                    dataLabels: {
                        position: "top", // top, center, bottom
                    },
                    startingShape: "flat",
                },
            },
            legend: {
                show: false,
            },
            dataLabels: {
                enabled: true,
                offsetY: -28,
                style: {
                    fontSize: "13px",
                    colors: [labelColor],
                },
                formatter: function (val) {
                    return val; // + "H";
                },
            },
            stroke: {
                show: true,
                width: 2,
                colors: ["transparent"],
            },
            xaxis: {
                categories: [
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "May",
                    "Jun",
                    "Jul",
                ],
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
                labels: {
                    style: {
                        colors: KTUtil.getCssVariableValue("--bs-gray-500"),
                        fontSize: "13px",
                    },
                },
                crosshairs: {
                    fill: {
                        gradient: {
                            opacityFrom: 0,
                            opacityTo: 0,
                        },
                    },
                },
            },
            yaxis: {
                labels: {
                    style: {
                        colors: KTUtil.getCssVariableValue("--bs-gray-500"),
                        fontSize: "13px",
                    },
                    formatter: function (val) {
                        return val;
                    },
                },
            },
            fill: {
                opacity: 1,
            },
            states: {
                normal: {
                    filter: {
                        type: "none",
                        value: 0,
                    },
                },
                hover: {
                    filter: {
                        type: "none",
                        value: 0,
                    },
                },
                active: {
                    allowMultipleDataPointsSelection: false,
                    filter: {
                        type: "none",
                        value: 0,
                    },
                },
            },
            tooltip: {
                style: {
                    fontSize: "12px",
                },
                y: {
                    formatter: function (val) {
                        return +val;
                    },
                },
            },
            colors: [
                KTUtil.getCssVariableValue("--bs-primary"),
                KTUtil.getCssVariableValue("--bs-primary-light"),
            ],
            grid: {
                borderColor: borderColor,
                strokeDashArray: 4,
                yaxis: {
                    lines: {
                        show: true,
                    },
                },
            },
        };

        chart.self = new ApexCharts(element, options);

        // Set timeout to properly get the parent elements width
        setTimeout(function () {
            chart.self.render();
            chart.rendered = true;
        }, 200);
    };

    // Public methods
    return {
        init: function () {
            initChart(chart);

            // Update chart on theme mode change
            KTThemeMode.on("kt.thememode.change", function () {
                if (chart.rendered) {
                    chart.self.destroy();
                }

                initChart(chart);
            });
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    gaugeApplications.init();
    summaryApplications.init();
});