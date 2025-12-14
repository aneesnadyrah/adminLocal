'use strict'

var graphIncome = (function () {
    var chart = {
        self: null,
        rendered: false,
    };

    // Private methods
    var initChart = function (chart) {
        var element = document.getElementById("chart_graph_income");

        if (!element) {
            return;
        }

        var height = parseInt(KTUtil.css(element, "height"));
        var labelColor = KTUtil.getCssVariableValue("--bs-gray-500");
        var borderColor = KTUtil.getCssVariableValue("--bs-border-dashed-color");
        var baseColor = KTUtil.getCssVariableValue("--bs-primary");
        var lightColor = KTUtil.getCssVariableValue("--bs-primary");
        var chartInfo = element.getAttribute("data-kt-chart-info");

        var options = {
            series: [{
                name: chartInfo,
                data: [
                    34.5, 34.5, 35, 35, 35.5, 35.5, 35, 35, 35.5, 35.5, 35, 35, 34.5,
                    34.5, 35, 35, 35.4, 35.4, 35,
                ],
            }, ],
            chart: {
                fontFamily: "inherit",
                type: "area",
                height: height,
                toolbar: {
                    show: false,
                },
            },
            plotOptions: {},
            legend: {
                show: false,
            },
            dataLabels: {
                enabled: false,
            },
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0,
                    stops: [0, 80, 100],
                },
            },
            stroke: {
                curve: "smooth",
                show: true,
                width: 3,
                colors: [baseColor],
            },
            xaxis: {
                categories: [
                    "",
                    "Feb 02",
                    "Feb 03",
                    "Feb 04",
                    "Feb 05",
                    "Feb 06",
                    "Feb 07",
                    "Feb 08",
                    "Feb 09",
                    "Feb 10",
                    "Feb 11",
                    "Feb 12",
                    "Feb 13",
                    "Feb 14",
                    "Feb 17",
                    "Feb 18",
                    "Feb 19",
                    "Feb 21",
                    "",
                ],
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
                tickAmount: 6,
                labels: {
                    rotate: 0,
                    rotateAlways: true,
                    style: {
                        colors: labelColor,
                        fontSize: "12px",
                    },
                },
                crosshairs: {
                    position: "front",
                    stroke: {
                        color: baseColor,
                        width: 1,
                        dashArray: 3,
                    },
                },
                tooltip: {
                    enabled: true,
                    formatter: undefined,
                    offsetY: 0,
                    style: {
                        fontSize: "12px",
                    },
                },
            },
            yaxis: {
                max: 36.3,
                min: 33,
                tickAmount: 6,
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: "12px",
                    },
                    formatter: function (val) {
                        return "RM " + parseInt(10 * val);
                    },
                },
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
                        return "RM " + parseInt(10 * val);
                    },
                },
            },
            colors: [lightColor],
            grid: {
                borderColor: borderColor,
                strokeDashArray: 4,
                yaxis: {
                    lines: {
                        show: true,
                    },
                },
            },
            markers: {
                strokeColor: baseColor,
                strokeWidth: 3,
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

// Class definition
var collectionsGauge = (function () {
    // Private methods
    var yearlyCollections = function () {
        // Check if amchart library is included
        if (typeof am5 === "undefined") {
            return;
        }

        var element = document.getElementById("yearly-collections");

        if (!element) {
            return;
        }

        var root;

        var init = function () {
            // Create root element
            // https://www.amcharts.com/docs/v5/getting-started/#Root_element
            root = am5.Root.new(element);

            root._logo.dispose();

            // Set themes
            // https://www.amcharts.com/docs/v5/concepts/themes/
            root.setThemes([am5themes_Animated.new(root)]);

            // Create chart
            // https://www.amcharts.com/docs/v5/charts/radar-chart/
            var chart = root.container.children.push(
                am5radar.RadarChart.new(root, {
                    panX: false,
                    panY: false,
                    wheelX: "panX",
                    wheelY: "zoomX",
                    innerRadius: am5.percent(20),
                    startAngle: -90,
                    endAngle: 180,
                })
            );



            // Data
            var data = [{
                    category: "Power",
                    value: 80,
                    full: 100,
                    columnSettings: {
                        fillOpacity: 1,
                        fill: am5.color(KTUtil.getCssVariableValue("--bs-danger")),
                    },
                },

                {
                    category: "Telco",
                    value: 92,
                    full: 100,
                    columnSettings: {
                        fillOpacity: 1,
                        fill: am5.color('#fc7703'),
                    },
                },
                {
                    category: "Water",
                    value: 68,
                    full: 100,
                    columnSettings: {
                        fillOpacity: 1,
                        fill: am5.color(KTUtil.getCssVariableValue("--bs-primary")),
                    },
                },
                {
                    category: "Gas",
                    value: 35,
                    full: 100,
                    columnSettings: {
                        fillOpacity: 1,
                        fill: am5.color(KTUtil.getCssVariableValue("--bs-warning")),
                    },
                },
                {
                    category: "Sewerage",
                    value: 68,
                    full: 100,
                    columnSettings: {
                        fillOpacity: 1,
                        fill: am5.color(KTUtil.getCssVariableValue("--bs-success")),
                    },
                }
            ];

            // Add cursor
            // https://www.amcharts.com/docs/v5/charts/radar-chart/#Cursor
            var cursor = chart.set(
                "cursor",
                am5radar.RadarCursor.new(root, {
                    behavior: "zoomX",
                })
            );

            cursor.lineY.set("visible", false);

            // Create axes and their renderers
            // https://www.amcharts.com/docs/v5/charts/radar-chart/#Adding_axes
            var xRenderer = am5radar.AxisRendererCircular.new(root, {
                //minGridDistance: 50
            });

            xRenderer.labels.template.setAll({
                radius: 10,
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
                fill: am5.color(KTUtil.getCssVariableValue("--bs-gray-500")),
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
                fill: am5.color(KTUtil.getCssVariableValue("--bs-gray-500")),
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
            // https://www.amcharts.com/docs/v5/charts/radar-chart/#Adding_series
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
            // https://www.amcharts.com/docs/v5/concepts/animations/#Initial_animation
            series1.appear(1000);
            series2.appear(1000);
            chart.appear(1000, 100);
        };

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

    var monthlyCollections = function () {
        // Check if amchart library is included
        if (typeof am5 === "undefined") {
            return;
        }

        var element = document.getElementById("monthly-collections");

        var root;

        if (!element) {
            return;
        }

        var init = function () {
            // Create root element
            // https://www.amcharts.com/docs/v5/getting-started/#Root_element
            root = am5.Root.new(element);

            root._logo.dispose();

            // Set themes
            // https://www.amcharts.com/docs/v5/concepts/themes/
            root.setThemes([am5themes_Animated.new(root)]);

            // Create chart
            // https://www.amcharts.com/docs/v5/charts/radar-chart/
            var chart = root.container.children.push(
                am5radar.RadarChart.new(root, {
                    panX: false,
                    panY: false,
                    wheelX: "panX",
                    wheelY: "zoomX",
                    innerRadius: am5.percent(20),
                    startAngle: -90,
                    endAngle: 180,
                })
            );

            // Data
            var data = [{
                    category: "Power",
                    value: 50,
                    full: 100,
                    columnSettings: {
                        fillOpacity: 1,
                        fill: am5.color(KTUtil.getCssVariableValue("--bs-danger")),
                    },
                },

                {
                    category: "Telco",
                    value: 60,
                    full: 100,
                    columnSettings: {
                        fillOpacity: 1,
                        fill: am5.color('#fc7703'),
                    },
                },
                {
                    category: "Water",
                    value: 68,
                    full: 100,
                    columnSettings: {
                        fillOpacity: 1,
                        fill: am5.color(KTUtil.getCssVariableValue("--bs-primary")),
                    },
                },
                {
                    category: "Gas",
                    value: 75,
                    full: 100,
                    columnSettings: {
                        fillOpacity: 1,
                        fill: am5.color(KTUtil.getCssVariableValue("--bs-warning")),
                    },
                },
                {
                    category: "Sewerage",
                    value: 18,
                    full: 100,
                    columnSettings: {
                        fillOpacity: 1,
                        fill: am5.color(KTUtil.getCssVariableValue("--bs-success")),
                    },
                }
            ];

            // Add cursor
            // https://www.amcharts.com/docs/v5/charts/radar-chart/#Cursor
            var cursor = chart.set(
                "cursor",
                am5radar.RadarCursor.new(root, {
                    behavior: "zoomX",
                })
            );

            cursor.lineY.set("visible", false);

            // Create axes and their renderers
            // https://www.amcharts.com/docs/v5/charts/radar-chart/#Adding_axes
            var xRenderer = am5radar.AxisRendererCircular.new(root, {
                //minGridDistance: 50
            });

            xRenderer.labels.template.setAll({
                radius: 10,
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

            var yRenderer = am5radar.AxisRendererRadial.new(root, {
                minGridDistance: 20,
            });

            yRenderer.labels.template.setAll({
                centerX: am5.p100,
                fontWeight: "500",
                fontSize: 18,
                fill: am5.color(KTUtil.getCssVariableValue("--bs-gray-500")),
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
            // https://www.amcharts.com/docs/v5/charts/radar-chart/#Adding_series
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
            // https://www.amcharts.com/docs/v5/concepts/animations/#Initial_animation
            series1.appear(1000);
            series2.appear(1000);
            chart.appear(1000, 100);
        };

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
            yearlyCollections();
            monthlyCollections();
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    graphIncome.init();
    collectionsGauge.init();
});