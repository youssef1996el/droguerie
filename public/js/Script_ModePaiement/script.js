$(document).ready(function () {

    $('#BtnShowModalAddModePaiement').on('click',function(e)
    {
        e.preventDefault();
        $('#AddModePaiement').modal("show");

    });


    initializeDataTable('.TableModePaiement', FetchModePaiementByCompanyActive);
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
                {data: 'name', name: 'name'},
                {data: 'title', name: 'title'},
                {data: 'creerpar', name: 'creerpar'},
                {data: 'created_at', name: 'created_at'},


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
        $(selector + ' tbody').on('click', '.edit', function(e)
        {
            e.preventDefault();
            var data            = table.row($(this).closest('tr')).data(); // Access DataTables API to get row data
            var idModePaiement  = $(this).attr('value');
            var name            = data.name;
            $('#Edittitle').val(name);
            $('#BtnUpdateModePaiement').attr('data-value',idModePaiement);
            $('#EditModePaiement').modal("show");

        });

        $(selector + ' tbody').on('click', '.trash', function(e)
        {
            e.preventDefault();
            var idModePaiement   = $(this).attr('value');
            swal({
                    title: "es-tu sûr de supprimer cette mode paiement",
                    text: "Une fois supprimée, vous ne pourrez plus récupérer cette mode paiement !",
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
                            'id'         : idModePaiement,
                            '_token'     : csrf_token,
                        };
                        $.ajax({
                            type: "post",
                            url: TrashModePaiement,
                            data: data,
                            dataType: "json",
                            success: function (response)
                            {
                                if(response.status == 200)
                                {
                                    swal("Votre mode paiement  a été supprimée !", {
                                        icon: "success",
                                    });
                                    $('.TableModePaiement').DataTable().ajax.reload();
                                }

                            }
                        });

                    }
                    else
                    {
                        swal("Votre  mode paiement est sécurisée !");
                    }
                });

            });
        }
        $('#BtnSaveModePaiement').on('click',function(e)
        {
            e.preventDefault();
            var data =
            {
                'name'   :$('#title').val(),
                '_token'     : csrf_token,
            };
            $.ajax({
                type: "post",
                url: StoreModePaiement,
                data: data,
                dataType: "json",
                success: function (response)
                {
                    if(response.status == 200)
                    {
                        $('.ValidationPaiement').html("");
                        $('#AddModePaiement').modal("hide");
                        toastr.success(response.message, 'Success');
                        $('.TableModePaiement').DataTable().ajax.reload();
                    }
                    else if(response.status == 422)
                    {
                        $('.ValidationPaiement').html("");
                        $('.ValidationPaiement').addClass('alert alert-danger');
                        $.each(response.errors, function(key, list_err) {
                            $('.ValidationPaiement').append('<li>'+list_err+'</li>');
                        });
                    }
                }
            });
        });


        $('#BtnUpdateModePaiement').on('click',function(e)
        {
            e.preventDefault();
            var data =
            {
                'name'          : $('#Edittitle').val(),
                '_token'        : csrf_token,
                'id'            : $(this).attr('data-value'),
            };
            $.ajax({
                type: "post",
                url: UpdateModePaiement,
                data: data,
                dataType: "json",
                success: function (response)
                {
                    if(response.status == 200)
                    {
                        $('.ValidationPaiement').html("");
                        $('#EditModePaiement').modal("hide");
                        toastr.success(response.message, 'Success');
                        $('.TableModePaiement').DataTable().ajax.reload();
                    }
                    else if(response.status == 422)
                    {
                        $('.ValidationPaiement').html("");
                        $('.ValidationPaiement').addClass('alert alert-danger');
                        $.each(response.errors, function(key, list_err) {
                            $('.ValidationPaiement').append('<li>'+list_err+'</li>');
                        });
                    }
                }
            });
        });
});


