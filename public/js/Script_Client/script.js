$(document).ready(function ()
{
    $(document).ready(function () {
        $('#BtnShowModalAddClient').on('click', function(e)
        {
            e.preventDefault();
            $('#AddClient').modal("show");
        });
        $(function ()
        {
            initializeDataTable('.TableClient', FicheClient);
            function initializeDataTable(selector, url)
            {
                var table = $(selector).DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: [[1, 'asc']],
                    ajax: {
                        url: url,
                        dataSrc: function (json) {
                            if (json.data.length === 0) {
                                $('.paging_full_numbers').css('display', 'none');
                            }
                            return json.data;
                        }
                    },
                    columns: [
                        {data: 'nom', name: 'nom'},
                        {data: 'prenom', name: 'prenom'},
                        {data: 'cin', name: 'cin'},
                        {data: 'adresse', name: 'adresse' },
                        {data: 'ville', name: 'ville'},
                        {data: 'phone', name: 'phone'},
                        {
                            data: 'plafonnier',
                            name: 'plafonnier',
                            render: function (data, type, row) {
                                // Assuming you want to concatenate 'plafonnier' with some string
                                // Replace 'Some text' with the actual string you want to concatenate
                                return data + ' DH';
                            }
                        },
                        {data: 'title', name: 'title'},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    language: {
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
                    $('#cinEdit').val(data.cin);
                    $('#adresseEdit').val(data.adresse);
                    $('#villeEdit').val(data.ville).change();
                    $('#phoneEdit').val(data.phone); // corrected typo here ('tlephone' to 'telephone')
                    $('#plafonnierEdit').val(data.plafonnier);
                    $('#BtnUpdateClient').attr('data-value', data.id);
                    $('#EditClient').modal("show");
                });

                $(selector + " tbody").on('click', '.trash', function(e) {
                    e.preventDefault();
                    var data = table.row($(this).closest('tr')).data(); // Access DataTables API to get row data
                    var id = data.id;
                    swal({
                        title: "es-tu sûr de supprimer cette client",
                        text: "Une fois supprimée, vous ne pourrez plus récupérer cette client !",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) =>
                    {
                        if (willDelete)
                        {
                            var data =
                            {
                                'id'         : id,
                                '_token'     : csrf_token,
                            };
                            $.ajax({
                                type: "post",
                                url: TrashClient,
                                data: data,

                                dataType: "json",
                                success: function (response)
                                {
                                    if(response.status == 200)
                                    {
                                        swal("Votre detail client a été supprimée !", {
                                            icon: "success",
                                        });
                                        $('.TableClient').DataTable().ajax.reload();
                                    }
                                    else if(response.status == 442)
                                    {

                                        toastr.error('Un client ayant déjà une commande ne peut pas être supprimé', 'Error');
                                        return false;
                                    }

                                }
                            });

                        }
                        else
                        {
                            swal("Votre detail catégorie est sécurisée !");
                        }
                    });
                });
            }

        });

    });

    $('#BtnSaveClient').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'nom'        : $('#nom').val(),
            'prenom'     : $('#prenom').val(),
            'cin'        : $('#cin').val(),
            'adresse'    : $('#adresse').val(),
            'ville'      : $('#ville').val(),
            'plafonnier' : $('#plafonnier').val(),
            'phone'      : $('#phone').val(),
            '_token'     : csrf_token,
        };

        $.ajax({
            type: "post",
            url: StoreClient,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationClient').html("");
                    $('#AddClient').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableClient').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationClient').html("");
                    $('.ValidationClient').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationClient').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });

    $('#BtnUpdateClient').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'nom'        : $('#nomEdit').val(),
            'prenom'     : $('#prenomEdit').val(),
            'cin'        : $('#cinEdit').val(),
            'adresse'    : $('#adresseEdit').val(),
            'ville'      : $('#villeEdit').val(),
            'plafonnier' : $('#plafonnierEdit').val(),
            'phone'      : $('#phoneEdit').val(),
            '_token'     : csrf_token,
            'id'         : $(this).attr('data-value'),
        };
        $.ajax({
            type: "post",
            url: UpdateClient,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationClientEdit').html("");
                    $('#EditClient').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableClient').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationClientEdit').html("");
                    $('.ValidationClientEdit').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationClientEdit').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });

});
