$(document).ready(function ()
{
    $(function ()
    {
        initializeDataTable('.TableSetting', FetchSetting);
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
                columns: [
                    {data: 'numero_bon'     , name: 'numero_bon'},
                    {data: 'name_product'   , name: 'name_product'},
                    {data: 'type'           , name: 'type'},
                    {data: 'qte'            , name: 'qte'},
                    {data: 'convert'        , name: 'convert'},
                    {data: 'title'          , name: 'title'},
                    {data: 'user'           , name: 'user'},
                    {data: 'creer_le'       , name: 'creer_le'},
                    {data: 'action'         , name: 'action', orderable: false, searchable: false}
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

                var data = table.row($(this).closest('tr')).data(); // Access DataTables API to get row data

                var idSetting =
                {
                    'id' : data.id,
                };
                $.ajax({
                    type: "get",
                    url: getSettingByID,
                    data: idSetting,
                    dataType: "json",
                    success: function (response)
                    {
                        if(response.status == 200)
                        {

                            $('#DropDownCategoryEdit').val(response.data[0].idcategory).change();
                            $('.DrowDownBonEdit').val(response.data[0].idbon);
                            var id         = $(this).attr('value');
                            var name_product = data.name_product;
                            var type         = data.type;
                            var convert      = data.convert;
                            $('#name_productEdit').val(name_product);
                            $('#uniteEdit').val(type);
                            $('#convertEdit').val(convert);
                            $('#BtnEditSetting').attr('data-value',data.id);
                            $('#EditSetting').modal("show");
                        }
                    }
                });





            });


            $(selector + ' tbody').on('click', '.trash', function(e)
            {
                e.preventDefault();
                var id   = $(this).attr('value');
                swal({
                        title: "es-tu sûr de supprimer cette paramètre",
                        text: "Une fois supprimée, vous ne pourrez plus récupérer cette paramètre !",
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
                                'id'         : id,
                                '_token'     : csrf_token,
                            };
                            $.ajax({
                                type: "post",
                                url: TrashSetting,
                                data: data,

                                dataType: "json",
                                success: function (response)
                                {
                                    if(response.status == 200)
                                    {
                                        swal("Votre paramètre a été supprimée !", {
                                            icon: "success",
                                        });
                                        $('.TableSetting').DataTable().ajax.reload();
                                    }

                                }
                            });

                        }
                        else
                        {
                            swal("Votre paramètre est sécurisée !");
                        }
                    });

                });



        }



    });

    $('#BtnShowModalAddSetting').on('click',function(e)
    {
        e.preventDefault();
        $('#AddSetting').modal("show");
    });

    $('#BtnSaveSetting').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'idcategory'        : $('#DropDownCategory').val(),
            'idstock'           : $('#name_products').val(),
            'name_product'      : $('#name_products option:selected').text(),
            'unite'             : $('#unite').val(),
            'conversion_rate'   : $('#conversion_rate').val(),
            '_token'            : csrf_token,
        };

        $.ajax({
            type: "post",
            url: StoreSetting,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    if(response.status == 200)
                    {
                        $('.ValidationSetting').html("");
                        $('#AddSetting').modal("hide");
                        toastr.success(response.message, 'Success');
                        $('.TableSetting').DataTable().ajax.reload();
                        $('#unite').val("");
                        $('#conversion_rate').val("");
                    }
                    else if(response.status == 422)
                    {
                        $('.ValidationSetting').html("");
                        $('.ValidationSetting').addClass('alert alert-danger');
                        $.each(response.errors, function(key, list_err) {
                            $('.ValidationSetting').append('<li>'+list_err+'</li>');
                        });
                    }
                }
            }
        });
    });


    $('#BtnEditSetting').on('click',function(e)
    {
        e.preventDefault();


        var data =
        {
            'idcategory'        : $('#DropDownCategoryEdit').val(),
            'idstock'           : $('#name_productEdit').val(),
            'name_product'      : $('#name_productEdit option:selected').text(),
            'unite'             : $('#uniteEdit').val(),
            'conversion_rate'   : $('#convertEdit').val(),
            '_token'            : csrf_token,
            'id'                : $(this).attr('data-value'),
        };
        $.ajax({
            type: "post",
            url: UpdateSetting,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    if(response.status == 200)
                    {
                        $('.ValidationSettingEdit').html("");
                        $('#EditSetting').modal("hide");
                        toastr.success(response.message, 'Success');
                        $('.TableSetting').DataTable().ajax.reload();
                    }
                    else if(response.status == 422)
                    {
                        $('.ValidationSettingEdit').html("");
                        $('.ValidationSettingEdit').addClass('alert alert-danger');
                        $.each(response.errors, function(key, list_err) {
                            $('.ValidationSettingEdit').append('<li>'+list_err+'</li>');
                        });
                    }
                }
            }
        });
    });
    function initializeSelect2(modalId) {
        $(modalId).find('.DrowDownBon').select2({
            dropdownParent: $(modalId), // Ensure it attaches to the modal
            placeholder: "Veuillez sélectionner N° bon",
            allowClear: true
        });
    }
    $('.modal').on('shown.bs.modal', function () {
        var modalId = '#' + $(this).attr('id');
        initializeSelect2(modalId);
    });
   /*  $('.DrowDownBon').select2({
        dropdownParent: $('#AddSetting'), // Ensure it attaches to the modal
        placeholder: "Veuillez sélectionner N° bon",
        allowClear: true
    }); */
    $('.DrowDownBon').on('change',function(e)
    {
        e.preventDefault();
        if($(this).val() == 0)
        {
            toastr.error('Veuillez sélectionner N° bon','Erreur');
            return false;
        }
        if($('#DropDownCategory').val() == 0)
        {
            toastr.error('Veuillez sélectionner le catégorie','Erreur');
            return false;
        }
        else
        {
            var data =
            {
                'idbon'   : $(this).val(),
                'idcategory'  : $('#DropDownCategory').val(),
            };
            $.ajax({
                type: "get",
                url: getNameProductByBonAndCategory,
                data: data,
                dataType: "json",
                success: function (response)
                {
                    if(response.status == 200)
                    {
                        $('#name_products').empty();
                        $.each(response.data, function (index, value)
                        {
                            $('#name_products').append('<option value="' + value.idstock + '">'+value.name+'</option>')
                        });
                    }
                }
            });
        }
    });


    $('.DrowDownBonEdit').on('change',function(e)
    {
        e.preventDefault();
        if($(this).val() == 0)
        {
            toastr.error('Veuillez sélectionner N° bon','Erreur');
            return false;
        }
        if($('#DropDownCategoryEdit').val() == 0)
        {
            toastr.error('Veuillez sélectionner le catégorie','Erreur');
            return false;
        }
        else
        {
            var data =
            {
                'idbon'   : $(this).val(),
                'idcategory'  : $('#DropDownCategoryEdit').val(),
            };
            $.ajax({
                type: "get",
                url: getNameProductByBonAndCategory,
                data: data,
                dataType: "json",
                success: function (response)
                {
                    if(response.status == 200)
                    {
                        $('#name_productEdit').empty();
                        $.each(response.data, function (index, value)
                        {
                            $('#name_productEdit').append('<option value="' + value.idstock + '">'+value.name+'</option>')
                        });
                    }
                }
            });
        }
    });


});
