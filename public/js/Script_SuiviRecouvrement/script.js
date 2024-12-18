$(document).ready(function () {
    $(function ()
    { 
        initializeDataTable('.TableSuiviRecouverement', Suivirecouverement);
        function initializeDataTable(selector, url)
        {
            var TableSuiviRecouverement = $(selector).DataTable({
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

                    {data: 'client'             , name: 'client'},
                    {
                        data: 'total',
                        name: 'total',
                        render: function (data, type, row) {
                            return data + ' DH';
                        },
                        className: "dt-right"
                    },
                    {data: 'title'              , name: 'title'},
                    {data: 'date_paye'          , name: 'date_paye'},
                    {data: 'date_credit'        , name: 'date_credit'},
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
            $(selector + " tbody").on('click', '.trashP_Paiement', function(e) {
                e.preventDefault();
                
                var idPaiement = $(this).attr('value');
                swal({
                    title: "es-tu sûr de supprimer cette paiement",
                    text: "Une fois supprimée, vous ne pourrez plus récupérer cette paiement !",
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
                            'idPaiement'         : idPaiement,
                            '_token'             : csrf_token,
                        };
                        $.ajax({
                            type: "post",
                            url: DeletePaiement,
                            data: data,

                            dataType: "json",
                            success: function (response)
                            {
                                if(response.status == 200)
                                {
                                    swal("Votre detail paiement a été supprimée !", {
                                        icon: "success",
                                    });
                                    $('.TableSuiviRecouverement').DataTable().ajax.reload();
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
