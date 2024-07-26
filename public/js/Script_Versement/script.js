$(document).ready(function () {
    $(function ()
    {
        initializeDataTable('.TableVersement', versement);
        function initializeDataTable(selector, url)
        {
            var tableVersement = $(selector).DataTable({
                processing: true,
                serverSide: true,
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

                    {data: 'comptable'             , name: 'comptable'},
                    {
                        data: 'total',
                        name: 'total',
                        render: function (data, type, row) {
                            return data + ' DH';
                        },
                        className: "dt-right"
                    },
                    {data: 'title'        , name: 'title'},
                    {data: 'user'           , name: 'user'},
                    {data: 'created_at_formatted'     , name: 'created_at_formatted'},
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
                var IdVersement     = $(this).attr('value');
                var name            = $(this).closest('tr').find('td:eq(0)').text();
                var data            = tableVersement.row($(this).closest('tr')).data();
                var totalString     = data.total.replace(/[^\d.]/g, ''); // Remove non-digit and non-dot characters
                var totalNumber     = parseFloat(totalString); // Convert cleaned string to a floating-point number
                var totalFloat      = totalNumber.toFixed(2);
                $('#comptableEdit').val(name);
                $('#totalEdit').val(totalFloat);
                $('#BtnEditVersement').attr('data-value',IdVersement);
                $('#ModelVersementEdit').modal("show");

            });

            $(selector + ' tbody').on('click', '.trash', function(e)
            {
                e.preventDefault();
                var IdVersement  = $(this).attr('value');
                swal({
                    title: "es-tu sûr de supprimer cette versement",
                    text: "Une fois supprimée, vous ne pourrez plus récupérer cette versement !",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                  })
                  .then((willDelete) => {
                    if (willDelete)
                    {
                        var data =
                        {
                            'id'         : IdVersement,
                            '_token'     : csrf_token,
                        };
                        $.ajax({
                            type: "post",
                            url: TrashVersement,
                            data: data,

                            dataType: "json",
                            success: function (response)
                            {
                                if(response.status == 200)
                                {
                                    swal("Votre versement a été supprimée !", {
                                        icon: "success",
                                    });
                                    $('.TableVersement').DataTable().ajax.reload();
                                }
                                else if(response.status ==400)
                                {
                                    swal("Oops !", response.message, "error");
                                }
                                else if(response.status ==404)
                                {
                                    swal("Oops !", response.message, "error");
                                }
                            }
                        });

                    }
                    else
                    {
                        swal("Votre charge est sécurisée !");
                    }
                  });

            });

        }
    });
    $('#BtnSaveVersement').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'comptable'  : $('#Comptable').val(),
            'total'      : $('#total').val(),
            '_token'        : csrf_token,
        };
        $.ajax({
            type: "post",
            url: StoreVersement,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('#total').val("");
                    $('#Comptable').val("");
                    $('.ValidationVersement').html("");
                    $('#ModelVersement').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableVersement').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationVersement').html("");
                    $('.ValidationVersement').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationVersement').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });

    $('#BtnEditVersement').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'comptable'     : $('#comptableEdit').val(),
            '_token'        : csrf_token,
            'total'         : $('#totalEdit').val(),
            'id'            : $(this).attr('data-value'),

        };
        $.ajax({
            type: "post",
            url: updateVersement,
            data: data,
            dataType: "json",
            success: function (response)
            {

                if(response.status == 200)
                {
                    $('#totalEdit').val("");
                    $('#comptableEdit').val("");

                    $('.ValidationVersementEdit').html("");
                    $('#ModelVersementEdit').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableVersement').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationVersementEdit').html("");
                    $('.ValidationVersementEdit').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationVersementEdit').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });
});
