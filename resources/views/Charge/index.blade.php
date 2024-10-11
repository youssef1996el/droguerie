@extends('Dashboard.app')
@section('content')
<script src="{{asset('js/Script_Charge/script.js')}}"></script>
<script>
    var Charge = "{{url('Charge')}}";
    var csrf_token                      = "{{csrf_token()}}";
    var StoreCharge                      = "{{url('StoreCharge')}}";
    var updateCharge                      = "{{url('updateCharge')}}";
    var TrashCharge                      = "{{url('TrashCharge')}}";
</script>
<div class="container-fluid">
    <div class="card card-body py-3">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="d-sm-flex align-items-center justify-space-between">
                    <h4 class="mb-4 mb-sm-0 card-title">Gestion de dépenses</h4>
                    <nav aria-label="breadcrumb" class="ms-auto">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item d-flex align-items-center">
                                <a class="text-muted text-decoration-none d-flex" href="{{url('/home')}}">
                                    <iconify-icon icon="solar:home-2-line-duotone" class="fs-6"></iconify-icon>
                                </a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <span class="badge fw-medium fs-2 bg-primary-subtle text-primary">
                                    Charge
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
                <div class="col-md-4 col-xl-3">
                    @can('charge-ajoute')
                        <a href="#"  class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#ModelCharge">
                            <i class="ti ti-category text-white me-1 fs-5"></i> Ajouter le charge
                        </a>
                    @endcan

                </div>
            </div>
        </div>
    </div>

    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2">Fiche charge</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped TableCharge">
                <thead>
                    <tr>
                        <th>Libelle</th>
                        <th>Total</th>
                        <th>Compagnie</th>
                        <th>Créer par</th>
                        <th>Créer le</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade " id="ModelCharge" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title">Fiche charge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="add-contact-box">
                        <div class="add-contact-content">

                            <ul class="ValidationCharge"></ul>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3 contact-name">
                                        <input type="text" id="name" name="name" class="form-control" placeholder="(obligatoire)" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3 contact-name">
                                        <input type="number" id="total" name="total" class="form-control" placeholder="Montant charge" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-6 m-0">
                        <button  class="btn btn-success" id="BtnSaveCharge">Sauvegarder</button>
                        <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade " id="ModelChargeEdit" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title">Fiche charge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="add-contact-box">
                        <div class="add-contact-content">

                            <ul class="ValidationChargeEdit"></ul>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3 contact-name">
                                        <input type="text" id="nameEdit" name="name" class="form-control" placeholder="Nom catégorie (obligatoire)" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3 contact-name">
                                        <input type="number" id="totalEdit" name="total" class="form-control" placeholder="Montant charge" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-6 m-0">
                        <button  class="btn btn-success" id="BtnEditCharge">Sauvegarder</button>
                        <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade " id="ModelChargeEditDate" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title">Fiche change date charge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{"ChangeDateCharge"}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="add-contact-box">
                            <div class="add-contact-content">                            
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3 contact-name">
                                            <input type="date" id="date" name="date" class="form-control"  required>
                                            <input type="text" name="id" id="idCharge" hidden>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex gap-6 m-0">
                            <button  class="btn btn-success" id="BtnEditCharge" type="submit">Sauvegarder</button>
                            <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                        </div>
                    </div>
                </form>
                
            </div>
        </div>
    </div>

</div>
@endsection
