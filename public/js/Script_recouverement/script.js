$(document).ready(function () {
    var IdCompanyActive = IdCompanyActiveExtren;
    $('#IdClient').select2({

        placeholder: "veuillez sélectionner le client",
        allowClear: true
    });

    function initializeTableRecouverement(idclient, idcompany) {
        return $('.TableRecouverement').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ordering: false,
            ajax: {
                url: GetRecouvementClient,
                data: function (d) {
                    d.idclient = idclient;
                    d.idcompany = idcompany;
                },
            },
            columns: [
                {
                    data: 'id',  // assuming 'id' is the property containing unique identifier
                    name: 'id',
                    render: function (data, type, row) {
                        return '<input type="checkbox" class="row-checkbox" value="' + data + '">';
                    },
                    className: "dt-center",
                    orderable: false,
                    searchable: false
                },
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

    // Function to destroy and reinitialize the DataTable
    function reloadTable(idclient, idcompany) {
        if ($.fn.DataTable.isDataTable('.TableRecouverement')) {
            $('.TableRecouverement').DataTable().destroy();
            /* $('.TableTmpVente').empty(); */ // Clear table content to avoid conflicts
        }
        initializeTableRecouverement(idclient, idcompany);
    }

    $('#IdClient').on('change',function(e)
    {
        e.preventDefault();
        var value = $(this).val();
        if(value == 0)
        {
            toastr.error('veuillez sélectionner le client', 'Error');
            return false;
        }
        else
        {
            var newIdClient = $(this).val();
            var newIdCompany = IdCompanyActive.id; // Assuming company ID does not change

            // Destroy the existing DataTable
            reloadTable(newIdClient, newIdCompany);

        }
    });
    /////////////
    function initializeTableRecouverementSelected(id) {
        return $('.TableRecouverementSelected').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ordering: false,
            ajax: {
                url: GetDataSelectedRecouvement,
                data: function (d) {
                    d.id = id;

                },
            },
            columns: [

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
                {

                    render: function (data, type, row)
                    {
                        var html = '<select class="form-select modePaiement" name="mode_paiement">';
                        $.each(ModePaiement, function(index, value) {
                            html += '<option value="' + value.id + '">' + value.name + '</option>';
                        });
                        html += '</select>';

                        return html;
                    },

                },
                {

                    render: function (data, type, row) {
                        var html =  '<input type="number" class="form-control MontantSaisir" min="1" placeholder="Saisir montant">';
                        return html;
                    },

                },
                {
                    /* // Hidden column for ID data
                    data: 'id',
                    name: 'id',
                    visible: false, // This hides the column */
                    render: function (data, type, row) {
                        var html =  '<input type="number" class="form-control idorder" min="1" placeholder="Saisir montant" value="'+row.id+'" hidden>';
                        return html;
                    },
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

    // Function to destroy and reinitialize the DataTable
    function reloadTableSelected(id) {
        if ($.fn.DataTable.isDataTable('.TableRecouverementSelected')) {
            $('.TableRecouverementSelected').DataTable().destroy();
            /* $('.TableTmpVente').empty(); */ // Clear table content to avoid conflicts
        }
        initializeTableRecouverementSelected(id);
    }
    function clearTableRecouverementSelected() {
        if ($.fn.DataTable.isDataTable('.TableRecouverementSelected')) {
            $('.TableRecouverementSelected').DataTable().clear().destroy();
        }
    }
    /////////////
    var checkedValues = [];
    $(document).on('click', '.row-checkbox', function () {
        var checkboxValue = $(this).val();
        var isChecked = $(this).prop('checked');

        // Update checkedValues array based on checkbox state
        if (isChecked && checkedValues.indexOf(checkboxValue) === -1) {
            checkedValues.push(checkboxValue); // Add to array if checked and not already in array
        } else if (!isChecked && checkedValues.indexOf(checkboxValue) !== -1) {
            checkedValues = checkedValues.filter(function(value) {
                return value !== checkboxValue; // Remove from array if unchecked
            });
        }


        if(checkedValues.length > 0)
        {
            reloadTableSelected(checkedValues);
        }
        else
        {
            clearTableRecouverementSelected();
        }
    });

    $('#Encaissement').on('click',function(e)
    {
        e.preventDefault();
        var lengthTableTmp = $('.TableRecouverementSelected tbody tr td.dt-right').length;
        if (lengthTableTmp === 0)
        {
            toastr.warning("Une table recouverement panier vide ne peut pas être exploitée", 'Attention');
            return false;
        }
        else
        {
            var hasEmptyInput = false;
            $('.MontantSaisir').each(function() {
                if ($(this).val().trim() === '')
                {
                    hasEmptyInput = true;
                    return false;
                }
            });

            if (hasEmptyInput)
            {
                toastr.warning("Veuillez saisir un montant pour tous les éléments", 'Attention');
                return false;
            }
            else
            {
                let modePaiementTable = [];

                $('.TableRecouverementSelected tbody tr').each(function() {
                    var data = $('.TableRecouverementSelected').DataTable().row(this).data();
                    let id = data.id;
                    let mode = $(this).find('select[name="mode_paiement"]').val();
                    let prix = parseFloat($(this).find('input.MontantSaisir').val());
                    let reste = $(this).find('td:eq(3)').text();

                    // Extract numeric value from reste (e.g., "800.00 dh" -> 800)
                    reste = parseFloat(reste.replace(/[^\d.-]/g, ''));

                    // Ensure prix is a valid number
                    if (prix > reste)
                    {
                        toastr.warning(`Le Prix (${prix}) DH ne peut pas être supérieur à Reste (${reste}) DH `,'Attention')

                        return false;
                    }
                    else
                    {

                        if (!isNaN(prix)) {
                            modePaiementTable.push({
                                'idorder': id,
                                'mode': mode,
                                'prix': prix,
                                'reste' : reste,
                            });
                        }
                    }
                });
                if(modePaiementTable.length > 0)
                {
                    let DataPaiement = Object.values(modePaiementTable);
                    var data =
                    {

                        'ModePaiement'  : DataPaiement,
                        '_token'        : csrf_token,
                    };
                    $.ajax({
                        type            : "post",
                        url             : StoreRecouvement,
                        data            : data,
                        dataType        : "json",
                        success: function (response)
                        {
                            if(response.status == 200)
                            {
                                toastr.success("L'opération s'est terminée avec succès", 'Success');
                                $('.TableRecouverement').DataTable().ajax.reload();
                                clearTableRecouverementSelected();
                            }
                        }
                        ,
                        error: function (xhr, status, error) {


                            toastr.error('Failed to process request: ' + error, 'Error');
                        }
                    });
                }



            }
        }
    });

});
