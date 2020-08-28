$(document).ready(function() {

    $('select[name="monselect"]').change(function () {
        var id = "ma_table_" + $(this).val();
        $('table').hide();
        $('#' + id).show();
    });

    $('#popup').click(function(){
        $('[data-toggle="popover"]').popover()
    })

    $('select[name="filtre"]').change(function () {

        var id = "ma_table_" + $(this).val();
        $('.mytables').hide();
        $('#' + id).show();
    });



});
