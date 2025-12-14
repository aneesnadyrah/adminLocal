// Add custom sorting for date format DD/MM/YYYY
jQuery.fn.dataTable.ext.type.order['date-dd-mm-yyyy-pre'] = function(date) {
    // Regular expression to match dates in the format DD/MM/YYYY
    var datePattern = /\b(\d{2})\/(\d{2})\/(\d{4})\b/;
    var match = date.match(datePattern);

    if (match) {
        // If a valid date is found, create a new Date object
        var day = parseInt(match[1], 10);
        var month = parseInt(match[2], 10) - 1; // Month is 0-based in JavaScript
        var year = parseInt(match[3], 10);
        return new Date(year, month, day).getTime();
    } else {
        // If no valid date is found, return a high value to push it to the end
        return Infinity;
    }
};
