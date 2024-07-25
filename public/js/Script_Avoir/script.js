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
        $.ajax({
            type: "get",
            url: getClientByCompany,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('#IdClient').empty();
                    $('#IdClient').append(`<option value="0">veuillez sélectionner le client</option>`);
                    $.each(response.data, function (index, value)
                    {
                        $('#IdClient').append(`<option value="${value.id}">${value.client}</option>`

                        )
                    });
                }
            }
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
    $('#IdClient').on('change',function(e)
    {
        if($(this).val() == 0)
        {
            toastr.error('veuillez sélectionner le client', 'Error');
            return false;
        }
        else
        {

            var newIdClient = $(this).val();
            var newIdCompany = IdCompanyActive.id; // Assuming company ID does not change
            $.ajax({
                type: "get",
                url: checkClientHasOrder,
                data:
                {
                    idclient : newIdClient,
                },
                dataType: "json",
                success: function (response)
                {
                    if(response.status == 200)
                    {


                        // Destroy the existing DataTable
                        reloadTable(newIdClient, newIdCompany);
                        $('#ModelOrderByClient').modal("show");

                       /*  updateTotals(); */

                        $('.CardRemark').css('display','block');
                        $('.CardRemark').addClass('slide-down');
                       /*  $('#remark').val(response.remark); */
                    }
                    else if(response.status == 442)
                    {
                        toastr.error(response.message,"Erreur");
                        return false;
                    }

                }
            });
        }
    });

    function initializeTableTmpLineAvoir(idclient, idcompany) {
        var TableVenteByClient = $('.TableVenteByClient').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ordering: false,
            ajax: {
                url: GetOrderClient,
                data: function (d) {
                    d.idclient = idclient;
                    d.idcompany = idcompany;
                },
            },
            columns: [
                { data: 'client', name: 'client' },
                {
                    data: 'montantOrder',
                    name: 'montantOrder',
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
                        var html = row.idfacture ? '<span class="btn btn-sm btn-success">Facture</span>' : '<span class="btn btn-sm btn-info">Bon</span>';
                        return html;
                    },
                },
                { data: 'title', name: 'title' },
                { data: 'user', name: 'user' },
                { data: 'creer_le', name: 'creer_le' },
            ],
            columnDefs: [
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
        $('.TableVenteByClient tbody').on('click', 'tr', function (e)
        {
            e.preventDefault();
            var data = TableVenteByClient.row(this).data();
            GetProductByOrderByClient(data.id);
        });
        return TableVenteByClient;
    }

    function GetProductByOrderByClient(idorder)
    {
        var TableProductsByOrder = $('.TableProductsByOrder').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ordering: false,
            ajax: {
                url: GetOrderClient,
                data: function (d) {
                    d.idclient = idclient;
                    d.idcompany = idcompany;
                },
            },
            columns: [
                { data: 'client', name: 'client' },
                {
                    data: 'montantOrder',
                    name: 'montantOrder',
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
                        var html = row.idfacture ? '<span class="btn btn-sm btn-success">Facture</span>' : '<span class="btn btn-sm btn-info">Bon</span>';
                        return html;
                    },
                },
                { data: 'title', name: 'title' },
                { data: 'user', name: 'user' },
                { data: 'creer_le', name: 'creer_le' },
            ],
            columnDefs: [
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
    }
});
