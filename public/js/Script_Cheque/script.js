$(document).ready(function ()
{
    $(function ()
    {
        initializeDataTable('.TableCheque', getCheque);
        function initializeDataTable(selector, url)
        {
            var TableCheque = $(selector).DataTable({
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

                    {data: 'numero'             , name: 'numero'},
                    {data: 'datecheque'         , name: 'datecheque'},
                    {data: 'datepromise'        , name: 'datepromise'},
                    {
                        data: 'montant',
                        name: 'montant',
                        render: function (data, type, row) {
                            return data + ' DH';
                        },
                        className: "dt-right"
                    },
                    {data: 'type'               , name: 'type'},
                    {data: 'name'               , name: 'name'},
                    {
                        data: 'status',
                        name: 'status',
                        render: function (data, type, row) {
                            if (data === null || data === 'En cours') {
                                return '<span class="badge bg-info-subtle text-info">En cours</span>';
                            } else if (data === 'Validé') {
                                return '<span class="badge bg-success text-white">Validé</span>';
                            } else if (data === 'Non Validé') {
                                return '<span class="badge bg-danger text-white">Non Validé</span>';
                            } else {
                                return data;
                            }
                        }
                    },
                    {data: 'bank'               , name: 'bank'},

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



            $(selector + ' tbody').on('click','.edit',function(e)
            {
                e.preventDefault();
                var Data = TableCheque.row($(this).closest('tr')).data();
                $('#numero').val(Data.numero);
                $('#Status').val(Data.status == null ? "En cours" : Data.status).change();
                $('#UpdateStatusCheque').modal("show");
                $('#BtnChangeStatusCheque').attr('data-value',Data.id);
            });

            $('#BtnSearchCheque').on('click',function(e)
            {
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();

                // Add date parameters to DataTables ajax URL
                TableCheque.ajax.url(url + '?startDate=' + startDate + '&endDate=' + endDate).load();
            });

            $('#BtnChangeStatusCheque').on('click',function()
            {
                var data =
                {
                    'id'     : $(this).attr('data-value'),
                    'status' : $('#Status').val(),
                };
                $.ajax({
                    type: "get",
                    url: ChangeStatus,
                    data: data,
                    dataType: "json",
                    success: function (response)
                    {
                        if(response.status == 200)
                        {

                            $('#UpdateStatusCheque').modal("hide");
                            $('.TableCheque').DataTable().ajax.reload();
                            toastr.success(response.message, 'Success');
                        }
                    }
                });
            });
        }
    });



});
