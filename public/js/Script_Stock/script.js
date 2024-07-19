$(document).ready(function ()
{
    var OrignaleListProduct = [];
    var availableTags = [];
    $.each(listNameProducts, function (index, value)
    {

        OrignaleListProduct.push(value);
    });

   /*  $( function() {
        availableTags= OrignaleListProduct;
        $( ".name" ).autocomplete({
          source: availableTags
        });
      } ); */

      function initializeAutocomplete(element) {
        $(element).autocomplete({
          source: availableTags
        });
      }

    $('#BtnShowModalAddStock').on('click', function(e)
    {
        e.preventDefault();
        $('#AddStock').modal("show");
    });



    $('#BtnSaveStock').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'numero_bon'        : $('#numero_bon').val(),
            'date'              : $('#date').val(),
            'numero'            : $('#numero').val(),
            'commercial'        : $('#commercial').val(),
            'modePaiement'      : $('#modePaiement').val(),
            'matricule'         : $('#matricule').val(),
            'chauffeur'         : $('#chauffeur').val(),
            'cin'               : $('#cin').val(),
            'name'              : $('.name').val(),
            'iddetailcategory'  : $('#DropDownUnite').val(),
            'idbonentre'        : $('#numero_bon').val(),
            '_token'            : csrf_token,

        };

        // Gather dynamic rows
        var names = [];
        var prices = [];
        var qtes = [];
        var qte_companies = [];
        var categories = [];
        var qte_notifications =[];

        $('#productTableBody tr').each(function() {
            var name = $(this).find('.name').val();
            var price = $(this).find('.price').val();
            var qte = $(this).find('.qte').val();
            var qte_company = $(this).find('.qte_company').val();
            var category = $(this).find('.DropDownCategory').val();
            var qte_notification = $(this).find('.qte_notification').val();

            if(name && price && qte && qte_company && category && qte_notification) {
                names.push(name);
                prices.push(price);
                qtes.push(qte);
                qte_companies.push(qte_company);
                categories.push(category);
                qte_notifications.push(qte_notification);
            }
        });

        data['name'] = names;
        data['price'] = prices;
        data['qte'] = qtes;
        data['qte_company'] = qte_companies;
        data['DropDownCategory'] = categories;
        data['qte_notification'] = qte_notifications;

        var errorFound = false;

        if (data['qte'].length === data['qte_notification'].length) {
            for (var i = 0; i < data['qte'].length; i++) {
                if (parseFloat(data['qte'][i]) < parseFloat(data['qte_notification'][i])) {
                    errorFound = true;
                    break;
                }
            }
        } else {
            toastr.error("Les longueurs des tableaux « qte » et « qte notification » ne correspondent pas.",'Erreur');
            return false;
        }

        if (errorFound)
        {
            toastr.error("Certaines quantités sont inférieures aux quantités de notification.",'Erreur');
            return false;
        }
        $.ajax({
            type: "post",
            url: StoreStock,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationStock').html("");
                    $('#AddStock').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableStock').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationStock').html("");
                    $('.ValidationStock').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationStock').append('<li>'+list_err+'</li>');
                    });
                }
                else if(response.status == 400)
                {
                    toastr.error(response.message, 'Error');
                }
            }
            ,
            error: function (xhr, status, error) {
                toastr.error('Failed to process request: ' + error, 'Error');
            }
        });
    });

    $(function ()
    {
        initializeDataTable('.TableStock', getStock);
        function initializeDataTable(selector, url)
        {
            var table = $(selector).DataTable({
                processing: true,
                serverSide: true,
                ordering: [[1, 'asc']],
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
                        className: 'details-control',
                        orderable: false,
                        searchable: false,
                        data: null,
                        defaultContent: '<i class="ti ti-plus border rounded-2 p-2 bg-success d-flex justify-content-center cursor-pointer text-white"></i>'
                    },
                    {data: 'numero_bon'         , name: 'numero_bon'},
                    {data: 'date'               , name: 'date'},
                    {data: 'numero'             , name: 'numero'},
                    {data: 'commercial'         , name: 'commercial'},
                    {data: 'title_company'      , name: 'title_company'},
                    {data: 'name'               , name: 'name'},
                    {data: 'created_at'         , name: 'created_at'},
                    {data: 'action'     , name: 'action', orderable: false, searchable: false},

                ],
                columnDefs:
                [
                    { className: "dt-right", targets: [2, 1] }
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
             // Add event listener for opening and closing details
            $(selector + ' tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    $(this).html('<i class="ti ti-plus border rounded-2 p-2 bg-success d-flex justify-content-center cursor-pointer text-white"></i>'); // Change icon to plus
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                    $(this).html('<i class="ti ti-minus border rounded-2 p-2 bg-danger d-flex justify-content-center cursor-pointer text-white"></i>'); // Change icon to minus
                }

            });
            function format(d) {
                console.log(d); // Log the data to inspect it in the console

                // Initialize the table structure
                var html = '<table class="table table-bordered table-striped">' +
                                '<thead class="text-nowrap fs-2">' +
                                    '<tr>' +
                                        '<th>Produit</th>' +
                                        '<th>Quantité Société</th>' +
                                        '<th>Quantité Stock</th>' +
                                        '<th>Quantité min de stock</th>' +
                                        '<th>Prix</th>' +
                                        '<th>Chauffeur</th>' +
                                        '<th>N° Immatriculation</th>' +
                                        '<th>C.I.N</th>' +
                                    '</tr>' +
                                '</thead>' +
                                '<tbody>';

                // Loop through each product in the 'product' array
                for (var i = 0; i < d.product.length; i++) {
                    var cinContent = (d.cin !== null) ? '<td>' + d.cin[i] + '</td>' : '<td></td>';

                    // Append row for each product
                    html += '<tr>' +
                                '<td>' + d.product[i] + '</td>' +
                                '<td>' + d.qte_company[i] + '</td>' +
                                '<td>' + d.qte_stock[i] + '</td>' +
                                '<td>' + d.qte_notification[i] + '</td>' +
                                '<td>' + d.price[i] + ' DH</td>' +
                                '<td>' + d.chauffeur[i] + '</td>' +
                                '<td>' + d.matricule[i] + '</td>' +
                                cinContent +
                            '</tr>';
                }


                html += '</tbody></table>';

                return html; // Return the constructed HTML for DataTables to display
            }




            $(selector + ' tbody').on('click', '.edit', function(e)
            {
                e.preventDefault();
                var idBon   = $(this).attr('value');
                $.ajax({
                    type: "get",
                    url: GetRowSelectedByTable,
                    data:
                    {
                        idBon : idBon,
                    },
                    dataType: "json",
                    success: function (response)
                    {
                        if(response.status  == 200)
                        {
                            $('#EditStock').modal("show");

                            $('#numero_bonEdit').val(response.data[0].numero_bon);
                            $('#dateEdit').val(response.data[0].date);
                            $('#numeroEdit').val(response.data[0].numero);
                            $('#commercialEdit').val(response.data[0].commercial);
                            $('#modePaiementEdit').val(response.data[0].mode_paiement);
                            $('#matriculeEdit').val(response.data[0].matricule);
                            $('#chauffeurEdit').val(response.data[0].chauffeur);
                            $('#cinEdit').val(response.data[0].cin);
                            $('.TableStockEdit').find('tbody').html("");
                            $.each(response.data, function (index, value) {
                                var options = '';
                                $.each(CategoryCompanyActive, function (catIndex, category) {
                                    // If the category id is equal to value.idcategory, mark it as selected
                                    if (category.id === value.idcategory) {
                                        options += `<option value="${category.id}" selected>${category.name}</option>`;
                                    } else {
                                        options += `<option value="${category.id}">${category.name}</option>`;
                                    }
                                });
                                console.log(value.product);
                                $('.TableStockEdit').find('tbody').append(`
                                    <tr>
                                        <td>
                                            <input type="text" id="name" name="name[]" class="form-control name" placeholder="(obligatoire)" value="${value.product}" required>
                                        </td>
                                        <td>
                                            <select name="DropDownCategory[]" id="DropDownCategory" class="form-select DropDownCategory">
                                                ${options}
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" id="price" name="price[]" class="form-control price" value="${value.price}" placeholder="(obligatoire)" required>
                                        </td>
                                        <td>
                                            <input type="number" min="1" id="qte" name="qte[]" class="form-control qte" value="${value.qte_stock}" placeholder="(obligatoire)" required>
                                        </td>
                                        <td>
                                            <input type="number" min="1" id="qte_company" name="qte_company[]" value="${value.qte_company}" class="form-control qte_company" placeholder="(obligatoire)" required>
                                        </td>
                                        <td>
                                            <input type="number" min="0" id="qte_notification" name="qte_notification[]" value="${value.qte_notification}" class="form-control qte_notification" placeholder="(obligatoire)" required>
                                        </td>
                                        <td>

                                        </td>
                                    </tr>`);
                            });


                            $('#BtnEditStock').attr('data-value',response.data[0].idbon);




                        }
                    }
                });
            });

            // trash delete stock
            $(selector + ' tbody').on('click','.trash',function(e)
            {
                e.preventDefault();
                var idBon = $(this).attr('value');
                swal({
                    title: "es-tu sûr de supprimer cette stock",
                    text: "Une fois supprimée, vous ne pourrez plus récupérer cette stock !",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) =>
                {
                    if (willDelete)
                    {
                        var data =
                        {
                            'id' : idBon,
                            '_token'     : csrf_token,
                        };
                        $.ajax({
                            type: "post",
                            url: TrashStock,
                            data: data,

                            dataType: "json",
                            success: function (response)
                            {
                                if(response.status == 200)
                                {
                                    swal("Votre stcok a été supprimée !", {
                                        icon: "success",
                                    });
                                    $('.TableStock').DataTable().ajax.reload();
                                }
                                else if(response.status ==400)
                                {
                                    swal("Oops !", response.message, "error");
                                }
                            }
                        });

                    }
                    else
                    {
                        swal("Votre stock est sécurisée !");
                    }
                });

            });

        }
    });

    $('#BtnEditStock').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'numero_bon'    : $('#numero_bonEdit').val(),
            'date'          : $('#dateEdit').val(),
            'numero'        : $('#numeroEdit').val(),
            'commercial'    : $('#commercialEdit').val(),
            'mode_paiement' : $('#modePaiementEdit').val(),
            'matricule'     : $('#matriculeEdit').val(),
            'chauffeur'     : $('#chauffeurEdit').val(),
            'cin'           : $('#cinEdit').val(),
            '_token'        : csrf_token,
            'idbon'        : $(this).attr('data-value'),

        };
        var names = [];
        var prices = [];
        var qtes = [];
        var qte_companies = [];
        var categories = [];
        var qte_notifications = [];
        $('#productTableBodyEdit tr').each(function() {
            var name = $(this).find('.name').val();
            var price = $(this).find('.price').val();
            var qte = $(this).find('.qte').val();
            var qte_company = $(this).find('.qte_company').val();
            var category = $(this).find('.DropDownCategory').val();
            var qte_notification = $(this).find('.qte_notification').val();

            if(name && price && qte && qte_company && category && qte_notification) {
                names.push(name);
                prices.push(price);
                qtes.push(qte);
                qte_companies.push(qte_company);
                categories.push(category);
                qte_notifications.push(qte_notification);
            }
        });
        data['name'] = names;
        data['price'] = prices;
        data['qte'] = qtes;
        data['qte_company'] = qte_companies;
        data['DropDownCategory'] = categories;
        data['qte_notification'] = qte_notifications;
        $.ajax({
            type: "post",
            url: UpdateStock,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationStockEdit').html("");
                    $('#EditStock').modal("hide");
                    toastr.success(response.message,'Success');
                    $('.TableStock').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationStockEdit').html("");
                    $('.ValidationStockEdit').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationStockEdit').append('<li>'+list_err+'</li>');
                    });
                }
                else if(response.status == 400)
                {
                    toastr.error(response.message, 'Error');
                }
            }
            ,
            error: function (xhr, status, error) {
                toastr.error('Failed to process request: ' + error, 'Error');
            }
        });
    });

    $('.add-row').on('click',function(e)
    {
        e.preventDefault();
        const newRow = `
            <tr>
                <td>
                    <input type="text" id="name" name="name[]" class="form-control name" placeholder="(obligatoire)" autocomplete="on" required>
                </td>
                <td>
                    <select name="DropDownCategory[]" id="DropDownCategory" class="form-select DropDownCategory">
                        <option value="0">Veuillez sélectionner le catégorie</option>
                    </select>
                </td>
                <td>
                    <input type="number" id="price" name="price[]" class="form-control price" placeholder="(obligatoire)" required>
                </td>
                <td>
                    <input type="number" min="1" id="qte" name="qte[]" class="form-control qte" placeholder="(obligatoire)" required>
                </td>
                <td>
                    <input type="number" min="1" id="qte_company" name="qte_company[]" class="form-control qte_company" placeholder="(obligatoire)" required>
                </td>
                <td>
                    <input type="number" min="0" id="qte_notification" name="qte_notification[]" class="form-control qte_notification" placeholder="(obligatoire)" required>
                </td>
                <td>
                    <button class="btn btn-sm btn-danger mt-2 remove-row">-</button>
                </td>
            </tr>`;
            const appendedRow = $(newRow).appendTo('#productTableBody');
            populateCategories(appendedRow.find('.DropDownCategory'));
    });

    $('.add-rowAppend').on('click',function(e)
    {
        e.preventDefault();
        const newRow = `
            <tr>
                <td>
                    <input type="text" id="name" name="name[]" class="form-control name" placeholder="(obligatoire)" autocomplete="on" required>
                </td>
                <td>
                    <select name="DropDownCategory[]" id="DropDownCategory" class="form-select DropDownCategory">
                        <option value="0">Veuillez sélectionner le catégorie</option>
                    </select>
                </td>
                <td>
                    <input type="number" id="price" name="price[]" class="form-control price" placeholder="(obligatoire)" required>
                </td>
                <td>
                    <input type="number" min="1" id="qte" name="qte[]" class="form-control qte" placeholder="(obligatoire)" required>
                </td>
                <td>
                    <input type="number" min="1" id="qte_company" name="qte_company[]" class="form-control qte_company" placeholder="(obligatoire)" required>
                </td>
                <td>
                    <input type="number" min="0" id="qte_notification" name="qte_notification[]" class="form-control qte_notification" placeholder="(obligatoire)" required>
                </td>
                <td>
                    <button class="btn btn-sm btn-danger mt-2 remove-rowAppend">-</button>
                </td>
            </tr>`;
            const appendedRow = $(newRow).appendTo('#productTableBodyEdit');
            populateCategories(appendedRow.find('.DropDownCategory'));
    });

    // Function to populate the category dropdowns
    function populateCategories(dropdown)
    {
        CategoryCompanyActive.forEach(category => {
          $(dropdown).append(new Option(category.name, category.id));
        });
    }
    populateCategories('.DropDownCategory');
    initializeAutocomplete('.name');
    $('#productTableBody').on('click', '.remove-row', function(e)
    {
        e.preventDefault();
        $(this).closest('tr').remove();
    });

    $('#productTableBodyEdit').on('click','.remove-rowAppend',function(e)
    {
        e.preventDefault();
        $(this).closest('tr').remove();
    });



});
