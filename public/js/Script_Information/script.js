$(document).ready(function ()
{
    $(function ()
    {
        initializeDataTable('.TableInformation', FetchInformation);
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
                    {data: 'title', name: 'title'},
                    {data: 'ice', name: 'ice'},
                    {data: 'cnss', name: 'cnss'},
                    {data: 'address', name: 'address' },
                    {data: 'rc', name: 'rc'},
                    {data: 'if', name: 'if'},
                    {data: 'phone', name: 'phone'},
                    {data: 'fix', name: 'fix'},
                    {data: 'title_company', name: 'title_company'},
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
            $(selector + " tbody").on('click', '.edit', function(e) {
                e.preventDefault();
                var data = table.row($(this).closest('tr')).data(); // Access DataTables API to get row data
                $('#titleEdit').val(data.title);
                $('#ICEEdit').val(data.ice);
                $('#CNSSEdit').val(data.cnss);
                $('#RCEdit').val(data.rc);
                $('#IFEdit').val(data.if);
                $('#AdresseEdit').val(data.address); // corrected typo here ('tlephone' to 'telephone')
                $('#phoneEdit').val(data.phone);
                $('#FixEdit').val(data.fix);
                $('#BtnEditInfo').attr('data-value', data.id);
                $('#ModelUpdateInformation').modal("show");
            });


        }

    });
    $('#BtnShowModalAddInformation').on('click',function(e)
    {
        e.preventDefault();
        $('#ModelAddInformation').modal("show");
    });
    $('#BtnSaveInfo').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'title'         : $('#title').val(),
            'ice'           : $('#ICE').val(),
            'cnss'          : $('#CNSS').val(),
            'rc'            : $('#RC').val(),
            'if'            : $('#IF').val(),
            'address'       : $('#Adresse').val(),
            'phone'         : $('#phone').val(),
            'fix'           : $('#Fix').val(),
            '_token'     : csrf_token,
        };
        $.ajax({
            type: "post",
            url: StoreInformation,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationInformation').html("");
                    $('#ModelAddInformation').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableInformation').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationInformation').html("");
                    $('.ValidationInformation').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationInformation').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });


    $('#BtnEditInfo').on('click',function(e)
    {
        e.preventDefault();
        var data =
        {
            'title'         : $('#titleEdit').val(),
            'ice'           : $('#ICEEdit').val(),
            'cnss'          : $('#CNSSEdit').val(),
            'rc'            : $('#RCEdit').val(),
            'if'            : $('#IFEdit').val(),
            'address'       : $('#AdresseEdit').val(),
            'phone'         : $('#phoneEdit').val(),
            'fix'           : $('#FixEdit').val(),
            '_token'        : csrf_token,
            'id'            : $(this).attr('data-value')
        };
        $.ajax({
            type: "post",
            url: UpdateInformation,
            data: data,
            dataType: "json",
            success: function (response)
            {
                if(response.status == 200)
                {
                    $('.ValidationInformationEdit').html("");
                    $('#ModelUpdateInformation').modal("hide");
                    toastr.success(response.message, 'Success');
                    $('.TableInformation').DataTable().ajax.reload();
                }
                else if(response.status == 422)
                {
                    $('.ValidationInformationEdit').html("");
                    $('.ValidationInformationEdit').addClass('alert alert-danger');
                    $.each(response.errors, function(key, list_err) {
                        $('.ValidationInformationEdit').append('<li>'+list_err+'</li>');
                    });
                }
            }
        });
    });

});
