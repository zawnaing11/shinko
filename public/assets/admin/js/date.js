// datepicker
$('.date_format').datepicker({
    language: 'ja',
    autoClose: true,
    dateFormat: 'yyyy-mm-dd',
});

$('.time_format').datepicker({
    language: 'ja',
    dateFormat: 'yyyy-mm-dd',
    timeFormat: 'hh:ii',
    timepicker: true,
    dateTimeSeparator: ' '
});
