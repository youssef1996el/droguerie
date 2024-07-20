@extends('Dashboard.app')
@section('content')

<div class="container-fluid">
    <div class="card card-body py-3">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="d-sm-flex align-items-center justify-space-between">
                    <h4 class="mb-4 mb-sm-0 card-title">Gestion de Client</h4>
                    <nav aria-label="breadcrumb" class="ms-auto">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item d-flex align-items-center">
                                <a class="text-muted text-decoration-none d-flex" href="{{url('/home')}}">
                                    <iconify-icon icon="solar:home-2-line-duotone" class="fs-6"></iconify-icon>
                                </a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <a href="#" class="badge fw-medium fs-2 bg-success text-white text-uppercase">solde de départ</a>
                                <span class="badge fw-medium fs-2 bg-primary-subtle text-primary">
                                    Voir client
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
            <h5 class="card-title border p-2 bg-light rounded-2 text-center text-uppercase fst-italic">Information</h5>
            <div class="row">
                <div class="add-contact-box">
                    <div class="add-contact-content">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <div class="mb-3 contact-name">
                                    <label for="">Nom :</label>
                                    <input type="text"  class="form-control" value="{{$Client->nom}}" disabled>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <div class="mb-3 contact-email">
                                    <label for="">Prénom :</label>
                                    <input type="text"  class="form-control" value="{{$Client->prenom}}" disabled>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <div class="mb-3 contact-name">
                                    <label for="">C.I.N :</label>
                                    <input type="text"  class="form-control" value="{{$Client->cin}}" disabled>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <div class="mb-3 contact-email">
                                    <label for="">Adresse :</label>
                                    <input type="text" class="form-control" value="{{$Client->adresse}}" disabled>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <div class="mb-3 contact-name">
                                    <label for="">Ville :</label>
                                    <input type="text" class="form-control" value="{{$Client->ville}}" disabled>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <div class="mb-3 contact-email">
                                    <label for="">Téléphone :</label>
                                    <input type="text" class="form-control" value="{{$Client->phone}}" disabled>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <div class="mb-3 contact-email">
                                    <label for="">Plafonnier</label>
                                    <input type="text" class="form-control" value="{{$Client->plafonnier}}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-body cardTable">
        <h5 class="card-title border p-2 bg-light rounded-2 text-center text-uppercase fst-italic">Historique client</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped TableClient" id="#TableClient">
                <thead class="fs-2" style="white-space: nowrap">
                    <tr>
                        <th>Client</th>
                        <th>Montant Vente</th>
                        <th>Montant Payé</th>
                        <th>Montant Rest</th>
                        <th>Type</th>
                        <th>Compagnie</th>
                        <th>Créer par</th>
                        <th>Créer Le</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($orders as $item)
                    @php
                        $id = Illuminate\Support\Facades\Crypt::encrypt($item->id);
                    @endphp

                    <tr>
                        <td style="white-space: nowrap">
                            <a href="{{ url('ShowOrder', $id) }}">{{$item->client}}</a>
                        </td>
                        <td>
                            <a href="{{ url('ShowOrder', $id) }}">{{$item->totalvente}} DH</a>
                        </td>
                        <td>
                            <a href="{{ url('ShowOrder', $id) }}">{{$item->totalpaye}}  DH</a>
                        </td>
                        <td>
                            <a href="{{ url('ShowOrder', $id) }}">{{$item->reste}}      DH</a>
                        </td>
                        @if ($item->idfacture)
                            <td>
                                <a href="{{ url('ShowOrder', $id) }}">
                                    <span class="form-control border border-success bg-success text-white">Facture</span>
                                </a>
                            </td>
                        @else
                            <td>
                                <a href="{{ url('ShowOrder', $item->id) }}">
                                    <span class="form-control border border-info bg-info text-white text-center">Bon</span>
                                </a>
                            </td>
                        @endif
                        <td>
                            <a href="{{ url('ShowOrder', $id) }}">{{$item->company}}</a>
                        </td>
                        <td>
                            <a href="{{ url('ShowOrder', $id) }}">{{$item->user}}</a>
                        </td>
                        <td style="white-space: nowrap">
                            <a href="{{ url('ShowOrder', $id) }}">{{$item->created_at_formatted}}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card card-body ">
        <h5 class="card-title border p-2 bg-light rounded-2 text-center text-uppercase fst-italic">Remarque client</h5>
        <div class="form-group FormRemaruqe">
            <ul class="ValidationRemark"></ul>
            <textarea name="" id="remark" cols="30" rows="10" class="form-control" placeholder="Remarque ... ">{{$remark}}</textarea>
            <button class="btn btn-success mt-2 float-end" id="SaveRemark">Sauvegarder</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function ()
    {
        var idClient = @Json($idclient);

        $('#SaveRemark').on('click',function(e)
        {
            e.preventDefault();
            var data =
            {
                'remark'  : $('#remark').val().trim(),
                'idclient': idClient,
            };
            $.ajax({
                type: "get",
                url: "{{url('StoreRemark')}}",
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
    });


</script>
@endsection
