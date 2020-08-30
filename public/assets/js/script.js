$(document).ready(function() {

    $('select[name="monselect"]').change(function () {
        var id = "ma_table_" + $(this).val();
        $('table').hide();

        if(id === "ma_table_Mandats")
        {
            $(".tablesVente").hide()
        }

        if(id != 'ma_table_Mandats')
        {
            $(".tablesVente").show()
        }

        $('#' + id).show();
    });

    $('#popup').click(function(){
        $('[data-toggle="popover"]').popover()
    })

    $('select[name="filtre"]').change(function () {

        var id = "ma_table_" + $(this).val();

        if(id === 'ma_table_Collaborateurs')
        {
            $('#ma_table_Consolidations').hide();
            $('#ma_table_Sites').hide();
        }
        else if(id === 'ma_table_Sites')
        {
            $('#ma_table_Collaborateurs').hide();
            $('#ma_table_Consolidations').hide();
        }
        else if(id === 'ma_table_Consolidations')
        {
            $('#ma_table_Collaborateurs').hide();
            $('#ma_table_Sites').hide();
        }


        $('#' + id).show();
    });



});
