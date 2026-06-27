(function ($) {
    "use strict";

    $(window).on('load', function() {
        setTimeout(function() {

            //Cashflow Chart
            if (document.getElementById("transactionAnalysis")) {
                var chartCurrency = _currency_symbol;
                const transactionAnalysis = document
                    .getElementById("transactionAnalysis")
                    .getContext("2d");
                var transactionAnalysisChart = new Chart(transactionAnalysis, {
                    type: "line",
                    data: {
                        labels: [],
                        datasets: [
                            {
                                label: $lang_income,
                                data: [],
                                backgroundColor: ["rgba(46, 204, 113, 0.4)"],
                                borderColor: ["rgba(46, 204, 113, 1.0)"],
                                yAxisID: "y",
                                borderWidth: 2,
                                tension: 0.4,
                            },
                            {
                                label: $lang_expense,
                                data: [],
                                backgroundColor: ["rgba(255, 99, 132, 0.4)"],
                                borderColor: ["rgba(255, 99, 132, 1)"],
                                yAxisID: "y",
                                borderWidth: 2,
                                tension: 0.4,
                            },
                            {
                                label: $lang_balance,
                                data: [],
                                backgroundColor: ["rgba(55, 66, 250, 0.4)"],
                                borderColor: ["rgba(55, 66, 250, 1)"],
                                yAxisID: "y",
                                borderWidth: 2,
                                tension: 0.4,
                            },
                        ],
                    },
                    options: {
                        interaction: {
                            mode: "index",
                            intersect: false,
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        stacked: true,
                        scales: {
                            y: {
                                type: "linear",
                                display: true,
                                position: "left",
                                ticks: {
                                    callback: function (value, index, values) {
                                        return chartCurrency + " " + value;
                                    },
                                },
                            },
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: "rectRounded",
                                },
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        var label = context.dataset.label || "";

                                        if (
                                            context.parsed.y !== null &&
                                            context.dataset.yAxisID == "y"
                                        ) {
                                            label +=
                                                ": " +
                                                chartCurrency +
                                                " " +
                                                context.parsed.y;
                                        } else {
                                            label += ": " + context.parsed.y;
                                        }

                                        return label;
                                    },
                                },
                            },
                        },
                    },
                });
            }

            //Income By Category Chart
            if (document.getElementById("incomeOverview")) {
                var link2 = _url + "/dashboard/json_income_by_category";
                $.ajax({
                    url: link2,
                    success: function (data2) {
                        var json2 = JSON.parse(data2);

                        const ctx = document
                            .getElementById("incomeOverview")
                            .getContext("2d");
                        const incomeOverviewChart = new Chart(ctx, {
                            type: "doughnut",
                            data: {
                                labels: json2["category"],
                                datasets: [
                                    {
                                        data: json2["amounts"],
                                        backgroundColor: json2["colors"],
                                    },
                                ],
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        labels: {
                                            usePointStyle: true,
                                            pointStyle: "rectRounded",
                                        },
                                    },
                                    title: {
                                        display: false,
                                        text: $lang_expense_overview,
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function (context) {
                                                return (
                                                    " " +
                                                    context.label +
                                                    ": " +
                                                    _currency_symbol +
                                                    " " +
                                                    context.parsed
                                                );
                                            },
                                        },
                                    },
                                },
                            },
                        });
                    },
                });
            }

            //Expense By Category Chart
            if (document.getElementById("expenseOverview")) {
                var link2 = _url + "/dashboard/json_expense_by_category";
                $.ajax({
                    url: link2,
                    success: function (data2) {
                        var json2 = JSON.parse(data2);

                        const ctx = document
                            .getElementById("expenseOverview")
                            .getContext("2d");
                        const expenseOverviewChart = new Chart(ctx, {
                            type: "doughnut",
                            data: {
                                labels: json2["category"],
                                datasets: [
                                    {
                                        data: json2["amounts"],
                                        backgroundColor: json2["colors"],
                                    },
                                ],
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        labels: {
                                            usePointStyle: true,
                                            pointStyle: "rectRounded",
                                        },
                                    },
                                    title: {
                                        display: false,
                                        text: $lang_expense_overview,
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function (context) {
                                                return (
                                                    " " +
                                                    context.label +
                                                    ": " +
                                                    _currency_symbol +
                                                    " " +
                                                    context.parsed
                                                );
                                            },
                                        },
                                    },
                                },
                            },
                        });
                    },
                });
            }

            if (document.getElementById("transactionAnalysis")) {
                $.ajax({
                    url: _url + "/dashboard/json_cashflow",
                    success: function (data) {
                        var json = JSON.parse(data);

                        transactionAnalysisChart.data.labels = json["month"];
                        transactionAnalysisChart.data.datasets[0].data = [
                            typeof json["deposit"][1] !== "undefined"
                                ? json["deposit"][1]
                                : 0,
                            typeof json["deposit"][2] !== "undefined"
                                ? json["deposit"][2]
                                : 0,
                            typeof json["deposit"][3] !== "undefined"
                                ? json["deposit"][3]
                                : 0,
                            typeof json["deposit"][4] !== "undefined"
                                ? json["deposit"][4]
                                : 0,
                            typeof json["deposit"][5] !== "undefined"
                                ? json["deposit"][5]
                                : 0,
                            typeof json["deposit"][6] !== "undefined"
                                ? json["deposit"][6]
                                : 0,
                            typeof json["deposit"][7] !== "undefined"
                                ? json["deposit"][7]
                                : 0,
                            typeof json["deposit"][8] !== "undefined"
                                ? json["deposit"][8]
                                : 0,
                            typeof json["deposit"][9] !== "undefined"
                                ? json["deposit"][9]
                                : 0,
                            typeof json["deposit"][10] !== "undefined"
                                ? json["deposit"][10]
                                : 0,
                            typeof json["deposit"][11] !== "undefined"
                                ? json["deposit"][11]
                                : 0,
                            typeof json["deposit"][12] !== "undefined"
                                ? json["deposit"][12]
                                : 0,
                        ];
                        transactionAnalysisChart.data.datasets[1].data = [
                            typeof json["withdraw"][1] !== "undefined"
                                ? json["withdraw"][1]
                                : 0,
                            typeof json["withdraw"][2] !== "undefined"
                                ? json["withdraw"][2]
                                : 0,
                            typeof json["withdraw"][3] !== "undefined"
                                ? json["withdraw"][3]
                                : 0,
                            typeof json["withdraw"][4] !== "undefined"
                                ? json["withdraw"][4]
                                : 0,
                            typeof json["withdraw"][5] !== "undefined"
                                ? json["withdraw"][5]
                                : 0,
                            typeof json["withdraw"][6] !== "undefined"
                                ? json["withdraw"][6]
                                : 0,
                            typeof json["withdraw"][7] !== "undefined"
                                ? json["withdraw"][7]
                                : 0,
                            typeof json["withdraw"][8] !== "undefined"
                                ? json["withdraw"][8]
                                : 0,
                            typeof json["withdraw"][9] !== "undefined"
                                ? json["withdraw"][9]
                                : 0,
                            typeof json["withdraw"][10] !== "undefined"
                                ? json["withdraw"][10]
                                : 0,
                            typeof json["withdraw"][11] !== "undefined"
                                ? json["withdraw"][11]
                                : 0,
                            typeof json["withdraw"][12] !== "undefined"
                                ? json["withdraw"][12]
                                : 0,
                        ];
                        transactionAnalysisChart.data.datasets[2].data = [
                            (transactionAnalysisChart.data.datasets[0].data[0] -
                                transactionAnalysisChart.data.datasets[1].data[0]).toFixed(json["decimal_place"]),
                            (transactionAnalysisChart.data.datasets[0].data[1] -
                                transactionAnalysisChart.data.datasets[1].data[1]).toFixed(json["decimal_place"]),
                            (transactionAnalysisChart.data.datasets[0].data[2] -
                                transactionAnalysisChart.data.datasets[1].data[2]).toFixed(json["decimal_place"]),
                            (transactionAnalysisChart.data.datasets[0].data[3] -
                                transactionAnalysisChart.data.datasets[1].data[3]).toFixed(json["decimal_place"]),
                            (transactionAnalysisChart.data.datasets[0].data[4] -
                                transactionAnalysisChart.data.datasets[1].data[4]).toFixed(json["decimal_place"]),
                            (transactionAnalysisChart.data.datasets[0].data[5] -
                                transactionAnalysisChart.data.datasets[1].data[5]).toFixed(json["decimal_place"]),
                            (transactionAnalysisChart.data.datasets[0].data[6] -
                                transactionAnalysisChart.data.datasets[1].data[6]).toFixed(json["decimal_place"]),
                            (transactionAnalysisChart.data.datasets[0].data[7] -
                                transactionAnalysisChart.data.datasets[1].data[7]).toFixed(json["decimal_place"]),
                            (transactionAnalysisChart.data.datasets[0].data[8] -
                                transactionAnalysisChart.data.datasets[1].data[8]).toFixed(json["decimal_place"]),
                            (transactionAnalysisChart.data.datasets[0].data[9] -
                                transactionAnalysisChart.data.datasets[1].data[9]).toFixed(json["decimal_place"]),
                            (transactionAnalysisChart.data.datasets[0].data[10] -
                                transactionAnalysisChart.data.datasets[1].data[10]).toFixed(json["decimal_place"]),
                            (transactionAnalysisChart.data.datasets[0].data[11] -
                                transactionAnalysisChart.data.datasets[1].data[11]).toFixed(json["decimal_place"]),
                        ];
                        transactionAnalysisChart.update();
                    },
                });
            }
            $(".loading-chart").remove();
      }, 2000);
    });
})(jQuery);
