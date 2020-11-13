$(document).ready(function () {
    $('[id^=chart]').show();
    var i = 1;
    chartSwitch(i);
    
    $('#chartNext').on('click', function () {
        i++;
        if (7 === i) {
            i = 1;
        }
        chartSwitch(i);
    });

    $('#chartPrevious').on('click', function () {
        i--;
        if (0 === i) {
            i = 6;
        }
        chartSwitch(i);
    });
    
    function chartSwitch(i) {
        $('#chart' + i).show();
        for (j = 1; j <= 6; j++) {
            if (j !== i) {
                $('#chart' + j).hide();
            }
        }
    }    
});
