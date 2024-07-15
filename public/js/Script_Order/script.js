$(document).ready(function ()
{
    var IdCompanyActive = IdCompanyActiveExtren;
    let originalTotalHt = parseFloat($('#TotalHT').text().replace(' DH', ''));
    let tvaCalcul         = tvaFromDataBase;
    let originalTotalTTC  = parseFloat($('#TotalTTC').text().replace(' DH', ''));;
    $('.select2').select2({
            dropdownParent: $('#AddOrder'), // Ensure it attaches to the modal
            placeholder: "veuillez sélectionner le produit",
            allowClear: true
        });

    $('#IdClient').select2({
        dropdownParent: $('#AddOrder'), // Ensure it attaches to the modal
        placeholder: "veuillez sélectionner le client",
        allowClear: true
    });
    $('.datecheque, .datepromise').change(function() {
        var date1Val = $('.datecheque').val();
        var date2Val = $('.datepromise').val();

        if (date1Val !== '' && date2Val !== '')
        {
            var date1 = new Date(date1Val);
            var date2 = new Date(date2Val);

            if (date2 <= date1)
            {
                toastr.error('La date promise doit être supérieure à la date chèque.','Erreur');
                $('.datepromise').val('');
            }
        }
    });
    $(function ()
    {
        initializeDataTable('.TableVente', GetMyVente);
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

           /*  $(selector + "tbody").on('click','.view',function(e)
            {
                e.preventDefault();
                var data = $('.TableVente').DataTable().row(this).data();
                var idToEncrypt = data.id.toString(); // Ensure id is in string format if necessary

                // Encode ID using base64
                var encryptedId = btoa(idToEncrypt);

                // Redirect to ShowOrder route with encryptedId as parameter
                window.location.href = ShowOrder + "/" + encryptedId;


            }); */


        }
    });
    /////////////////////
    function initializeTableTmpLineOrder(idclient, idcompany,typeVente) {
        return $('.TableTmpVente').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ordering: false,
            ajax: {
                url: GetDataTmpOrderByClient,
                data: function (d) {
                    d.idclient = idclient;
                    d.idcompany = idcompany;
                    d.typeVente = typeVente;
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
                                            <button class="minus" aria-label="Decrease" data-value="'+row.id+'">&minus;</button>\
                                            <input type="number" class="input-box" value="'+data+'" min="1" max="10000">\
                                            <button class="plus" aria-label="Increase" data-value="'+row.id+'">&plus;</button>\
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
                        return '<input type="number" class="inputAccessoire"  value="'+data+'" min="0" max="10000" style="border: 1px solid skyblue;border-radius: 10px;text-align: center;padding: 5px">';
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

    // Function to destroy and reinitialize the DataTable
    function reloadTable(idclient, idcompany,typeVente) {
        if ($.fn.DataTable.isDataTable('.TableTmpVente')) {
            $('.TableTmpVente').DataTable().destroy();
            /* $('.TableTmpVente').empty(); */ // Clear table content to avoid conflicts
        }
        initializeTableTmpLineOrder(idclient, idcompany,typeVente);
    }
    const $dropDownProduct = $('#DropDownProduct');
    const $divTypeVente = $('#DivTypeVente');
    const $dropDownTypeVente = $('#DropDownTypeVente');
    const $tableStock = $('.TableStock');
    function initializeDataTable(product,uniteVente)
    {
        if ($.fn.dataTable.isDataTable($tableStock)) {
            $tableStock.DataTable().destroy();
        }
        $tableStock.DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: DisplayProductStock,
                data: function (d) {
                    d.product = product;
                    d.type    = uniteVente;
                }
            },
            columns: [
                { data: 'numero_bon', name: 'numero_bon' },
                { data: 'name', name: 'name' },
                { data: 'qte', name: 'qte', className: "dt-right" },
                { data: 'price', name: 'price', render: data => `${data} DH`, className: "dt-right" },
               /*  { data: 'title', name: 'title' }, */

                { data: 'id', name: 'id', visible: false },
                { data: 'idstock', name: 'idstock', visible: false }
            ],
            language: {
                sInfo: "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                sInfoEmpty: "Affichage de l'élément 0 à 0 sur 0 élément",
                sInfoFiltered: "(filtré à partir de _MAX_ éléments au total)",
                sLengthMenu: "Afficher _MENU_ éléments",
                sLoadingRecords: "Chargement...",
                sProcessing: "Traitement...",
                sSearch: "Rechercher :",
                sZeroRecords: "Aucun élément correspondant trouvé",
                oPaginate: {
                    sFirst: "Premier",
                    sLast: "Dernier",
                    sNext: "Suivant",
                    sPrevious: "Précédent"
                },
                oAria: {
                    sSortAscending: ": activer pour trier la colonne par ordre croissant",
                    sSortDescending: ": activer pour trier la colonne par ordre décroissant"
                },
                select: {
                    rows: {
                        _: "%d lignes sélectionnées",
                        0: "Aucune ligne sélectionnée",
                        1: "1 ligne sélectionnée"
                    }
                }
            }
        });
    }
    var  nameProduct ="";
    $dropDownProduct.on('change', function(e) {
        e.preventDefault();
         nameProduct = $(this).val();

        $.ajax({
            type: "GET",
            url: getUniteVenteByProduct,
            data: { name: nameProduct },
            dataType: "json",
            success: function(response) {
                if (response.status === 200)
                {

                    $divTypeVente.show();
                    $dropDownTypeVente.empty().append('<option value="0" selected>Veuillez sélectionner type de vente </option>');
                    $.each(response.data, function(index, value) {
                        $dropDownTypeVente.append(`<option value="${value.id}">${value.type}</option>`);
                    });
                    initializeDataTable(nameProduct,$('#DropDownTypeVente').val());
                    $dropDownProduct.val("");

                }
                else
                {

                    $dropDownTypeVente.val("0").change();
                    initializeDataTable(nameProduct,$('#DropDownTypeVente').val());
                    $dropDownProduct.val("");
                    $divTypeVente.hide();
                    $dropDownTypeVente.empty();
                }
            }
        });
    });
    $dropDownTypeVente.on('change',function(e)
    {
        e.preventDefault();
        var DropDownProduct = nameProduct;
        var DropDownTypeVente = $('#DropDownTypeVente').val();

        initializeDataTable(DropDownProduct,DropDownTypeVente);
    });


    $(document).on('click', '.TableStock tbody tr', function(e)
    {
        e.preventDefault();
        var idclient = $('#IdClient').val();
        var typeVente = $('#DropDownTypeVente').val();
        var optionExists = false;
        $('#DropDownTypeVente option').each(function() {
            if ($(this).val() === typeVente || $(this).text() === typeVente) {
                optionExists = true;
                return false; // Break the loop
            }
        });
        if(idclient == 0)
        {
            toastr.error('veuillez sélectionner le client', 'Error');
            return false;
        }
        if (optionExists)
        {
            if(typeVente == 0)
            {
                toastr.error('veuillez sélectionner type de vente', 'Error');
                return false;
            }

        }
        var data = $('.TableStock').DataTable().row(this).data();
        var data = $('.TableStock').DataTable().row(this).data();
        var idproduct =
        {
            'idproduct' :data.id,
            'type'      : $('#DropDownTypeVente').val(),
            'idclient'  : $('#IdClient').val(),
        };
        $.ajax({
            type: "get",
            url: checkQteProduct,
            data: idproduct,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $.get(sendDataToTmpOrder, {idproduct : data.id, idclient : $('#IdClient').val(),idcompany : IdCompanyActive.id,typeVente : $('#DropDownTypeVente').val(),idstock : data.idstock},
                        function (data, textStatus, jqXHR) {
                            if(data.status == 200)
                            {
                                var newIdClient = $('#IdClient').val();
                                var newIdCompany = IdCompanyActive.id; // Assuming company ID does not change

                                // Destroy the existing DataTable
                                reloadTable(newIdClient, newIdCompany,typeVente);
                                updateTotals();
                                toastr.success("Le produit a été ajouté au panier", 'Success');
                                if($('#DivTypeVente').is(':hidden'))
                                {
                                    var uniteVente = null;
                                    initializeDataTable(nameProduct,uniteVente);
                                }
                                else
                                {
                                    initializeDataTable(nameProduct,$('#DropDownTypeVente').val());
                                }

                            }
                        },
                        "json"
                    );
                }
                else if(response.status == 422)
                {
                    toastr.error(response.message, 'Error');
                }
            }
        });





    });

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
            var typeVente    = null;
            $.ajax({
                type: "get",
                url: checkTableTmpHasDataNotThisClient,
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
                        reloadTable(newIdClient, newIdCompany,typeVente);
                        updateTotals();
                        $('.CardRemark').css('display','block');
                        $('.CardRemark').addClass('slide-down');
                        $('#remark').val(response.remark);
                    }
                    else if(response.status == 442)
                    {
                        toastr.error(response.errorMessage,"Erreur");
                        return false;
                    }

                }
            });
        }
    });
    $('#SaveRemark').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'remark'  : $('#remark').val().trim(),
            'idclient': $('#IdClient').val(),
        };
        $.ajax({
            type: "get",
            url: StoreRemark,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationRemark').html("");
                    toastr.success(response.message, 'Success');
                    $('.FormRemaruqe').load(window.location.href + '.FormRemaruqe');
                }
                else if(response.status == 422)
                {
                    $('.ValidationRemark').html("");
                    $('.ValidationRemark').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationRemark').append('<li>'+list_err+'</li>');
                    });
                    setTimeout(function() {
                        $('.ValidationRemark').slideUp('slow');
                    }, 6000);
                }
            }
        });
    });



    $(document).on('click', '.TableTmpVente tbody .trash', function(e){
        e.preventDefault();
        var id = $(this).attr('value');
        var csrf_token = $('meta[name="csrf-token"]').attr('content'); // Assuming you're using a meta tag for CSRF token

        var table = $('.TableTmpVente').DataTable();
        var rowCount = table.rows().count();

        if (rowCount > 0) {
            $.ajax({
                type: "post",
                url: TrashTmpOrder,
                data: {
                    'id': id,
                    '_token': csrf_token
                },
                dataType: "json",
                success: function(response) {
                    if (response.status == 200)
                    {
                        var newIdClient = $('#IdClient').val();
                        var newIdCompany = IdCompanyActive.id; // Assuming company ID does not change
                        var typeVente    = null;
                        // Destroy the existing DataTable
                        reloadTable(newIdClient, newIdCompany,typeVente);
                        updateTotals();
                        toastr.success("Le produit a été supprimé du panier", 'Success');

                        if($('#DropDownProduct').val() != "0")
                        {
                            if($('#DivTypeVente').is(':hidden'))
                            {
                                var uniteVente = null;
                                initializeDataTable(nameProduct,uniteVente);
                            }
                            else
                            {
                                initializeDataTable(nameProduct,$('#DropDownTypeVente').val());
                            }
                        }

                    }
                }
            });
        } else {
            toastr.warning("Vous ne pouvez pas supprimer le dernier élément.", 'Warning');
        }
    });

    function updateTotals() {
        var IdClient = $('#IdClient').val();
        $.get(GetTotalByClientCompany, { idclient: IdClient, idcompany: IdCompanyActive.id },
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
    }


   // Event delegation for dynamically created elements
   $('.TableTmpVente').on('click', 'button.minus, button.plus', function (e) {
    e.preventDefault();


    var $quantityContainer = $(this).closest('.quantity');
    var $inputBox = $quantityContainer.find('.input-box');
    var currentValue = parseInt($inputBox.val());
    var maxValue = parseInt($inputBox.attr('max'));
    var minValue = parseInt($inputBox.attr('min'));
    var idRow    = $(this).attr('data-value');
    if ($(this).hasClass('minus'))
    {


        var newValue = Math.max(currentValue - 1, minValue);
        var newIdClient = $('#IdClient').val();
        var newIdCompany = IdCompanyActive.id; // Assuming company ID does not change

        $.ajax({
            type: "get",
            url: ChangeQteTmpMinus,
            data:
            {
                id: idRow, qte: newValue,type : 'minus',
            },
            dataType: "json",
            async: false,
            success: function (response)
            {
                if (response.status === 200)
                {


                    var newIdClient = $('#IdClient').val();
                    var newIdCompany = IdCompanyActive.id;
                    var typeVente = null;
                    if($('#DivTypeVente').is(':visible'))
                    {
                        typeVente =  $('#DropDownTypeVente').val();
                    }
                    // Destroy the existing DataTable
                    reloadTable(newIdClient, newIdCompany,typeVente);
                    updateTotals();
                    if($('#DivTypeVente').is(':hidden'))
                    {
                        var uniteVente = null;
                        initializeDataTable(nameProduct,uniteVente);
                    }
                    else
                    {
                        initializeDataTable(nameProduct,$('#DropDownTypeVente').val());
                    }

                    toastr.success("La quantité a été modifiée avec succès", 'Success');
                }
                else if (response.status === 422)
                {

                    toastr.error(response.message, "Attention");
                    // Optionally adjust newValue or take other actions
                }
                else
                {

                    toastr.error("Une erreur s'est produite", "Erreur");
                }
            }
        });

    }
    else if ($(this).hasClass('plus'))
    {


        var newValue = Math.min(currentValue + 1, maxValue);
        var newIdClient = $('#IdClient').val();
        var newIdCompany = IdCompanyActive.id; // Assuming company ID does not change
        $.ajax({
            type: "get",
            url : ChangeQteTmpPlus,
            data:
            {
                id: idRow, qte: newValue,type : 'plus',idclient : newIdClient,
            },
            dataType: "json",
            async: false,
            success: function (response)
            {
                if (response.status === 200)
                {


                    var newIdClient = $('#IdClient').val();
                    var newIdCompany = IdCompanyActive.id;
                    var typeVente = null;
                    if($('#DivTypeVente').is(':visible'))
                    {
                        typeVente =  $('#DropDownTypeVente').val();
                    }
                    // Destroy the existing DataTable
                    reloadTable(newIdClient, newIdCompany,typeVente);
                    updateTotals();
                    if($('#DivTypeVente').is(':hidden'))
                    {
                        var uniteVente = null;
                        initializeDataTable(nameProduct,uniteVente);
                    }
                    else
                    {
                        initializeDataTable(nameProduct,$('#DropDownTypeVente').val());
                    }
                    toastr.success("La quantité a été modifiée avec succès", 'Success');

                }
                else if (response.status === 422)
                {
                    newValue -= 1;
                    toastr.error(response.message, "Attention");

                }
                else if(response.status === 500)
                {
                    newValue -= 1;
                    toastr.error(response.message, "Attention");
                }
                else
                {
                    console.log('Other status:', response.status); // Debug statement for other statuses
                    toastr.error("Une erreur s'est produite", "Erreur");
                }
            }
        });
    }

    $inputBox.val(newValue);

    // Update the button states
    updateButtonStates($quantityContainer, newValue, minValue, maxValue);
});

$('.TableTmpVente').on('input', 'input.input-box', function () {
    var $quantityContainer = $(this).closest('.quantity');
    var newValue = parseInt($(this).val());
    var maxValue = parseInt($(this).attr('max'));
    var minValue = parseInt($(this).attr('min'));

    // Ensure value is within bounds
    if (isNaN(newValue) || newValue < minValue) {
        newValue = minValue;
    } else if (newValue > maxValue)
    {
        newValue = maxValue;
    }

    $(this).val(newValue);

    // Update the button states
    updateButtonStates($quantityContainer, newValue, minValue, maxValue);
});
    function updateButtonStates($container, value, min, max) {
        $container.find('.minus').prop('disabled', value <= min);
        $container.find('.plus').prop('disabled', value >= max);
    }


    $('.buttonAddModePaiement').on('click', function() {

        // Check all .TotalModePaiement inputs for emptiness
        var allInputsValid = true;
        $('.TotalModePaiement').each(function() {
            if ($(this).val().trim() === '') {
                allInputsValid = false;
                return false; // Exit the loop early if any input is empty
            }
        });

        // If any input is empty, show alert and focus on the first empty input
        if (!allInputsValid) {

            toastr.warning("Veuillez saisir un montant valide pour le paiement", 'Attention');
            $('.TotalModePaiement').filter(function() {
                return $(this).val().trim() === '';
            }).first().focus();
            return false;
        }
        // Clone the template row
        var newRowHtml = `<tr>
                            <td>
                                <select name="mode_paiement" class="form-select mode_paiement">
                                    <!-- Options will be dynamically populated here -->
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-control TotalModePaiement" placeholder="Saisir montant paiement">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary btnRemoveModePaiement">−</button>
                            </td>
                        </tr>`;

        // Append the new row to the tbody
        $('.TableModePaiement thead').append(newRowHtml);

        // Populate options for the new row's select dropdown
        var selectOptions = '';
        $.each(ModePaiement, function(index, value) {
            selectOptions += '<option value="' + value.id + '">' + value.name + '</option>';
        });
        $('.TableModePaiement thead').find('tr:last-child select[name="mode_paiement"]').html(selectOptions);


    });

    $(document).on('click','.TableModePaiement .btnRemoveModePaiement',function()
    {
        $(this).closest('tr').remove();
    });


    $('#BtnSaveVente').on('click',function(e)
    {
        e.preventDefault();

        var lengthTableTmp = $('.TableTmpVente tbody tr td.dt-right').length;
        if (lengthTableTmp === 0)
        {
            toastr.warning("Une table panier vide ne peut pas être exploitée", 'Attention');
            return false;
        }
        else
        {
            // Check all .TotalModePaiement inputs for emptiness
            var allInputsValid = true;
            $('.TotalModePaiement').each(function() {
                if ($(this).val().trim() === '') {
                    allInputsValid = false;
                    return false; // Exit the loop early if any input is empty
                }
            });

            // If any input is empty, show alert and focus on the first empty input
            if (!allInputsValid) {

                toastr.warning("Veuillez saisir un montant valide pour le paiement", 'Attention');
                $('.TotalModePaiement').filter(function() {
                    return $(this).val().trim() === '';
                }).first().focus();
                return false;
            }
            let modePaiementTable = [];

            $('.TableModePaiement thead tr').each(function() {
                let mode = $(this).find('select[name="mode_paiement"]').val();
                let prix = parseFloat($(this).find('input.TotalModePaiement').val()); // Convert prix to float

                // Initialize the mode in modePaiementTable if it doesn't exist
                if (!modePaiementTable[mode]) {
                    modePaiementTable[mode] = {
                        'mode': mode,
                        'totalPrix': 0 // Initialize totalPrix for this mode
                    };
                }

                // Add prix to the totalPrix for this mode
                modePaiementTable[mode].totalPrix += prix;
            });

            // Convert modePaiementTable to an array of values
            let modePaiementArray = Object.values(modePaiementTable);

            let totalPrixPaiement = 0;

            // Iterate through modePaiementArray to calculate totalPrixPaiement
            for (let i = 0; i < modePaiementArray.length; i++) {
                // Access each mode's totalPrix from modePaiementArray
                totalPrixPaiement += modePaiementArray[i].totalPrix;
            }



            let TotalHtText = $('#TotalHT').text();
            // Remove the " DH" from the string
            let numericPart = TotalHtText.replace(' DH', '');

            // Convert the remaining string to a float
            let TotalHtFloat = parseFloat(numericPart);


            if(TotalHtFloat < totalPrixPaiement)
            {
                toastr.warning("le total HT est inférieur au prix payé", 'Attention');
                modePaiementTable = []; // Reset the array to empty
                return false;
            }
            else if(TotalHtFloat > totalPrixPaiement)
            {
                toastr.warning("le total HT est supérieur au prix payé", 'Attention');
                modePaiementTable = []; // Reset the array to empty
                return false;
            }
            if(TotalHtFloat == totalPrixPaiement)
            {

                let displayStatus = $('.DivCheque').css('display');
                let contentCheque = displayStatus === 'block';

                let data = {
                    'idclient'          : $('#IdClient').val(),
                    'ModePaiement'      : modePaiementArray,
                    '_token'            : csrf_token,
                    'totalPrixPaiement' : totalPrixPaiement
                };

                if (contentCheque)
                {
                    Object.assign(data, {
                        'numero'        : $('.numero').val(),
                        'datecheque'    : $('.datecheque').val(),
                        'datepromise'   : $('.datepromise').val(),
                        'montant'       : $('.montant').val(),
                        'type'          : $('.type').val(),
                        'bank'          : $('.bank').val(),
                        'name'          : $('.name').val(),
                    });
                }
                var date1Val = $('.datecheque').val();
                var date2Val = $('.datepromise').val();

                if (date1Val !== '' && date2Val !== '')
                {
                    var date1 = new Date(date1Val);
                    var date2 = new Date(date2Val);

                    if (date2 <= date1)
                    {
                        toastr.error('La date promise doit être supérieure à la date chèque.','Erreur');
                        $('.datepromise').val('');
                        return false;
                    }
                }

                $('.preloader').show();
                $.ajax({
                    type        : "post",
                    url         : StoreOrder,
                    data        : data,
                    dataType    : "json",
                    success: function (response)
                    {
                        $('.preloader').hide();
                        if(response.status == 200)
                        {
                            toastr.success("L'opération s'est terminée avec succès", 'Success');
                            modePaiementTable = []; // Reset the array to empty
                            $('.TableVente').DataTable().ajax.reload();
                            $('#AddOrder').modal("hide");
                            var newIdClient = $('#IdClient').val();
                            var newIdCompany = IdCompanyActive.id;
                            var typeVente    = null;
                            reloadTable(newIdClient, newIdCompany,typeVente);
                            updateTotals();

                            $('.TableModePaiement thead tr').each(function() {
                                $(this).find('input.TotalModePaiement').val(""); // Convert prix to float
                            });
                            $('.TableTmpVente').DataTable().clear().draw();
                            $('.TableStock').DataTable().clear().draw();
                            $('#TotalHT').text('0.00 DH');
                        }
                        else if(response.status == 442)
                        {
                            toastr.error("Le montant dans TABLEAU INFORMATION CHEQUE ne correspond pas au montant dans Tableau du mode paiement", 'Erreur');
                            $('.preloader').hide();
                        }
                    },
                    error: function (xhr, status, error) {
                        // Hide preloader on error
                        $('.preloader').hide();

                        toastr.error('Failed to process request: ' + error, 'Error');
                    }
                });
            }
        }
    });


    $('#BtnSaveVenteInvocie').on('click',function(e)
    {
        e.preventDefault();
        var lengthTableTmp = $('.TableTmpVente tbody tr td.dt-right').length;
        if (lengthTableTmp === 0)
        {
            toastr.warning("Une table panier vide ne peut pas être exploitée", 'Attention');
            return false;
        }
        else
        {
            // Check all .TotalModePaiement inputs for emptiness
            var allInputsValid = true;
            $('.TotalModePaiement').each(function() {
                if ($(this).val().trim() === '') {
                    allInputsValid = false;
                    return false; // Exit the loop early if any input is empty
                }
            });

            // If any input is empty, show alert and focus on the first empty input
            if (!allInputsValid) {

                toastr.warning("Veuillez saisir un montant valide pour le paiement", 'Attention');
                $('.TotalModePaiement').filter(function() {
                    return $(this).val().trim() === '';
                }).first().focus();
                return false;
            }
            let modePaiementTable = [];
            $('.TableModePaiement thead tr').each(function() {
                let mode = $(this).find('select[name="mode_paiement"]').val();
                let prix = parseFloat($(this).find('input.TotalModePaiement').val()); // Convert prix to float

                // Initialize the mode in modePaiementTable if it doesn't exist
                if (!modePaiementTable[mode]) {
                    modePaiementTable[mode] = {
                        'mode': mode,
                        'totalPrix': 0 // Initialize totalPrix for this mode
                    };
                }

                // Add prix to the totalPrix for this mode
                modePaiementTable[mode].totalPrix += prix;
            });

            // Convert modePaiementTable to an array of values
            let modePaiementArray = Object.values(modePaiementTable);

            let totalPrixPaiement = 0;
            // Iterate through modePaiementArray to calculate totalPrixPaiement
            for (let i = 0; i < modePaiementArray.length; i++)
            {
                // Access each mode's totalPrix from modePaiementArray
                totalPrixPaiement += modePaiementArray[i].totalPrix;
            }
            let TotalTTCText = $('#TotalTTC').text();
             // Remove the " DH" from the string
            let numericPart = TotalTTCText.replace(' DH', '');
             // Convert the remaining string to a float
            let TotalTTCFloat = parseFloat(numericPart);
            if(TotalTTCFloat < totalPrixPaiement)
            {
                toastr.warning("le total TTC est inférieur au prix payé", 'Attention');
                modePaiementTable = []; // Reset the array to empty
                return false;
            }
            else if(TotalTTCFloat > totalPrixPaiement)
            {
                toastr.warning("le total TTC est supérieur au prix payé", 'Attention');
                modePaiementTable = []; // Reset the array to empty
                return false;
            }
            if(TotalTTCFloat == totalPrixPaiement)
            {
                let displayStatus = $('.DivCheque').css('display');
                let contentCheque = displayStatus === 'block';

                let data = {
                    'idclient'          : $('#IdClient').val(),
                    'ModePaiement'      : modePaiementArray,
                    '_token'            : csrf_token,
                    'totalPrixPaiement' : totalPrixPaiement,
                    'isFacture'         : 'isFacture',
                };

                if (contentCheque) {
                    Object.assign(data, {
                        'numero'        : $('.numero').val(),
                        'datecheque'    : $('.datecheque').val(),
                        'datepromise'   : $('.datepromise').val(),
                        'montant'       : $('.montant').val(),
                        'type'          : $('.type').val(),
                        'bank'          : $('.bank').val(),
                        'name'          : $('.name').val(),
                    });
                }
                var date1Val = $('.datecheque').val();
                var date2Val = $('.datepromise').val();

                if (date1Val !== '' && date2Val !== '')
                {
                    var date1 = new Date(date1Val);
                    var date2 = new Date(date2Val);

                    if (date2 <= date1)
                    {
                        toastr.error('La date promise doit être supérieure à la date chèque.','Erreur');
                        $('.datepromise').val('');
                        return false;
                    }
                }

                $('.preloader').show();
                $.ajax({
                    type        : "post",
                    url         : StoreOrder,
                    data        : data,
                    dataType    : "json",
                    success: function (response)
                    {
                        $('.preloader').hide();
                        if(response.status == 200)
                        {
                            toastr.success("L'opération s'est terminée avec succès", 'Success');
                            modePaiementTable = []; // Reset the array to empty
                            $('.TableVente').DataTable().ajax.reload();
                            $('#AddOrder').modal("hide");
                            var newIdClient = $('#IdClient').val();
                            var newIdCompany = IdCompanyActive.id;
                            var typeVente    = null;
                            reloadTable(newIdClient, newIdCompany,typeVente);
                            updateTotals();
                            /* if ($.fn.dataTable.isDataTable('.TableStock')) {
                                $('.TableStock').DataTable().destroy();
                            } */
                            $('.TableModePaiement thead tr').each(function() {
                                $(this).find('input.TotalModePaiement').val(""); // Convert prix to float
                            });
                            $('.TableTmpVente').DataTable().clear().draw();
                            $('.TableStock').DataTable().clear().draw();
                            $('#TotalHT').text('0.00 DH');
                        }
                        else if(response.status == 442)
                        {
                            toastr.error("Le montant dans TABLEAU INFORMATION CHEQUE ne correspond pas au montant dans Tableau du mode paiement", 'Erreur');
                            $('.preloader').hide();
                        }
                    },
                    error: function (xhr, status, error) {
                        // Hide preloader on error
                        $('.preloader').hide();
                        toastr.error('Failed to process request: ' + error, 'Error');
                    }
                });
            }
        }
    });

    $('#OpenModelAddClient').on('click',function(e)
    {
        $('#AddClient').modal("show");
    });

    $('#BtnSaveClient').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'nom'        : $('#nom').val(),
            'prenom'     : $('#prenom').val(),
            'cin'        : $('#cin').val(),
            'adresse'    : $('#adresse').val(),
            'ville'      : $('#ville').val(),
            'plafonnier' : $('#plafonnier').val(),
            'phone'      : $('#phone').val(),
            '_token'     : csrf_token,
        };

        $.ajax({
            type: "post",
            url: StoreClient,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationClient').html("");
                    $('#AddClient').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableClient').DataTable().ajax.reload();
                    FunctiongetClientByCompany();
                }
                else if(response.status == 422)
                {
                    $('.ValidationClient').html("");
                    $('.ValidationClient').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationClient').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
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



    $('#TotalAccessoires').on('input', function(e)
    {
        e.preventDefault();
        var value = $(this).val().trim();
        // Remove leading zeros and ensure minimum value is 0
        if (value === '') {
            $(this).val('0');
        } else {
            $(this).val(value.replace(/^0+/, '')); // Remove leading zeros
        }

        let TotalAccessoireText = $(this).val();

        let convert_number_Accessoire_to_float = parseFloat(TotalAccessoireText);

        // Check for NaN to avoid invalid calculations
        if (isNaN(convert_number_Accessoire_to_float)) {
            convert_number_Accessoire_to_float = 0;
        }

        if (originalTotalHt === 0) {
            toastr.error('Je ne peux pas ajouter accessoire', 'Erreur');
            $(this).val("");
            return false;
        }

        if (TotalAccessoireText === null || TotalAccessoireText === "") {
            $('#TotalHT').text(originalTotalHt.toFixed(2) + ' DH');
            return;
        }
        // accessoire + ht
        let New_Total_tHT = convert_number_Accessoire_to_float + originalTotalHt;
        // convert tva to int
        let parsedValue = parseInt(tvaCalcul.replace(' %', ''));
        // calcul ttc
       /*  let New_Total_TTC = (New_Total_Tva + New_Total_tHT).toFixed(2); */

        // set value ht
        $('#TotalHT').text(New_Total_tHT.toFixed(2) + ' DH');



        // set value tva
        let New_Total_Tva = ( (New_Total_tHT *   parsedValue) / 100 ).toFixed(2);
        $('#CalculTva').text(New_Total_Tva + ' DH');




       // Calculate Total TTC
        let New_Total_TTC = (parseFloat(New_Total_tHT) + parseFloat(New_Total_Tva)).toFixed(2);
        $('#TotalTTC').text(New_Total_TTC + ' DH');

    });

    $('#TotalAccessoires').on('keyup', function(event) {
        var value = $(this).val().trim();
        if (value === '') {
            $(this).val('0');
        }
    });

    $('#TotalAccessoires').on('keydown', function(event) {
        if (event.key === 'Backspace' || event.key === 'Delete') {
            let TotalHtText = $('#TotalHT').text().replace(' DH', '');
            let convert_number_ht_to_float = parseFloat(TotalHtText);

            let TotalAccessoireText = $(this).val();
            let convert_number_Accessoire_to_float = parseFloat(TotalAccessoireText);

            if (TotalAccessoireText === null || TotalAccessoireText === "") {
                $('#TotalHT').text(convert_number_ht_to_float.toFixed(2) + ' DH');
                return;
            }

            let New_Total_tHT = (convert_number_ht_to_float - convert_number_Accessoire_to_float).toFixed(2) + ' DH';
            $('#TotalHT').text(New_Total_tHT);

            let parsedValue = parseInt(tvaCalcul.replace(' %', ''));


            let New_Total_Tva = ( (New_Total_tHT *   parsedValue) / 100 ).toFixed(2);
            $('#CalculTva').text(New_Total_Tva + ' DH');

            // Calculate new Total TTC
            let New_Total_TTC = (parseFloat(New_Total_tHT) + parseFloat(New_Total_Tva)).toFixed(2);
            $('#TotalTTC').text(New_Total_TTC + ' DH');
        }
    });
    $(document).on('change','.mode_paiement',function(e)
    {
        e.preventDefault();
        let modeArray = [];

        $('.TableModePaiement thead tr').each(function() {
            let mode = $(this).find('select[name="mode_paiement"] option:selected').text();
            if (mode) {
                modeArray.push(mode);
            }
        });
        console.log(modeArray);
        if (modeArray.includes('chèque')) {
            $('.DivCheque').css('display', 'block');
        } else {
            $('.DivCheque').css('display', 'none');
        }
    });
    function debounce(func, timeout = 300) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => { func.apply(this, args); }, timeout);
        };
    }

    $(document).on('input change keydown', '.TableTmpVente tbody .inputAccessoire', debounce(function(e) {
        var $input = $(this);

        // Check if the key pressed is backspace (8) or delete (46)
        if ((e.type === 'keydown' && (e.keyCode === 8 || e.keyCode === 46)) || e.type === 'input' || e.type === 'change') {
            setTimeout(function() {
                var id = $('.TableTmpVente').DataTable().row($input.closest('tr')).data().id;
                var accessoire = $input.val().trim() === '' ? 0 : $input.val().trim();

                $.ajax({
                    type: "GET",
                    url: changeAccessoireTmp,
                    data: {
                        id: id,
                        accessoire: accessoire
                    },
                    dataType: "json",
                    success: function(response)
                    {
                        if(response.status == 200)
                        {
                            var newIdClient = $('#IdClient').val();
                            var newIdCompany = IdCompanyActive.id; // Assuming company ID does not change
                            var typeVente = null;
                            // Destroy the existing DataTable
                            reloadTable(newIdClient, newIdCompany,typeVente);
                            updateTotals();
                        }
                    },
                    error: function(xhr, status, error)
                    {
                        // Handle error
                    }
                });
            }, 0);
        }
    }, 300));





});
