$(document).ready(function ()
{
    $(function ()
    {
        initializeDataTable('.TableFacture', Facture);
        function initializeDataTable(selector, url)
        {
            var tableVente = $(selector).DataTable({
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
                        data: 'totalvente',
                        name: 'totalvente',
                        render: function (data, type, row) {
                            return data + ' DH';
                        },
                        className: "dt-right"
                    },
                    {
                        data: 'totalpaye',
                        name: 'totalpaye',
                        render: function (data, type, row) {
                            return data + ' DH';
                        },
                        className: "dt-right"
                    },
                    {
                        data: 'reste',
                        name: 'reste',
                        render: function (data, type, row) {
                            return data + ' DH';
                        },
                        className: "dt-right"
                    },
                    {
                        data: 'idfacture',
                        name: 'idfacture',
                        render: function (data, type, row) {
                            var html = row.idfacture ? '<span class="btn btn-md btn-success">Facture</span>' : '<span class="btn btn-md btn-info">Bon</span>';
                            return html;
                        },

                    },
                    {data: 'company'        , name: 'company'},
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


        }
    });
});
