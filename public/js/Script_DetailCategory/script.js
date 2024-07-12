$(document).ready(function ()
{
    $(function ()
    {
        initializeDataTable('.TableDetailCategory', fetchDetailCategory);
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
                    {data: 'name', name: 'name'},
                    {data: 'namecategory', name: 'namecategory'},
                    {data: 'title', name: 'namecategory'},
                    {data: 'idcategory', name: 'idcategory', visible: false}, // add idcategory here
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
            $(selector + ' tbody').on('click', '.edit', function(e)
            {
                e.preventDefault();
                var data = table.row($(this).closest('tr')).data(); // Access DataTables API to get row data
                var IdDetailCategory = $(this).attr('value');
                var name = data.name;
                var idcategory = data.idcategory;

                $('#nameEdit').val(name);
                $('#idcategoryEdit').val(idcategory).change();
                $('#BtnUpdateDetailCategory').attr('data-value',IdDetailCategory);
                $('#EditDetailCategory').modal("show");

            });

            $(selector + ' tbody').on('click', '.trash', function(e)
            {
                e.preventDefault();
                var IdDetailCategory   = $(this).attr('value');
                swal({
                        title: "es-tu sûr de supprimer cette detail catégorie",
                        text: "Une fois supprimée, vous ne pourrez plus récupérer cette detail catégorie !",
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
                                'id'         : IdDetailCategory,
                                '_token'     : csrf_token,
                            };
                            $.ajax({
                                type: "post",
                                url: TrashDetailCategory,
                                data: data,

                                dataType: "json",
                                success: function (response)
                                {
                                    if(response.status == 200)
                                    {
                                        swal("Votre detail catégorie a été supprimée !", {
                                            icon: "success",
                                        });
                                        $('.TableDetailCategory').DataTable().ajax.reload();
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
    $('#BtnShowModalAddDetailCategory').on('click',function(e)
    {
        e.preventDefault();
        $('#AddDetailCategory').modal('show');
    });
    $('#BtnUpdateDetailCategory').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'name'          : $('#nameEdit').val(),
            'idcategory' : $('#idcategoryEdit').val(),
            '_token'     : csrf_token,
            'id'         : $(this).attr('data-value'),
        };
        $.ajax({
            type: "post",
            url: UpdateDetailCategory,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationEditDetailCategory').html("");
                    $('#EditDetailCategory').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableDetailCategory').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationEditDetailCategory').html("");
                    $('.ValidationEditDetailCategory').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationEditDetailCategory').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });
    $('#BtnSaveDetailCategory').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'name' : $('#name').val(),
            'idcategory' : $('#idcategory').val(),
            '_token'     : csrf_token,
        };
        $.ajax({
            type: "post",
            url: StoreDetailCategory,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationDetailCategory').html("");
                    $('#AddDetailCategory').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableDetailCategory').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationDetailCategory').html("");
                    $('.ValidationDetailCategory').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationDetailCategory').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });
});
