$(document).ready(function () {
    var IdCompanyActive = IdCompanyActiveExtren;
    let originalTotalHt = parseFloat($('#TotalHT').text().replace(' DH', ''));
    let tvaCalcul         = tvaFromDataBase;
    let originalTotalTTC  = parseFloat($('#TotalTTC').text().replace(' DH', ''));
    $('#IdClient').select2({
        dropdownParent: $('#AddAvoir'), // Ensure it attaches to the modal
        placeholder: "veuillez sélectionner le client",
        allowClear: true
    });
    function FunctiongetClientByCompany()
    {
        $('#IdClient').empty();
        $('#IdClient').append(`<option value="0">veuillez sélectionner le client</option>`);
        $.each(Clients, function (index, value)
        {
            $('#IdClient').append(`<option value="${value.id}">${value.client}</option>`

            )
        });

    }
    FunctiongetClientByCompany();
    function reloadTable(idclient, idcompany) {
        if ($.fn.DataTable.isDataTable('.TableVenteByClient')) {
            $('.TableVenteByClient').DataTable().destroy();

        }
        initializeTableTmpLineAvoir(idclient, idcompany);
    }
    /* function updateTotals() {
        var IdClient = $('#IdClient').val();
        $.get(GetTotalByClientCompanyaVoir, { idclient: IdClient, idcompany: IdCompanyActive.id },
            function(data, textStatus, jqXHR) {
                $('#TotalHT').text(data.sumTotal + ' DH');
                $('#CalculTva').text(data.Calcul_Tva.toFixed(2) + ' DH');
                $('#TotalTTC').text(data.TotalTTC.toFixed(2) + ' DH');
                $('#Plafonnier').text(data.Plafonnier + ' DH');
                $('#TotalCredit').text(data.TotalCredit + ' DH');
                originalTotalHt = parseFloat($('#TotalHT').text().replace(' DH', ''));
                tvaCalcul       = tvaFromDataBase;
                originalTotalTTC = parseFloat($('#TotalTTC').text().replace(' DH', ''));

            },
            "json"
        );
    } */
        $('#IdClient').on('change', function(e) {
            if ($(this).val() == 0) {
                toastr.error('veuillez sélectionner le client', 'Error');
                return false;
            } else {
                var newIdClient = $(this).val();
                var newIdCompany = IdCompanyActive.id; // Assuming company ID does not change
                $.ajax({
                    type: "get",
                    url: checkClientHasOrder,
                    data: {
                        idclient: newIdClient,
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status == 200) {
                            // Destroy the existing DataTable
                            destroyTable('.TableVenteByClient');
                            $('.TableProductsByOrder').DataTable().clear().destroy();
                            reloadTable(newIdClient, newIdCompany);
                            reloadTableTmpLineOrder(newIdClient,newIdCompany);
                            GetTotalTmpAvoir(newIdClient);
                            $('#ModelOrderByClient').modal("show");

                            $('.CardRemark').css('display', 'block');
                            $('.CardRemark').addClass('slide-down');
                        } else if (response.status == 442) {
                            toastr.error(response.message, "Erreur");
                            return false;
                        }
                    }
                });
            }
        });

        function destroyTable(selector) {
            if ($.fn.DataTable.isDataTable(selector)) {
                $(selector).DataTable().clear().destroy();
            }
        }

        function initializeTableTmpLineAvoir(idclient, idcompany) {
            var TableVenteByClient = $('.TableVenteByClient').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                ajax: {
                    url: GetOrderClient,
                    data: function(d) {
                        d.idclient = idclient;
                        d.idcompany = idcompany;
                    },
                },
                columns: [
                    { data: 'client', name: 'client' },
                    {
                        data: 'totalvente',
                        name: 'totalvente',
                        render: function(data, type, row) {
                            return data + ' DH';
                        },
                        className: "dt-right"
                    },
                    {
                        data: 'totalpaye',
                        name: 'totalpaye',
                        render: function(data, type, row) {
                            return data + ' DH';
                        },
                        className: "dt-right"
                    },
                    {
                        data: 'reste',
                        name: 'reste',
                        render: function(data, type, row) {
                            return data + ' DH';
                        },
                        className: "dt-right"
                    },
                    {
                        data: 'idfacture',
                        name: 'idfacture',
                        render: function(data, type, row) {
                            var html = row.idfacture ? '<span class="btn btn-sm btn-success">Facture</span>' : '<span class="btn btn-sm btn-info">Bon</span>';
                            return html;
                        },
                    },
                    { data: 'title', name: 'title' },
                    { data: 'user', name: 'user' },
                    { data: 'created_at_formatted', name: 'created_at_formatted' },
                    { data: 'id', name: 'id' },
                ],
                columnDefs: [
                    {
                        targets: [8], // Targeting the idfacture column
                        visible: false,
                    },
                    {
                        targets: 2,
                        width: '150px'
                    },
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

            return TableVenteByClient;
        }

        $('.TableVenteByClient tbody').on('click', 'tr', function(e) {
            e.preventDefault();
            var data = $('.TableVenteByClient').DataTable().row(this).data();



            GetProductByOrderByClient(data.id);

            $('#ModelOrderByClient').modal('hide');
        });

        function GetProductByOrderByClient(idorder) {
            destroyTable('.TableProductsByOrder');

            var TableProductsByOrder = $('.TableProductsByOrder').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                ajax: {
                    url: GetProductByOrderClient,
                    data: function(d) {
                        d.idorder = idorder;
                    },
                },
                columns: [
                    { data: 'numero_bon', name: 'numero_bon' },
                    { data: 'name', name: 'name' },
                    {
                        data: 'qte_convert',
                        name: 'qte_convert',
                        render: function(data, type, row) {
                            return data + row.type;
                        },
                    },
                    {
                        data: 'price',
                        name: 'price',
                        render: function(data, type, row) {
                            return data + ' DH';
                        },
                        className: "dt-right"
                    },
                    {
                        data: 'accessoire',
                        name: 'accessoire',
                        render: function(data, type, row) {
                            return data + ' DH';
                        },
                        className: "dt-right"
                    },
                    {
                        data: 'total',
                        name: 'total',
                        render: function(data, type, row) {
                            return data + ' DH';
                        },
                        className: "dt-right"
                    },
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
            return TableProductsByOrder;
        }

    $('.TableProductsByOrder tbody').on('click','tr',function(e)
    {
        e.preventDefault();
        var data = $('.TableProductsByOrder').DataTable().row(this).data();

        $.ajax({
            type    : "post",
            url     : StoreTmpAvoir,
            headers:
            {
                'X-CSRF-TOKEN': csrf_token
            },
            data    : data,
            dataType: "json",
            success : function (response)
            {
                if(response.status == 422)
                {
                    toastr.error(response.message,'Erreur');
                }
                else if(response.status == 200)
                {
                    toastr.success(response.message,'Success');

                    var idclient = response.Data.idclient;
                    var idcompany = response.Data.idcompany;


                    reloadTableTmpLineOrder(idclient,idcompany)
                }
                else if(response.status == 400)
                {
                    toastr.error(response.message,'Erreur');
                }
            }
        });
    });
    function reloadTableTmpLineOrder(idclient, idcompany) {
        if ($.fn.DataTable.isDataTable('.TableTmpAvoir')) {
            $('.TableTmpAvoir').DataTable().destroy();

        }
        InitializeTableProductsInTableTmpAvoir(idclient, idcompany);
    }
    function InitializeTableProductsInTableTmpAvoir(idclient,idcompany)
    {
        return $('.TableTmpAvoir').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ordering: false,
            ajax: {
                url: DisplayProductsTableTmpAvoir,
                data: function (d) {
                    d.idclient = idclient;
                    d.idcompany = idcompany;
                },
            },
            columns: [
                { data: 'name', name: 'name' },
                {
                    data: 'qte',
                    name: 'qte',
                    render: function (data, type, row) {
                        var uniteSpan = row.type ? '<span class="input-group-text bg-warning" style="width:100%;display:block">' + row.type + '</span>' : '';

                        return '<div class="row g-0">\
                                    <div class="col-12 d-flex justify-content-center" >\
                                        <div class="quantity" style="width:100%">\
                                            <button class="minus" aria-label="Decrease" data-value="'+row.id+'" hidden>&minus;</button>\
                                            <input type="number" class="input-box" value="'+data+'" min="1" max="10000">\
                                            <button class="plus" aria-label="Increase" data-value="'+row.id+'" hidden >&plus;</button>\
                                             ' + uniteSpan + '\
                                        </div>\
                                    </div>\
                                </div>';
                    },
                    className: "dt-right"
                },
                {
                    data: 'price',
                    name: 'price',
                    render: function (data, type, row) {
                        return data + ' DH';
                    },
                    className: "dt-right"
                },
                {
                    data: 'accessoire',
                    name: 'accessoire',
                    render: function (data, type, row) {
                        /* return data + ' DH'; */
                        return '<input type="number" class="inputAccessoire"  value="'+data+'"  max="10000" style="border: 1px solid skyblue;border-radius: 10px;text-align: center;padding: 5px">';
                    },
                    className: "dt-right"
                },
                {
                    data: 'total',
                    name: 'total',
                    render: function (data, type, row) {
                        return data + ' DH';
                    },
                    className: "dt-right"
                },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            columnDefs: [

                {
                    targets: 2, // Targeting the third column (price)
                    width: '150px' // Set the desired width
                },

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

    $(document).on('keypress','.TableTmpAvoir tbody .input-box', function (e)
    {



        var $input = $(this);
        var newValue = $input.val().trim();
        var key = e.which;
        if(key == 13)
        {
            e.preventDefault();
            var table = $('.TableTmpAvoir').DataTable();
            var row = $input.closest('tr');
            var data = table.row(row).data();

            // Update the data object with the new value
            data.newValue = newValue;
            $.ajax({
                type        : "get",
                url         : CheckQteChangeNotSuperQteOrderAndUpdateQte,
                data        : data,
                dataType    : "json",
                success     : function (response)
                {
                    if(response.status == 422)
                    {
                        toastr.error(response.message,'Erreur');
                    }
                    else if(response.status == 200)
                    {
                        table.ajax.reload();
                        var idclient = $('#IdClient').val();
                        GetTotalTmpAvoir(idclient);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + status + " - " + error);
                }
            });
        }
    });

    function GetTotalTmpAvoir(idclient)
    {
        $.ajax({
            type        : "get",
            url         : TotalTmpAvoir,
            data        :
            {
                idclient :idclient
            },
            dataType    : "json",
            success     : function (response)
            {
                if(response.status == 200)
                {
                    $('#TotalHT').text(response.Total_HT + ' DH');
                    $('#CalculTva').text(response.TVA + 'DH');
                    $('#TotalTTC').text(response.Total_TTC + 'DH');

                }
            }
        });
    }

});
