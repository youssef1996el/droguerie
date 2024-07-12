$(document).ready(function ()
{
    $(function ()
    {
        initializeDataTable('.TableCompany', getCompany);
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
                    {data: 'title', name: 'title'},
                    {data: 'status', name: 'status'},
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
                var IdCompany   = $(this).attr('value');
                $.ajax({
                    type: "get",
                    url: ShowCompany,
                    data:
                    {
                        IdCompany : IdCompany,
                    },
                    dataType: "json",
                    success: function (response)
                    {
                        if(response.status  == 200)
                        {
                            $('#EditCompany').modal("show");
                            $('#Edittitle').val(response.data.title);
                            $('#BtnEditCompany').attr('data-value',response.data.id);
                            $('#status').append('<option value="Active">Active</option>');
                        }
                    }
                });
            });
        }
    });
    $('#BtnShowModalAddCompany').on('click',function(e)
    {
        e.preventDefault();
        $('#AddCompany').modal("show");
    });

    $('#BtnSaveCompany').on('click',function(e)
    {
        e.preventDefault();

        var data  =
        {
            'title'         : $('#title').val(),
            '_token'        : csrf_token,

        };
        $.ajax({
            type: "post",
            url: StoreCompany,
            data:data,

            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationCompany').html("");
                    $('#AddCompany').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableCompany').DataTable().ajax.reload();
                    $('#companyStatus').load(window.location.href + ' #companyStatus');
                }
                else if(response.status == 422)
                {
                    $('.ValidationCompany').html("");
                    $('.ValidationCompany').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationCompany').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });

    $('#BtnEditCompany').on('click',function(e)
    {
        e.preventDefault();
        var data  =
        {
            'title'         :$('#Edittitle').val(),
            'status'        :$('#status').val(),
            '_token'        : csrf_token,
            'IdCompany'     : $(this).attr('data-value'),
        };

        $.ajax({
            type: "post",
            url: EditCompany,
            data:data,

            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationCompanyEdit').html("");
                    $('#EditCompany').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableCompany').DataTable().ajax.reload();
                    $('#companyStatus').load(window.location.href + ' #companyStatus');
                }
                else if(response.status == 422)
                {
                    $('.ValidationCompanyEdit').html("");
                    $('.ValidationCompanyEdit').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationCompanyEdit').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });

    });

});
