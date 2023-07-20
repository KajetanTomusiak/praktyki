document.addEventListener('DOMContentLoaded', function() {
    var tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    var tooltipInstances = [];

    tooltips.forEach(function(tooltip) {
        tooltipInstances.push(new bootstrap.Tooltip(tooltip));
    });
});