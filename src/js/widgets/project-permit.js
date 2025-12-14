"use strict";

// TODO: Consider changing to apexchart radial chart since this one generated using html5 got pixelate when displayed
// Class definition
var taskOverviewHead = (function () {
    // Private methods
    var initChart = function () {
        var el = document.getElementsByName("task-overview-head");

        if (!el) {
            return;
        }

        el.forEach((el) => {
            // Perform a function on each element
            var options = {
                size: el.getAttribute("data-kt-size") ?
                    parseInt(el.getAttribute("data-kt-size")) :
                    70,
                lineWidth: el.getAttribute("data-kt-line") ?
                    parseInt(el.getAttribute("data-kt-line")) :
                    11,
                rotate: el.getAttribute("data-kt-rotate") ?
                    parseInt(el.getAttribute("data-kt-rotate")) :
                    145,
                color: (el.getAttribute("data-kt-progress-color") != "") ?
                    el.getAttribute("data-kt-progress-color") :
                    '--bs-success',
                //percent:  el.getAttribute('data-kt-percent') ,
            };

            var canvas = document.createElement("canvas");
            var span = document.createElement("span");

            if (typeof G_vmlCanvasManager !== "undefined") {
                G_vmlCanvasManager.initElement(canvas);
            }

            var ctx = canvas.getContext("2d");
            canvas.width = canvas.height = options.size;

            el.appendChild(span);
            el.appendChild(canvas);

            ctx.translate(options.size / 2, options.size / 2); // change center
            ctx.rotate((-1 / 2 + options.rotate / 180) * Math.PI); // rotate -90 deg

            //imd = ctx.getImageData(0, 0, 240, 240);
            var radius = (options.size - options.lineWidth) / 2;

            var drawCircle = function (color, lineWidth, percent) {
                percent = Math.min(Math.max(0, percent || 1), 1);
                ctx.beginPath();
                ctx.arc(0, 0, radius, 0, Math.PI * 2 * percent, false);
                ctx.strokeStyle = color;
                ctx.lineCap = "round"; // butt, round or square
                ctx.lineWidth = lineWidth;
                ctx.stroke();
            };

            // Init
            // TODO: Get the data from the html or api
            var widgetCard = el.parentElement.parentElement.parentElement;
            var headTotal = widgetCard.querySelector('[name="head-total"]').innerHTML;
            var countSuccess = widgetCard.querySelector('[name="count-success"]').innerHTML;
            widgetCard.querySelector('[name="count-open"]').innerHTML = headTotal - countSuccess;
            var percentSuccess = countSuccess / headTotal * 100;
            var headPercent = widgetCard.querySelector('[name="head-percent"]');

            headPercent.innerHTML = percentSuccess.toFixed(0) + '%';

            drawCircle("#E4E6EF", options.lineWidth, 1);
            drawCircle(
                KTUtil.getCssVariableValue(options.color),
                options.lineWidth,
                percentSuccess / 100
            );
        });
    };

    // Public methods
    return {
        init: function () {
            initChart();
        },
    };
})();

// Webpack support
if (typeof module !== "undefined") {
    module.exports = taskOverviewHead;
}

// On document ready
KTUtil.onDOMContentLoaded(function () {
    taskOverviewHead.init();
});