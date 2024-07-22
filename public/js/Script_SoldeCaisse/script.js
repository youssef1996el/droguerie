$(document).ready(function () {
    $(function ()
    {
        initializeDataTable('.TableSolde', getSoldeCaisse);
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
                    {data: 'total'     , name: 'total'},
                    {data: 'title'          , name: 'title'},
                    {data: 'name'           , name: 'name'},
                    {data: 'name'           , name: 'name'},
                    {data: 'created'       , name: 'created'},
                    {data: 'action'         , name: 'action', orderable: false, searchable: false}
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
                var data = table.row($(this).closest('tr')).data();
                $('#BtnShowModalUpdateSolde').modal("show");
                $('#BtnUpdateSolde').attr('data-value',data.id);
                $('#totalEdit').val(data.total);
            });


            $(selector + ' tbody').on('click', '.trash', function(e)
            {
                e.preventDefault();
                var id   = $(this).attr('value');
                swal({
                        title: "es-tu sûr de supprimer cette solde de caisse",
                        text: "Une fois supprimée, vous ne pourrez plus récupérer cette solde de caisse !",
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
                                url: TrashSoldeCaisse,
                                data: data,

                                dataType: "json",
                                success: function (response)
                                {
                                    if(response.status == 200)
                                    {
                                        swal("Votre solde de caisse a été supprimée !", {
                                            icon: "success",
                                        });
                                        $('.TableSolde').DataTable().ajax.reload();
                                    }

                                }
                            });

                        }
                        else
                        {
                            swal("Votre solde de caisse est sécurisée !");
                        }
                    });

                });



        }



    });
    ////////////
    $('#BtnSaveSolde').on('click',function(e)
    {
        e.preventDefault();
        var total = $('#total').val();
        if(total == '')
        {
            $('.ValidationSolde').html("");
            $('.ValidationSolde').addClass('alert alert-danger');
            $('.ValidationSolde').append('<li>Veuillez entrer le total vendu à partir de la caisse</li>');
        }
        else
        {
            $('.ValidationSolde').html("");
            $('.ValidationSolde').removeClass('alert alert-danger');
            var data =
            {
                "total"      : total,
                '_token'     : csrf_token
            };

            $.ajax({
                type: "post",
                url: StoreSoldeCaisse,
                data: data,
                dataType: "json",
                success: function (response)
                {
                    if(response.status == 200)
                    {
                        toastr.success('Solde initial de la caisse créé avec succes ', 'Success');
                        $('.TableSolde').DataTable().ajax.reload();
                        $('#total').val("");
                        $('#BtnShowModalAddSolde').modal('hide');
                    }
                }
            });
        }
    });

    $('#BtnUpdateSolde').on('click',function(e)
    {
        e.preventDefault();
        var total = $('#totalEdit').val();
        if(total == '')
        {
            $('.ValidationSoldeEdit').html("");
            $('.ValidationSoldeEdit').addClass('alert alert-danger');
            $('.ValidationSoldeEdit').append('<li>Veuillez entrer le total vendu à partir de la caisse</li>');
        }
        else
        {
            $('.ValidationSoldeEdit').html("");
            $('.ValidationSoldeEdit').removeClass('alert alert-danger');
            var data =
            {
                "total"      : total,
                '_token'     : csrf_token,
                'id'         : $(this).attr('data-value'),
            };

            $.ajax({
                type: "post",
                url: UpdateSoldeCaisse,
                data: data,
                dataType: "json",
                success: function (response)
                {
                    if(response.status == 200)
                    {
                        toastr.success('Solde initial de la caisse modifier avec succes ', 'Success');
                        $('.TableSolde').DataTable().ajax.reload();
                        $('#totalEdit').val("");
                        $('#BtnShowModalUpdateSolde').modal('hide');
                    }
                }
            });
        }

    });
});
