$(document).ready(function () {
    $(function ()
    {
        initializeDataTable('.TableCharge', Charge);
        function initializeDataTable(selector, url)
        {
            var tablecharge = $(selector).DataTable({
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

                    {data: 'name'             , name: 'name'},
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
                var Idcharge   = $(this).attr('value');
                var name         = $(this).closest('tr').find('td:eq(0)').text();
                var data = tablecharge.row($(this).closest('tr')).data();
                var totalString = data.total.replace(/[^\d.]/g, ''); // Remove non-digit and non-dot characters
                var totalNumber = parseFloat(totalString); // Convert cleaned string to a floating-point number
                var totalFloat = totalNumber.toFixed(2);
                $('#nameEdit').val(name);
                $('#totalEdit').val(totalFloat);
                $('#BtnEditCharge').attr('data-value',Idcharge);
                $('#ModelChargeEdit').modal("show");

            });

            $(selector + ' tbody').on('click', '.trash', function(e)
            {
                e.preventDefault();
                var IdCharge  = $(this).attr('value');
                swal({
                    title: "es-tu sûr de supprimer cette charge",
                    text: "Une fois supprimée, vous ne pourrez plus récupérer cette charge !",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                  })
                  .then((willDelete) => {
                    if (willDelete)
                    {
                        var data =
                        {
                            'id'         : IdCharge,
                            '_token'     : csrf_token,
                        };
                        $.ajax({
                            type: "post",
                            url: TrashCharge,
                            data: data,

                            dataType: "json",
                            success: function (response)
                            {
                                if(response.status == 200)
                                {
                                    swal("Votre charge a été supprimée !", {
                                        icon: "success",
                                    });
                                    $('.TableCharge').DataTable().ajax.reload();
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

            $(selector + ' tbody').on('click', '.ChangeDate', function(e)
            {
                e.preventDefault();
                var idcharge = $(this).attr('value');
                $('#idCharge').val(idcharge);
                $('#ModelChargeEditDate').modal('show');
            });

        }
    });

    $('#BtnEditCharge').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'name'          : $('#nameEdit').val(),
            '_token'        : csrf_token,
            'total'         : $('#totalEdit').val(),
            'id'            : $(this).attr('data-value'),

        };

        $.ajax({
            type: "post",
            url: updateCharge,
            data: data,
            dataType: "json",
            success: function (response)
            {

                if(response.status == 200)
                {
                    $('.ValidationChargeEdit').html("");
                    $('#ModelChargeEdit').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableCharge').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationChargeEdit').html("");
                    $('.ValidationChargeEdit').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationChargeEdit').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });


    $('#BtnSaveCharge').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'name'          : $('#name').val(),
            '_token'        : csrf_token,
            'total'         : $('#total').val(),

        };

        $.ajax({
            type: "post",
            url: StoreCharge,
            data: data,
            dataType: "json",
            success: function (response)
            {

                if(response.status == 200)
                {
                    $('.ValidationCharge').html("");
                    $('#ModelCharge').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableCharge').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationCharge').html("");
                    $('.ValidationCharge').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationCharge').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });
});
