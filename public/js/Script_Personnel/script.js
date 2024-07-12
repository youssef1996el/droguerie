$(document).ready(function () {
    $(function ()
    {
        initializeDataTable('.TablePersonnel', getFichePersonnel);
        function initializeDataTable(selector, url)
        {
            var table = $(selector).DataTable({
                processing: true,
                serverSide: true,
                ordering: [[1, 'asc']],
                ajax:
                {
                    url: url,
                    dataSrc: function (json) {
                        if (json.data.length === 0) {
                            $('.paging_full_numbers').css('display', 'none');
                        }
                        return json.data;
                    }
                },
                columns:
                [
                    {data: 'nom'            , name: 'nom'},
                    {data: 'prenom'         , name: 'prenom'},
                    {data: 'adresse'        , name: 'adresse' },
                    {data: 'cin'            , name: 'cin'},
                    {data: 'ville'          , name: 'ville'},
                    {data: 'telephone'      , name: 'telephone'},
                    {data: 'title'          , name: 'title'},
                    {data: 'name'           , name: 'name'},
                    {data: 'date_created'   , name: 'date_created'},
                    {data: 'action'         , name: 'action', orderable: false, searchable: false}
                ],
                language:
                {
                    "sInfo": "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                    "sInfoEmpty": "Affichage de l'élément 0 à 0 sur 0 élément",
                    "sInfoFiltered": "(filtré à partir de _MAX_ éléments au total)",
                    "sInfoPostFix": "",
                    "sInfoThousands": ",",
                    "sLengthMenu": "Afficher _MENU_ éléments",
                    "sLoadingRecords": "Chargement...",
                    "sProcessing": "Traitement...",
                    "sSearch": "Rechercher :",
                    "sZeroRecords": "Aucun élément correspondant trouvé",
                    "oPaginate": {
                        "sFirst": "Premier",
                        "sLast": "Dernier",
                        "sNext": "Suivant",
                        "sPrevious": "Précédent"
                    },
                    "oAria": {
                        "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                        "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
                    },
                    "select": {
                        "rows": {
                            "_": "%d lignes sélectionnées",
                            "0": "Aucune ligne sélectionnée",
                            "1": "1 ligne sélectionnée"
                        }
                    }
                }

            });
            $(selector + " tbody").on('click', '.edit', function(e) {
                e.preventDefault();
                var data = table.row($(this).closest('tr')).data(); // Access DataTables API to get row data
                $('#nomEdit').val(data.nom);
                $('#prenomEdit').val(data.prenom);
                $('#adresseEdit').val(data.adresse);
                $('#cinEdit').val(data.cin);
                $('#villeEdit').val(data.ville).change();
                $('#phoneEdit').val(data.telephone); // corrected typo here ('tlephone' to 'telephone')
                $('#BtnUpdatePersonnel').attr('data-value', data.id);
                $('#EditPersonnel').modal("show");
            });



        }
    });
    $('#BtnShowModalAddPersonnel').on('click',function(e)
    {
        e.preventDefault();
        $('#AddPersonnel').modal('show');
    });

    $('#BtnSavePersonnel').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'nom'        : $('#nom').val(),
            'prenom'     : $('#prenom').val(),
            'adresse'    : $('#adresse').val(),
            'cin'        : $('#cin').val(),
            'ville'      : $('#ville').val(),
            'telephone'  : $('#phone').val(),
            '_token'     : csrf_token,
        };
        $.ajax({
            type: "post",
            url: StorePersonnel,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationPersonnel').html("");
                    $('#AddPersonnel').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TablePersonnel').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationPersonnel').html("");
                    $('.ValidationPersonnel').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationPersonnel').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });
    $('#BtnUpdatePersonnel').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'nom'        : $('#nomEdit').val(),
            'prenom'     : $('#prenomEdit').val(),
            'adresse'    : $('#adresseEdit').val(),
            'cin'        : $('#cinEdit').val(),
            'ville'      : $('#villeEdit').val(),
            'telephone'  : $('#phoneEdit').val(),
            '_token'     : csrf_token,
            'id'         : $(this).attr('data-value'),
        };
        $.ajax({
            type: "post",
            url: UpdatePersonnel,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationPersonnelEdit').html("");
                    $('#EditPersonnel').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TablePersonnel').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationPersonnelEdit').html("");
                    $('.ValidationPersonnelEdit').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationPersonnelEdit').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });

    $('#BtnShowModalAddPersonnelPaiement').on('click',function(e)
    {
        e.preventDefault();
        $('#AddPersonnelPaiement').modal('show');
    });

    $('#BtnSavePersonnelPaiement').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'idpersonnel': $('#PersonnelPaiement').val(),
            'total'      : $('#total').val(),
            '_token'     : csrf_token,
        };
        $.ajax({
            type: "post",
            url: StorePaiementPersonnel,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationPersonnelPaiement').html("");
                    $('#AddPersonnelPaiement').modal("hide");
                    toastr.success(response.message, 'Success');

                }
                else if(response.status == 422)
                {
                    $('.ValidationPersonnelPaiement').html("");
                    $('.ValidationPersonnelPaiement').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationPersonnelPaiement').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });
});
