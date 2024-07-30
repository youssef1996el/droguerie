$(document).ready(function ()
{
    $(function ()
    {
        initializeDataTable('.TableCategory', FetchCategoryByCompanyActive);
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
                    {data: 'title', name: 'title'},
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
                var IdCategory   = $(this).attr('value');
                var name         = $(this).closest('tr').find('td:eq(0)').text();
                $('#nameEdit').val(name);
                $('#BtnUpdateCategory').attr('data-value',IdCategory);
                $('#EditCategory').modal("show");

            });

            $(selector + ' tbody').on('click', '.trash', function(e)
            {
                e.preventDefault();
                var IdCategory   = $(this).attr('value');
                swal({
                    title: "es-tu sûr de supprimer cette catégorie",
                    text: "Une fois supprimée, vous ne pourrez plus récupérer cette catégorie !",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                  })
                  .then((willDelete) => {
                    if (willDelete)
                    {
                        var data =
                        {
                            'id' : IdCategory,
                            '_token'     : csrf_token,
                        };
                        $.ajax({
                            type: "post",
                            url: TrashCategory,
                            data: data,

                            dataType: "json",
                            success: function (response)
                            {
                                if(response.status == 200)
                                {
                                    swal("Votre catégorie a été supprimée !", {
                                        icon: "success",
                                    });
                                    $('.TableCategory').DataTable().ajax.reload();
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
                        swal("Votre catégorie est sécurisée !");
                    }
                });

            });
        }



    });

    $('#BtnShowModalAddCategory').on('click',function(e)
    {
        e.preventDefault();
        $('#AddCategory').modal('show');
    });
    $('#BtnSaveCategory').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'name'   : $('#name').val(),
            '_token'     : csrf_token,
        };
        $.ajax({
            type: "post",
            url: StoreCategory,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationCategory').html("");
                    $('#AddCategory').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableCategory').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationCategory').html("");
                    $('.ValidationCategory').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationCategory').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });


    $('#BtnUpdateCategory').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'name'   : $('#nameEdit').val(),
            '_token'     : csrf_token,
            'id'       : $(this).attr('data-value'),
        };
        $.ajax({
            type: "post",
            url: UpdateCaegory,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationEditCategory').html("");
                    $('#EditCategory').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableCategory').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationEditCategory').html("");
                    $('.ValidationEditCategory').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationEditCategory').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });


});
