@extends('Dashboard.app')
@section('content')
<div class="container-fluid">
    <div class="card card-body py-3">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="d-sm-flex align-items-center justify-space-between">
                    <h4 class="mb-4 mb-sm-0 card-title">Gestion de Personnel</h4>
                    <nav aria-label="breadcrumb" class="ms-auto">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item d-flex align-items-center">
                                <a class="text-muted text-decoration-none d-flex" href="{{url('/home')}}">
                                    <iconify-icon icon="solar:home-2-line-duotone" class="fs-6"></iconify-icon>
                                </a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <span class="badge fw-medium fs-2 bg-primary-subtle text-primary">
                                    Suivi Personnel
                                </span>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="widget-content searchable-container list">
        <div class="card card-body">
            <div class="row">
                <div class="col-md-4 col-xl-4">

                    <select name="" id="IdPersonnel" class="form-control">
                        @if (isset($id))
                            @foreach ($personnel as $item)
                                <option value="{{$item->id}}">{{$item->nom ." ".$item->prenom}}</option>
                            @endforeach
                        @else
                            <option value="0">veuillez sélectionner le personnel</option>
                            @foreach ($personnel as $item)
                                <option value="{{$item->id}}">{{$item->nom ." ".$item->prenom}}</option>
                            @endforeach
                        @endif

                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2">Fiche personnel</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped TablePersonnel">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Adresse</th>
                        <th>C.I.N</th>
                        <th>Ville</th>
                        <th>Téléphone</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                @if (isset($id))
                    <tbody>
                        @foreach ($dataLine as $item)
                            <tr>
                                <td>{{$item->nom}}</td>
                                <td>{{$item->prenom}}</td>
                                <td>{{$item->adresse}}</td>
                                <td>{{$item->cin}}</td>
                                <td>{{$item->ville}}</td>
                                <td>{{$item->telephone}}</td>
                                <td>{{$item->total}} DH</td>
                                <td>{{$item->datereglement}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    @else
                    <tbody></tbody>
                @endif

            </table>
        </div>
    </div>
    <script>
        $(document).ready(function ()
        {
            $('#IdPersonnel').select2({

                placeholder: "veuillez sélectionner le personnel",
                allowClear: true
            });
            @if (isset($id))
            // This JavaScript code will only be output if $id is set in PHP
                console.log('ID is set: {{ $id }}');


            @else
                $('#IdPersonnel').on('change',function(e)
                {
                    e.preventDefault();
                    if($(this).val() == 0)
                    {
                        toastar.error('veuillez sélectionner le client','Erreur');
                        return false;
                    }
                    else
                    {
                        var getFichePersonnelByPersonnel = "{{ url('getFichePersonnelByPersonnel') }}";
                        initializeDataTable('.TablePersonnel', getFichePersonnelByPersonnel);


                    }
                });
            @endif
            function initializeDataTable(selector, url)
            {
                // Destroy existing DataTable instance if it exists
                if ($.fn.DataTable.isDataTable(selector))
                {
                    $(selector).DataTable().destroy();
                }

                // Initialize DataTable
                var table = $(selector).DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: [[1, 'asc']],
                    ajax:
                    {
                        url: url,
                        data: function (params) {
                            params.IdPersonnel = $('#IdPersonnel').val();
                            return params;
                        },
                        dataSrc: function (json) {
                            if (json.data.length === 0) {
                                $('.paging_full_numbers').css('display', 'none');
                            }
                            return json.data;
                        }
                    },
                    columns: [
                        { data: 'nom', name: 'nom' },
                        { data: 'prenom', name: 'prenom' },
                        { data: 'adresse', name: 'adresse' },
                        { data: 'cin', name: 'cin' },
                        { data: 'ville', name: 'ville' },
                        { data: 'telephone', name: 'telephone' },
                        {
                            data: 'total',
                            name: 'total',
                            render: function (data, type, row) {
                                // Assuming 'DH' is a currency symbol or identifier
                                return data + ' DH';
                            }
                        },
                        { data: 'datereglement', name: 'datereglement' },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
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
                $(selector + ' tbody').on('click', '.Trash', function(e)
                {
                    e.preventDefault();
                    var idreglement = $(this).attr('value');
                    swal({
                    title: "es-tu sûr de supprimer cette regelemnt",
                    text: "Une fois supprimée, vous ne pourrez plus récupérer cette vente !",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                  })
                  .then((willDelete) => {
                    if (willDelete)
                    {
                        
                        
                        $.ajax({
                            type: "get",
                            url: "{{url('deletereglementpersonnel')}}",
                            data: 
                            {
                                id : idreglement,
                            },
                            dataType: "json",
                            success: function (response) 
                            {
                                if(response.status == 200)
                                {
                                    swal("Votre reglement a été supprimée !", {
                                        icon: "success",
                                    });
                                    location.reload();
                                }  
                                else if(response.status ==400)
                                {
                                    swal("Oops !", 'please contact support', "error");
                                } 
                            }
                        });

                    }
                    else
                    {
                        swal("Votre reglemnt est sécurisée !");
                    }
                });
                    
                });

                return table;
            }
        });
    </script>
</div>
@endsection
