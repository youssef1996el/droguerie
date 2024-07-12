$(document).ready(function () {
    $(function ()
    {
        initializeDataTable('.TableTva', getTva);
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
                columns:
                [
                    {data: 'tva'        , name: 'tva'},
                    {data: 'title'      , name: 'title'},
                    {data: 'name'       , name: 'name'},
                    {data: 'date_creer' , name: 'date_creer'},
                    {data: 'action'     , name: 'action', orderable: false, searchable: false}
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
                var idtva   = $(this).attr('value');
                var data = table.row($(this).closest('tr')).data();
                var tvaValue = data.tva.replace(/[^\d]/g, ''); // Remove non-digit characters

                $('#EditTva').modal('show');
                $('#tvaEdit').val(tvaValue);
                $('#BtnEditTva').attr('data-value',data.id);

            });


            $(selector + ' tbody').on('click', '.trash', function(e)
            {
                e.preventDefault();
                var idTva = $(this).attr('value');


                swal({
                    title: "es-tu sûr de supprimer cette TVA",
                    text: "Une fois supprimée, vous ne pourrez plus récupérer cette TVA !",
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
                            'id' : idTva,
                            '_token'        : csrf_token,
                        };
                        $.ajax({
                            type: "post",
                            url: trashTva,
                            data: data,
                            dataType: "json",
                            success: function (response) {
                                if(response.status == 200)
                                {
                                    swal(response.message, {
                                        icon: "success",
                                    });

                                    $('.TableTva').DataTable().ajax.reload();
                                }
                            }
                        });



                    }
                    else
                    {
                        swal("Votre tva est sécurisée !");
                    }
                });

            });


        }
    });
    $('#BtnSaveTva').on('click',function(e)
    {
        var data =
        {
            'name' : $('#tva').val(),
            '_token'        : csrf_token,
        };

        $.ajax({
            type: "post",
            url: StoreTva,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationTva').html("");
                    $('#AddTva').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableTva').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationTva').html("");
                    $('.ValidationTva').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationTva').append('<li>'+list_err+'</li>');
                    });
                }
                else if(response.status == 450)
                {
                    toastr.error(response.message, 'Error');
                    $('#tva').val("");
                }
            }
            ,
            error: function (xhr, status, error) {
                toastr.error('Failed to process request: ' + error, 'Error');
            }
        });
    });

    $('#BtnEditTva').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'name'          : $('#tvaEdit').val(),
            '_token'        : csrf_token,
            'id'            : $(this).attr('data-value'),
        };

        $.ajax({
            type: "post",
            url: UpdateTva,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationTvaEdit').html("");
                    $('#EditTva').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableTva').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationTvaEdit').html("");
                    $('.ValidationTvaEdit').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationTvaEdit').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });
});
