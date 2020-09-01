// Ajax sur la partie tableau de bord

$('#poplight3').click(function(e){
    e.preventDefault();

    var mandat = $('input[placeholder="Nombre"]').val();

    $.ajax({
        url: '/tableau-de-bord/add-mandat',
        type: 'POST',
        data: {
            mandat: mandat
        },
        success: function (data) {
            $('.nombre_mandat').html(data.response);

            $('form').find('input').val("");

            if(data.message === "Erreur dans l'ajout de mandat")
            {
                toastr.error(data.message)
            }
            else
            {
                toastr.success(data.message)
            }


        },
        error: function (jqxhr) {
            alert(jqxhr.responseText);
        }
    });
});

$('#js-add').click(function(e){
    e.preventDefault();

    var date_vente = $('input[placeholder="01/01/1900"]').val();
    var immatriculation = $('input[placeholder="AA123BB"]').val();
    var livree = $('input[name="vente[livree]"]').is(":checked") ? 1 : 0;
    var fraisMER = $('input[name="vente[frais_mer]"]').is(":checked") ? 1 : 0;
    var garantie = $('input[name="vente[garantie]"]').is(":checked") ? 1 : 0;
    var financement = $('input[name="vente[financement]"]').is(":checked") ? 1 : 0;

    $.ajax({
        url: '/tableau-de-bord/add-vente',
        type: 'POST',
        data: {
            vente: date_vente,
            immatriculation: immatriculation,
            livree: livree,
            fraisMER: fraisMER,
            garantie: garantie,
            financement: financement
        },
        success: function (data) {

            if(data.errors !== undefined)
            {
                for(const error in data.errors)
                {
                    toastr.error(data.errors[error],{
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "7000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    });
                }

                $('#fade , .popup_block').fadeOut(function() {
                    $('#fade, a.close').remove();  //...ils disparaissent ensemble
                });

                $('input[placeholder="01/01/1900"]').val("")
                $('input[placeholder="AA123BB"]').val("");
                $('input[type="checkbox"]').attr('checked', false);
            }
            else
            {
                $('#mesventes').html(data.vente);
                $('#bouton1').html(data.livree);
                $('#bouton2').html(data.fraisMER);
                $('#bouton3').html(data.garanties);
                $('#bouton4').html(data.financement);

                toastr.success(data.message)

                $('input[placeholder="01/01/1900"]').val("")
                $('input[placeholder="AA123BB"]').val("");
                $('input[type="checkbox"]').attr('checked', false);
            }
        },
        error: function (jqxhr) {
            console.log(jqxhr.responseText);
        }
    });
});

// Ajax sur la partie historique

$('a.js-delete').click(function(e){

    e.preventDefault();

    var $a = $(this)

    var url = $a.attr('href')

    $.ajax({
        url : url,
        type : 'GET',
        success: function()
        {
            $a.parents('tr').fadeOut(500);
        },
        error: function(jqxhr)
        {
            console.log(jqxhr.responseText);
        }
    });
});

$('select[name="mois"]').change(function(e) {

    e.preventDefault();

    var mois = $("select[name='mois'] option:selected").attr("id");

    var select = $("select[name='monselect']").val();

    var tableVente = $("#ma_table_Ventes");
    var tableMandat = $("#ma_table_Mandats");

    if(select === "Ventes")
    {
        $.ajax({
            url: '/historique-vente',
            type: 'POST',
            data: {
                mois: mois
            },
            success: function (data) {
                tableVente.html(data);
            },
            error: function (jqxhr) {
                console.log(jqxhr.responseText);
            }
        });

    }else if(select === "Mandats")
    {
        $.ajax({
            url: '/historique-mandat',
            type: 'POST',
            data: {
                mois: mois
            },
            success: function (data) {
                tableMandat.html(data);
            },
            error: function (jqxhr) {
                console.log(jqxhr.responseText);
            }
        });
    }
});

// Ajax partie backoffice

$('select[name="mois-admin"]').change(function(e) {

    e.preventDefault();
    var mois = $("select[name='mois-admin'] option:selected").attr("id");
    var table = $('#ma_table_Collaborateurs');


    if(mois === 'periode')
    {
        var url_vente = '/export-csv/vente';
        var url_mandat = '/export-csv/mandat';
    }
    else
    {
        var url_vente = '/export-csv/vente/' + mois;
        var url_mandat = '/export-csv/mandat/' + mois;
    }

    $.ajax({
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        url : '/admin',
        type : 'POST',
        data : {
            mois : mois
        },
        success: function(data)
        {
            table.html(data);
            $('#lien').attr('href',url_vente);
            $('#lien_mandat').attr('href',url_mandat);
        },
        error: function(jqxhr)
        {
            alert(jqxhr.responseText);
        }
    });


});

$('select[name="trimestre-admin"]').change(function(e) {

    e.preventDefault();
    var trimestre = $("select[name='trimestre-admin'] option:selected").attr("id");
    var table = $('#ma_table_Collaborateurs');


    if(trimestre === 'periode')
    {
        var url_vente = '/export-csv/vente';
        var url_mandat = '/export-csv/mandat';
    }
    else
    {
        var url_vente = '/export-csv/vente/trimestre/' + trimestre;
        var url_mandat = '/export-csv/mandat/trimestre/' + trimestre;
    }

    $.ajax({
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        url : '/admin',
        type : 'POST',
        data : {
            trimestre : trimestre
        },
        success: function(data)
        {
            table.html(data);
            $('#lien').attr('href',url_vente);
            $('#lien_mandat').attr('href',url_mandat);
        },
        error: function(jqxhr)
        {
            alert(jqxhr.responseText);
        }
    });
});

// Ajax partie Miscellaneous

$('#js-add-divers').click(function (e){
    e.preventDefault();

    var mandat = $('input[name="mandat"]').val();
    var vente = $('input[name="vente"]').val();
    var collaborateur = $('select[name="name"]').val();

    jQuery.ajax({
        url: '/admin/miscellaneous/add',
        type: 'POST',
        data: {
            mandat: mandat,
            vente: vente,
            name: collaborateur
        },
        success: function (data) {
            toastr.success("Données à jour")
        },
        error: function (jqxhr) {
            alert(jqxhr.responseText);
        }
    });

});