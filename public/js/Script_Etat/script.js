$(document).ready(function() {
    $(function ()
    {
        initializeDataTable('#TableEtat', SearchEtatTable);
        function initializeDataTable(selector, url)
        {
            var TableBordereau = $(selector).DataTable({
                processing: true,
                serverSide: true,
                lengthChange: false,
                pageLength: 10000,
                info: false,
                paging: false, // Disable pagination controls
                dom: 'lrtip',
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
                        data: 'total_qte',
                        name: 'total_qte',
                        render: function (data, type, row) {
                            var typeLabel = row.type ? row.type : 'kg';
                            return data + ' ' + typeLabel;
                        },
                        className: "dt-right"
                    },



                    {data: 'created_at'     , name: 'created_at'},


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

    $(function ()
    {
        initializeDataTable('#TableProduction', EtatProduction);
        function initializeDataTable(selector, url)
        {
            var TableBordereau = $(selector).DataTable({
                processing: true,
                serverSide: true,
                lengthChange: false,
                pageLength: 10000,
                info: false,
                paging: false, // Disable pagination controls
                dom: 'lrtip',
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
                            return data + ' DH' ;
                        },
                        className: "dt-right"
                    },
                    {
                        data: 'totalpaye',
                        name: 'totalpaye',
                        render: function (data, type, row) {
                            return data + ' DH' ;
                        },
                        className: "dt-right"
                    },
                    {
                        data: 'reste',
                        name: 'reste',
                        render: function (data, type, row) {
                            return data + ' DH' ;
                        },
                        className: "dt-right"
                    },



                    {data: 'created_at'     , name: 'created_at'},


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

    $(function ()
    {
        initializeDataTable('#TableTotalUnit', TotalUniteByDate);
        function initializeDataTable(selector, url)
        {
            var TableBordereau = $(selector).DataTable({
                processing: true,
                serverSide: true,
                lengthChange: false,
                pageLength: 10000,
                info: false,
                paging: false, // Disable pagination controls
                dom: 'lrtip',
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

                    {
                        data: 'type',
                        name: 'type',
                        render: function (data, type, row) {
                            var typeLabel = row.type ? row.type : 'KG';
                            return  typeLabel;
                        },

                    },

                    {data: 'total_qte'             , name: 'total_qte'},




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
    $('.accordion-header').click(function() {
        var accordionItem = $(this).parent();
        var accordionContent = accordionItem.find('.accordion-content');

        // Toggle active class on header
        $(this).toggleClass('active');

        // Toggle visibility of accordion content
        if (accordionContent.is(':visible')) {
            accordionContent.slideUp();
        } else {
            accordionContent.slideDown();
        }
    });

    $('#searchForm').submit(function(event)
    {
        event.preventDefault();
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();
        if (startDate === '' || endDate === '') {
            toastr.warning('Veuillez remplir la date de début et la date de fin.','attention');
            return;
        }
        this.submit();
    });
});
