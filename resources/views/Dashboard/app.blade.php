


 <!DOCTYPE html>
 <html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

 <head>
   <!-- Required meta tags -->

   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <!-- Favicon icon-->
   <link rel="shortcut icon" type="image/png" href="" />

   <!-- Core Css -->
   <link rel="stylesheet" href="{{asset('css/dashboard/styles.css')}}" />

   <title>STE RIFI</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css">

  <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

  <link rel="stylesheet" href="{{asset('css/Datatable/style.css')}}">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">




  <meta name="csrf-token" content="{{ csrf_token() }}">
 </head>

 <body class="link-sidebar">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script> --}}
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
   <!-- Toast -->
   <div class="toast toast-onload align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
     <div class="toast-body hstack align-items-start gap-6">
       <i class="ti ti-alert-circle fs-6"></i>
       <div>
         <h5 class="text-white fs-3 mb-1">Bienvenue à RIFI</h5>

       </div>
       <button type="button" class="btn-close btn-close-white fs-2 m-0 ms-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
     </div>
   </div>
   <!-- Preloader -->
   <div class="preloader">
     <img src="{{asset('images/favicon.png')}}" alt="loader" class="lds-ripple img-fluid" />
   </div>
   <div id="main-wrapper">
     <!-- Sidebar Start -->
     <aside class="left-sidebar with-vertical">
       <!-- ---------------------------------- -->
       <!-- Start Vertical Layout Sidebar -->
       <!-- ---------------------------------- -->
       <div>

         <div class="brand-logo d-flex align-items-center">
           <a href="#" class="text-nowrap logo-img">
             <img src="{{asset('images_login/logo.webp')}}" alt="Logo"/>
           </a>

         </div>
         <!-- ---------------------------------- -->
         <!-- Dashboard -->
         <!-- ---------------------------------- -->
         <nav class="sidebar-nav scroll-sidebar" data-simplebar>
           <ul class="sidebar-menu" id="sidebarnav">
             <!-- ---------------------------------- -->
             <!-- Home -->
             <!-- ---------------------------------- -->
             <li class="sortable-group" data-id="group1">
                <div class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="mini-icon"></iconify-icon>
                    <span class="hide-menu">Tableau de bord</span>
                </div>
                <div class="sidebar-item">
                    <a class="sidebar-link {{ Request::is('home') || Request::is('Dashboard') ? 'active' : '' }}" href="{{ url('Dashboard') }}"  aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-dashboard">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 13m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                            <path d="M13.45 11.55l2.05 -2.05" />
                            <path d="M6.4 20a9 9 0 1 1 11.2 0z" />
                        </svg>
                        <span class="hide-menu">Tableau de bord</span>
                    </a>
                </div>
                <div class="sidebar-divider lg"></div>
            </li>
            @can('company')
                <li class="sortable-group" data-id="group2">
                    <div class="nav-small-cap">
                        <iconify-icon icon="solar:menu-dots-linear" class="mini-icon"></iconify-icon>
                        <span class="hide-menu">Gestion de Compagnie</span>
                    </div>
                    <div class="sidebar-item">
                        <a class="sidebar-link {{Request::is('Company') ? 'active' : ''}}" href="{{url('Company')}}" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-home">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                            </svg>
                            <span class="hide-menu">Compagnie</span>
                        </a>
                    </div>
                    <div class="sidebar-divider lg"></div>
                </li>
            @endcan




            @can('clients')
                <li class="sortable-group" data-id="group3">
                    <div class="nav-small-cap">
                        <iconify-icon icon="solar:menu-dots-linear" class="mini-icon"></iconify-icon>
                        <span class="hide-menu">Gestion de Client</span>
                    </div>
                    <div class="sidebar-item">
                        <a class="sidebar-link {{Request::is('Client') ? 'active' : ''}}" href="{{url('Client')}}" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                            </svg>
                            <span class="hide-menu">Clients</span>
                        </a>
                    </div>
                    <div class="sidebar-divider lg"></div>
                </li>
            @endcan
            @can('catégorie')
                <li class="sortable-group" data-id="group4">
                    <div class="nav-small-cap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mini-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-category">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 4h6v6h-6z" />
                            <path d="M14 4h6v6h-6z" />
                            <path d="M4 14h6v6h-6z" />
                            <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                        </svg>

                        <span class="hide-menu">Gestion de catégorie</span>
                    </div>
                    <div class="sidebar-item">
                        <a class="sidebar-link {{Request::is('Category') ? 'active' : ''}}" href="{{url('Category')}}" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-category">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 4h6v6h-6z" />
                                <path d="M14 4h6v6h-6z" />
                                <path d="M4 14h6v6h-6z" />
                                <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                            </svg>
                            <span class="hide-menu">Catégorie</span>
                        </a>
                    </div>
                    <div class="sidebar-divider lg"></div>

                </li>
            @endcan

            <li class="sortable-group" data-id="group5">
                <div class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="mini-icon"></iconify-icon>
                    <span class="hide-menu">Gestion de paramètre</span>
                </div>
                @can('paramètre')
                    <div class="sidebar-item">
                        <a class="sidebar-link {{Request::is('Setting') ? 'active' : ''}}" href="{{url('Setting')}}" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-settings">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                            </svg>
                            <span class="hide-menu">Paramètre</span>
                        </a>
                    </div>
                @endcan
                @can('tva')
                    <div class="sidebar-item">
                        <a class="sidebar-link {{Request::is('Tva') ? 'active' : ''}}" href="{{url('Tva')}}" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-percentage">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M17 17m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                <path d="M7 7m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                <path d="M6 18l12 -12" />
                            </svg>
                            <span class="hide-menu">Tva</span>
                        </a>
                    </div>
                @endcan
                @can('mode paiement')
                    <div class="sidebar-item">
                        <a class="sidebar-link {{Request::is('ModePaiement') ? 'active' : ''}}" href="{{url('ModePaiement')}}" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-coin">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1" />
                                <path d="M12 7v10" />
                            </svg>
                            <span class="hide-menu">Mode paiement</span>
                        </a>
                    </div>
                @endcan
                @can('information')
                    <div class="sidebar-item">
                        <a class="sidebar-link {{Request::is('Info') ? 'active' : ''}}" href="{{url('Info')}}" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                <path d="M12 9h.01" />
                                <path d="M11 12h1v4h1" />
                            </svg>
                            <span class="hide-menu">Information</span>
                        </a>
                    </div>
                @endcan
                @can('utilisateur')
                    <div class="sidebar-item">
                        <a class="sidebar-link {{Request::is('users') ? 'active' : ''}}" href="{{url('users')}}" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-license">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M15 21h-9a3 3 0 0 1 -3 -3v-1h10v2a2 2 0 0 0 4 0v-14a2 2 0 1 1 2 2h-2m2 -4h-11a3 3 0 0 0 -3 3v11" />
                                <path d="M9 7l4 0" />
                                <path d="M9 11l4 0" />
                            </svg>
                            <span class="hide-menu">utilisateurs</span>
                        </a>
                    </div>
                @endcan
                @can('rôles')
                <div class="sidebar-item">
                    <a class="sidebar-link {{Request::is('roles') ? 'active' : ''}}" href="{{url('roles')}}" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-edit">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                            <path d="M18.42 15.61a2.1 2.1 0 0 1 2.97 2.97l-3.39 3.42h-3v-3l3.42 -3.39z" />
                          </svg>
                        <span class="hide-menu">Les rôles</span>
                    </a>
                </div>

                @endcan
                <div class="sidebar-divider lg"></div>
            </li>
            @can('stock')
                <li class="sortable-group" data-id="group6">
                    <div class="nav-small-cap">
                        <iconify-icon icon="solar:menu-dots-linear" class="mini-icon"></iconify-icon>
                        <span class="hide-menu">Gestion de Stock</span>
                    </div>

                    <div class="sidebar-item">
                        <a class="sidebar-link {{Request::is('Stock') ? 'active' : ''}}" href="{{url('Stock')}}" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-packages">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 16.5l-5 -3l5 -3l5 3v5.5l-5 3z" />
                                <path d="M2 13.5v5.5l5 3" />
                                <path d="M7 16.545l5 -3.03" />
                                <path d="M17 16.5l-5 -3l5 -3l5 3v5.5l-5 3z" />
                                <path d="M12 19l5 3" />
                                <path d="M17 16.5l5 -3" />
                                <path d="M12 13.5v-5.5l-5 -3l5 -3l5 3v5.5" />
                                <path d="M7 5.03v5.455" />
                                <path d="M12 8l5 -3" />
                            </svg>
                            <span class="hide-menu">Stock</span>
                        </a>
                    </div>
                    <div class="sidebar-divider lg"></div>
                </li>
            @endcan

            <style>
                .collapse {
                    display: none;
                    transition: all 0.3s ease;
                }
                .collapse.in {
                    display: block;
                }
                .active {
                    font-weight: bold; /* Example styling for active state */
                }
            </style>


            <li class="sortable-group" data-id="group7">
                <div class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="mini-icon"></iconify-icon>
                    <span class="hide-menu">Production</span>
                </div>
                @can('vente')
                    <div class="sidebar-item">
                        <a class="sidebar-link {{Request::is('Order') ? 'active' : ''}} " href="{{url('Order')}}" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-building-store">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" />
                                <path d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4" />
                                <path d="M5 21l0 -10.15" />
                                <path d="M19 21l0 -10.15" />
                                <path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" />
                            </svg>
                            <span class="hide-menu">Vente</span>
                        </a>
                    </div>
                @endcan
                @can('facture')
                    <div class="sidebar-item">
                        <a class="sidebar-link {{Request::is('Facture') ? 'active' : ''}} " href="{{url('Facture')}}" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-invoice">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                <path d="M9 7l1 0" />
                                <path d="M9 13l6 0" />
                                <path d="M13 17l2 0" />
                            </svg>
                            <span class="hide-menu">Facture</span>
                        </a>
                    </div>
                @endcan
                <div class="sidebar-item">
                    <a class="sidebar-link {{Request::is('Avoir') ? 'active' : ''}} " href="{{url('Avoir')}}" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-switch-horizontal">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M16 3l4 4l-4 4" />
                            <path d="M10 7l10 0" />
                            <path d="M8 13l-4 4l4 4" />
                            <path d="M4 17l9 0" />
                          </svg>
                        <span class="hide-menu">Change</span>
                    </a>
                </div>


                <div class="sidebar-divider lg"></div>
            </li>

            <script>
              document.addEventListener('DOMContentLoaded', function () {
            const sidebarLinks = document.querySelectorAll('.sidebar-link.has-arrow');

            sidebarLinks.forEach(link => {
                link.addEventListener('click', function (event) {
                    event.preventDefault();

                    // Check if the clicked link is already active
                    const isActive = this.classList.contains('active');
                    const collapse = this.nextElementSibling;

                    // Close all active collapsible elements at the same level
                    const siblingLinks = this.closest('ul').querySelectorAll('.sidebar-link.has-arrow');
                    siblingLinks.forEach(siblingLink => {
                        siblingLink.classList.remove('active');
                        const siblingCollapse = siblingLink.nextElementSibling;
                        if (siblingCollapse && siblingCollapse.tagName === 'UL') {
                            siblingCollapse.classList.remove('in');
                        }
                    });

                    // If the clicked link was not active, activate it and show the collapse
                    if (!isActive) {
                        this.classList.add('active');
                        if (collapse && collapse.tagName === 'UL') {
                            collapse.classList.add('in');
                        }
                    }
                });
            });
        });
            </script>
            <script>
                /* document.addEventListener('DOMContentLoaded', function () {
            var sortableRow = document.getElementById('sidebarnav');

            // Load the saved order from localStorage
            var savedOrder = localStorage.getItem('sidebarOrder');
            if (savedOrder) {
                var order = JSON.parse(savedOrder);
                order.forEach(function(id) {
                    var element = document.querySelector('[data-id="' + id + '"]');
                    if (element) {
                        sortableRow.appendChild(element);
                    }
                });
            }

            // Initialize SortableJS
            var sortable = new Sortable(sortableRow, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                handle: '.nav-small-cap, .sidebar-item, .sidebar-divider',
                onEnd: function (evt) {
                    // Save the new order in localStorage
                    var order = [];
                    sortableRow.querySelectorAll('.sortable-group').forEach(function(item) {
                        order.push(item.getAttribute('data-id'));
                    });
                    localStorage.setItem('sidebarOrder', JSON.stringify(order));
                }
            });
        }); */
            </script>


            @can('bordereau journalier')
                <li class="sortable-group" data-id="group8">
                    <div class="nav-small-cap">
                        <iconify-icon icon="solar:menu-dots-linear" class="mini-icon"></iconify-icon>
                        <span class="hide-menu">exploitation</span>
                    </div>
                    <div class="sidebar-item">
                        <a class="sidebar-link {{Request::is('Borderau') ? 'active' : ''}} " href="{{url('Borderau')}}">
                            <svg style="color: rgb(192, 188, 188)" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clipboard-data">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                <path d="M9 17v-4" />
                                <path d="M12 17v-1" />
                                <path d="M15 17v-2" />
                                <path d="M12 17v-1" />
                            </svg>
                            <span class="hide-menu " title="Bordereau journalier de production">Bordereau journalier de <br>  production</span>
                        </a>
                    </div>

                    <div class="sidebar-divider lg"></div>
                </li>
            @endcan


            @can('charge')
                <li class="sortable-group" data-id="group9">
                    <div class="nav-small-cap">
                        <iconify-icon icon="solar:menu-dots-linear" class="mini-icon"></iconify-icon>
                        <span class="hide-menu">dépenses</span>
                    </div>
                    <div class="sidebar-item">
                        <a class="sidebar-link {{Request::is('Charge') ? 'active' : ''}}" href="{{url('Charge')}}">
                            <svg style="color: rgb(192, 188, 188)" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-shield-dollar">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M13.018 20.687c-.333 .119 -.673 .223 -1.018 .313a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3a12 12 0 0 0 8.5 3c.433 1.472 .575 2.998 .436 4.495" />
                                <path d="M21 15h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" />
                                <path d="M19 21v1m0 -8v1" />
                            </svg>
                            <span class="hide-menu">Charge</span>
                        </a>
                    </div>
                    <div class="sidebar-divider lg"></div>
                </li>
            @endcan


            <li class="sortable-group" data-id="group10">
                <div class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="mini-icon"></iconify-icon>
                    <span class="hide-menu">Gestion de personnel</span>
                </div>
                <div class="sidebar-item">
                    @can('personnel')
                        <a class="sidebar-link {{Request::is('Personnel') ? 'active' : ''}}" href="{{url('Personnel')}}" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4c.96 0 1.84 .338 2.53 .901" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                <path d="M16 19h6" />
                                <path d="M19 16v6" />
                            </svg>
                            <span class="hide-menu">Personnel</span>
                        </a>
                    @endcan
                    @can('suivi personnel')
                        <a class="sidebar-link {{Request::is('SuiviPersonnel') ? 'active' : ''}}" href="{{url('SuiviPersonnel')}}" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cash">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 9m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                                <path d="M14 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2" />
                            </svg>
                            <span class="hide-menu">Suivi personnel</span>
                        </a>
                    @endcan

                </div>

                <div class="sidebar-divider lg"></div>
            </li>
            @can('Cheque')
                <li class="sortable-group" data-id="group10">
                    <div class="nav-small-cap">
                        <iconify-icon icon="solar:menu-dots-linear" class="mini-icon"></iconify-icon>
                        <span class="hide-menu">Trésorie</span>
                    </div>
                    <div class="sidebar-item">
                        <a class="sidebar-link {{Request::is('Cheque') ? 'active' : ''}}" href="{{url('Cheque')}}">

                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-dollar">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                <path d="M14 11h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" />
                                <path d="M12 17v1m0 -8v1" />
                            </svg>
                            <span class="hide-menu">Chèque</span>
                        </a>
                    </div>
                    <div class="sidebar-divider lg"></div>
                </li>
            @endcan


            @can('recouverement')
                <li class="sortable-group" data-id="group11">
                    <div class="nav-small-cap">
                        <iconify-icon icon="solar:menu-dots-linear" class="mini-icon"></iconify-icon>
                        <span class="hide-menu">Évaluation de la situation financière</span>
                    </div>
                    <div class="sidebar-item">
                        <a class="sidebar-link {{Request::is('Recouverement') ? 'active' : ''}}" href="{{url('Recouverement')}}">

                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-record">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M21 12.3a9 9 0 1 0 -8.683 8.694" />
                                <path d="M12 7v5l2 2" />
                                <path d="M19 19m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                            </svg>
                            <span class="hide-menu">Recouverement</span>
                        </a>
                    </div>
                    <div class="sidebar-divider lg"></div>
                </li>
            @endcan

            @can('etat')
                <li class="sortable-group" data-id="group12">
                    <div class="nav-small-cap">
                        <iconify-icon icon="solar:menu-dots-linear" class="mini-icon"></iconify-icon>
                        <span class="hide-menu">rapport</span>
                    </div>
                    <div class="sidebar-item">
                        <a href="{{url('Etat')}}" class="sidebar-link {{Request::is('Etat') ? 'active' : ''}}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-report">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M8 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5.697" />
                                <path d="M18 14v4h4" />
                                <path d="M18 11v-4a2 2 0 0 0 -2 -2h-2" />
                                <path d="M8 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M8 11h4" />
                                <path d="M8 15h3" />
                            </svg>
                            <span class="hide-menu">État journalier </span>
                        </a>
                    </div>
                </li>
            @endcan
            @can('Solde')
                <li class="sortable-group" data-id="group12">
                    <div class="nav-small-cap">
                        <iconify-icon icon="solar:menu-dots-linear" class="mini-icon"></iconify-icon>
                        <span class="hide-menu">Gestion de la caisse</span>
                    </div>
                    <div class="sidebar-item">
                        <a href="{{url('SoldeCaisse')}}" class="sidebar-link {{Request::is('SoldeCaisse') ? 'active' : ''}}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-currency-dollar">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" />
                                <path d="M12 3v3m0 12v3" />
                            </svg>
                            <span class="hide-menu">Solde initial de la caisse </span>
                        </a>
                    </div>
                </li>
            @endcan





        </ul>
    </nav>

</div>
</aside>
     <!--  Sidebar End -->
     <div class="page-wrapper">
       <!--  Header Start -->
       <header class="topbar">
         <div class="with-vertical">
           <!-- ---------------------------------- -->
           <!-- Start Vertical Layout Header -->
           <!-- ---------------------------------- -->
           <nav class="navbar navbar-expand-lg p-0">

             <ul class="navbar-nav">
               <li class="nav-item nav-icon-hover-bg rounded-circle d-flex">
                 <a class="nav-link  sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                   <iconify-icon icon="solar:hamburger-menu-line-duotone" class="fs-6"></iconify-icon>
                 </a>
               </li>


             </ul>
             <ul class="navbar-nav mx-auto border rounded-2 bg-light  w-100 " id="companyStatus">
                <li class="nav-item  w-100">
                    <span class="navbar-text w-100 fs-5 d-inline-block text-center text-danger fw-bold">{{$CompanyIsActive ? "Compagnie est active :".  $CompanyIsActive->title : "Il n'y a pas de Compagnie active" }}  </span>
                </li>
            </ul>

             <div class="d-block d-lg-none py-9 py-xl-0">
               <img src="" alt="matdash-img" />
             </div>
             <a class="navbar-toggler p-0 border-0 nav-icon-hover-bg rounded-circle" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
               <iconify-icon icon="solar:menu-dots-bold-duotone" class="fs-6"></iconify-icon>
             </a>
             <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
               <div class="d-flex align-items-center justify-content-between">
                 <ul class="navbar-nav flex-row mx-auto ms-lg-auto align-items-center justify-content-center">
                   <li class="nav-item dropdown">
                     <a href="javascript:void(0)" class="nav-link nav-icon-hover-bg rounded-circle d-flex d-lg-none align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar" aria-controls="offcanvasWithBothOptions">
                       <iconify-icon icon="solar:sort-line-duotone" class="fs-6"></iconify-icon>
                     </a>
                   </li>
                   <li class="nav-item">
                     <a class="nav-link moon dark-layout nav-icon-hover-bg rounded-circle" href="javascript:void(0)">
                       <iconify-icon icon="solar:moon-line-duotone" class="moon fs-6"></iconify-icon>
                     </a>
                     <a class="nav-link sun light-layout nav-icon-hover-bg rounded-circle" href="javascript:void(0)" style="display: none">
                       <iconify-icon icon="solar:sun-2-line-duotone" class="sun fs-6"></iconify-icon>
                     </a>
                   </li>
                   <li class="nav-item d-block d-xl-none">
                     <a class="nav-link nav-icon-hover-bg rounded-circle" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal">
                       <iconify-icon icon="solar:magnifer-line-duotone" class="fs-6"></iconify-icon>
                     </a>
                   </li>

                   <!-- ------------------------------- -->
                   <!-- start notification Dropdown -->
                   <!-- ------------------------------- -->
                   <li class="nav-item dropdown nav-icon-hover-bg rounded-circle">
                        <a class="nav-link position-relative" href="javascript:void(0)" id="drop2" aria-expanded="false">
                            <iconify-icon icon="solar:bell-bing-line-duotone" class="fs-6"></iconify-icon>
                        </a>
                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                        <div class="d-flex align-items-center justify-content-between py-3 px-7">
                            <h5 class="mb-0 fs-5 fw-semibold">Notifications</h5>
                            @php
                                $unreadCount = Auth::user()->unreadNotifications->count();
                            @endphp
                            <span class="badge text-bg-primary rounded-4 px-3 py-1 lh-sm">{{ $unreadCount }} nouveaux</span>
                        </div>
                        <div class="message-body" data-simplebar>
                            @foreach(Auth::user()->unreadNotifications as $notification)
                                <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item gap-3">
                                    <span class="flex-shrink-0 bg-danger-subtle rounded-circle round d-flex align-items-center justify-content-center fs-6 text-danger">
                                      <iconify-icon icon="solar:widget-3-line-duotone"></iconify-icon>
                                    </span>
                                    <div class="w-75">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <p class="mb-1 fw-semibold" style="white-space: break-spaces">{{$notification->data['text']}}</p>
                                          {{-- <span class="d-block fs-2">9:30 AM</span> --}}
                                      </div>
                                      <div>
                                          <span class="d-block text-truncate text-truncate fs-11">Just see the my new admin!</span>
                                      </div>
                                    </div>
                                </a>
                            @endforeach
                       </div>

                     </div>
                   </li>
                   <li class="nav-item dropdown">
                     <a class="nav-link" href="javascript:void(0)" id="drop1" aria-expanded="false">
                       <div class="d-flex align-items-center gap-2 lh-base">
                         <img src="{{asset('images/user-1.jpg')}}" class="rounded-circle" width="35" height="35" alt="matdash-img" />
                         <iconify-icon icon="solar:alt-arrow-down-bold" class="fs-2"></iconify-icon>
                       </div>
                     </a>
                     <div class="dropdown-menu profile-dropdown dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop1">
                       <div class="position-relative px-4 pt-3 pb-2">
                         <div class="d-flex align-items-center mb-3 pb-3 border-bottom gap-6">
                           <img src="{{asset('images/user-1.jpg')}}" class="rounded-circle" width="56" height="56" alt="matdash-img" />
                           <div>
                             <h5 class="mb-0 fs-12">{{Auth::user()->name}}
                                <span class="text-success fs-11">
                                    {{-- @if(!empty($user->getRoleNames()))

                                        @foreach($user->getRoleNames() as $role)
                                            <label class="badge badge-success text-success">{{ $role }}</label>
                                        @endforeach

                                    @endif --}}
                                </span>
                             </h5>
                             <p class="mb-0 text-dark">
                               {{Auth::user()->email}}
                             </p>
                           </div>
                         </div>
                         <div class="message-body">

                           <a class="p-2 dropdown-item h6 rounded-1" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                         </div>
                       </div>
                     </div>
                   </li>
                 </ul>
               </div>
             </div>
           </nav>
         </div>
         <div class="app-header with-horizontal">
           <nav class="navbar navbar-expand-xl container-fluid p-0">
             <ul class="navbar-nav align-items-center">
               <li class="nav-item d-flex d-xl-none">
                 <a class="nav-link sidebartoggler nav-icon-hover-bg rounded-circle" id="sidebarCollapse" href="javascript:void(0)">
                   <iconify-icon icon="solar:hamburger-menu-line-duotone" class="fs-7"></iconify-icon>
                 </a>
               </li>
               <li class="nav-item d-none d-xl-flex align-items-center">
                 <a href="" class="text-nowrap nav-link">
                   <img src="" alt="matdash-img" />
                 </a>
               </li>
               <li class="nav-item d-none d-lg-flex align-items-center dropdown nav-icon-hover-bg rounded-circle">
                 <div class="hover-dd">
                   <a class="nav-link" id="drop2" href="javascript:void(0)" aria-haspopup="true" aria-expanded="false">
                     <iconify-icon icon="solar:widget-3-line-duotone" class="fs-6"></iconify-icon>
                   </a>
                   <div class="dropdown-menu dropdown-menu-nav dropdown-menu-animate-up py-0 overflow-hidden" aria-labelledby="drop2">
                     <div class="position-relative">
                       <div class="row">
                         <div class="col-8">
                           <div class="p-4 pb-3">

                             <div class="row">
                               <div class="col-6">
                                 <div class="position-relative">
                                   <a href="../default-sidebar/app-chat.html" class="d-flex align-items-center pb-9 position-relative">
                                     <div class="bg-primary-subtle rounded round-48 me-3 d-flex align-items-center justify-content-center">
                                       <iconify-icon icon="solar:chat-line-bold-duotone" class="fs-7 text-primary"></iconify-icon>
                                     </div>
                                     <div class="d-inline-block">
                                       <h6 class="mb-0">Chat Application</h6>
                                       <span class="fs-11 d-block text-body-color">New messages arrived</span>
                                     </div>
                                   </a>
                                   <a href="../default-sidebar/app-invoice.html" class="d-flex align-items-center pb-9 position-relative">
                                     <div class="bg-secondary-subtle rounded round-48 me-3 d-flex align-items-center justify-content-center">
                                       <iconify-icon icon="solar:bill-list-bold-duotone" class="fs-7 text-secondary"></iconify-icon>
                                     </div>
                                     <div class="d-inline-block">
                                       <h6 class="mb-0">Invoice App</h6>
                                       <span class="fs-11 d-block text-body-color">Get latest invoice</span>
                                     </div>
                                   </a>
                                   <a href="../default-sidebar/app-contact2.html" class="d-flex align-items-center pb-9 position-relative">
                                     <div class="bg-warning-subtle rounded round-48 me-3 d-flex align-items-center justify-content-center">
                                       <iconify-icon icon="solar:phone-calling-rounded-bold-duotone" class="fs-7 text-warning"></iconify-icon>
                                     </div>
                                     <div class="d-inline-block">
                                       <h6 class="mb-0">Contact Application</h6>
                                       <span class="fs-11 d-block text-body-color">2 Unsaved Contacts</span>
                                     </div>
                                   </a>
                                   <a href="../default-sidebar/app-email.html" class="d-flex align-items-center pb-9 position-relative">
                                     <div class="bg-danger-subtle rounded round-48 me-3 d-flex align-items-center justify-content-center">
                                       <iconify-icon icon="solar:letter-bold-duotone" class="fs-7 text-danger"></iconify-icon>
                                     </div>
                                     <div class="d-inline-block">
                                       <h6 class="mb-0">Email App</h6>
                                       <span class="fs-11 d-block text-body-color">Get new emails</span>
                                     </div>
                                   </a>
                                 </div>
                               </div>
                               <div class="col-6">
                                 <div class="position-relative">
                                   <a href="../default-sidebar/page-user-profile.html" class="d-flex align-items-center pb-9 position-relative">
                                     <div class="bg-success-subtle rounded round-48 me-3 d-flex align-items-center justify-content-center">
                                       <iconify-icon icon="solar:user-bold-duotone" class="fs-7 text-success"></iconify-icon>
                                     </div>
                                     <div class="d-inline-block">
                                       <h6 class="mb-0">User Profile</h6>
                                       <span class="fs-11 d-block text-body-color">learn more information</span>
                                     </div>
                                   </a>
                                   <a href="../default-sidebar/app-calendar.html" class="d-flex align-items-center pb-9 position-relative">
                                     <div class="bg-primary-subtle rounded round-48 me-3 d-flex align-items-center justify-content-center">
                                       <iconify-icon icon="solar:calendar-minimalistic-bold-duotone" class="fs-7 text-primary"></iconify-icon>
                                     </div>
                                     <div class="d-inline-block">
                                       <h6 class="mb-0">Calendar App</h6>
                                       <span class="fs-11 d-block text-body-color">Get dates</span>
                                     </div>
                                   </a>
                                   <a href="../default-sidebar/app-contact.html" class="d-flex align-items-center pb-9 position-relative">
                                     <div class="bg-secondary-subtle rounded round-48 me-3 d-flex align-items-center justify-content-center">
                                       <iconify-icon icon="solar:smartphone-2-bold-duotone" class="fs-7 text-secondary"></iconify-icon>
                                     </div>
                                     <div class="d-inline-block">
                                       <h6 class="mb-0">Contact List Table</h6>
                                       <span class="fs-11 d-block text-body-color">Add new contact</span>
                                     </div>
                                   </a>
                                   <a href="../default-sidebar/app-notes.html" class="d-flex align-items-center pb-9 position-relative">
                                     <div class="bg-warning-subtle rounded round-48 me-3 d-flex align-items-center justify-content-center">
                                       <iconify-icon icon="solar:notes-bold-duotone" class="fs-7 text-warning"></iconify-icon>
                                     </div>
                                     <div class="d-inline-block">
                                       <h6 class="mb-0">Notes Application</h6>
                                       <span class="fs-11 d-block text-body-color">To-do and Daily tasks</span>
                                     </div>
                                   </a>
                                 </div>
                               </div>
                             </div>
                           </div>
                         </div>
                         <div class="col-4">
                           <img src="" alt="mega-dd" class="img-fluid mega-dd-bg" />
                         </div>
                       </div>
                     </div>
                   </div>
                 </div>
               </li>
             </ul>
             <div class="d-block d-xl-none">
               <a href="#" class="text-nowrap nav-link">
                 <img src="" alt="matdash-img" />
               </a>
             </div>
             <a class="navbar-toggler nav-icon-hover p-0 border-0 nav-icon-hover-bg rounded-circle" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
               <span class="p-2">
                 <i class="ti ti-dots fs-7"></i>
               </span>
             </a>
             <div class="collapse navbar-collapse justify-content-end collapse show" id="navbarNav">
                <div class="d-flex align-items-center justify-content-between px-0 px-xl-8">
                    <ul class="navbar-nav flex-row mx-auto ms-lg-auto align-items-center justify-content-center">
                    <li class="nav-item dropdown">
                        <a href="javascript:void(0)" class="nav-link nav-icon-hover-bg rounded-circle d-flex d-lg-none align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar" aria-controls="offcanvasWithBothOptions">
                        <iconify-icon icon="solar:sort-line-duotone" class="fs-6"></iconify-icon>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-icon-hover-bg rounded-circle moon dark-layout" href="javascript:void(0)">
                        <iconify-icon icon="solar:moon-line-duotone" class="moon fs-6"></iconify-icon>
                        </a>
                        <a class="nav-link nav-icon-hover-bg rounded-circle sun light-layout" href="javascript:void(0)" style="display: none">
                        <iconify-icon icon="solar:sun-2-line-duotone" class="sun fs-6"></iconify-icon>
                        </a>
                    </li>
                    <li class="nav-item d-block d-xl-none">
                        <a class="nav-link nav-icon-hover-bg rounded-circle" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <iconify-icon icon="solar:magnifer-line-duotone" class="fs-6"></iconify-icon>
                        </a>
                    </li>
                    <li class="nav-item dropdown nav-icon-hover-bg rounded-circle">
                        <a class="nav-link position-relative" href="javascript:void(0)" id="drop2" aria-expanded="false">
                            <iconify-icon icon="solar:bell-bing-line-duotone" class="fs-6"></iconify-icon>
                        </a>
                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                            <div class="d-flex align-items-center justify-content-between py-3 px-7">
                                <h5 class="mb-0 fs-5 fw-semibold">Notifications</h5>
                                @php
                                    $unreadCount = Auth::user()->unreadNotifications->count();
                                @endphp
                                <span class="badge text-bg-primary rounded-4 px-3 py-1 lh-sm">{{$unreadCount}} nouveaux</span>
                            </div>
                            <div class="message-body" data-simplebar>
                                @foreach(Auth::user()->unreadNotifications as $notification)
                                    <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item gap-3">
                                        <span class="flex-shrink-0 bg-danger-subtle rounded-circle round d-flex align-items-center justify-content-center fs-6 text-danger">
                                          <iconify-icon icon="solar:widget-3-line-duotone"></iconify-icon>
                                        </span>
                                        <div class="w-75">
                                          <div class="d-flex align-items-center justify-content-between">
                                              <p class="mb-1 fw-semibold" style="white-space: break-spaces">{{$notification->data['text']}}</p>
                                              {{-- <span class="d-block fs-2">9:30 AM</span> --}}
                                          </div>
                                          <div>
                                              <span class="d-block text-truncate text-truncate fs-11">Just see the my new admin!</span>
                                          </div>
                                        </div>
                                    </a>
                                @endforeach
                           </div>
                            <div class="py-6 px-7 mb-1">
                                <button class="btn btn-primary w-100">See All Notifications</button>
                            </div>
                        </div>
                    </li>
                   <li class="nav-item dropdown nav-icon-hover-bg rounded-circle">
                     <a class="nav-link" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
                       <img src="" alt="matdash-img" width="20px" height="20px" class="rounded-circle object-fit-cover round-20" />
                     </a>
                     <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                       <div class="message-body">
                         <a href="javascript:void(0)" class="d-flex align-items-center gap-2 py-3 px-4 dropdown-item">
                           <div class="position-relative">
                             <img src="" alt="matdash-img" width="20px" height="20px" class="rounded-circle object-fit-cover round-20" />
                           </div>
                           <p class="mb-0 fs-3">English (UK)</p>
                         </a>
                         <a href="javascript:void(0)" class="d-flex align-items-center gap-2 py-3 px-4 dropdown-item">
                           <div class="position-relative">
                             <img src="" alt="matdash-img" width="20px" height="20px" class="rounded-circle object-fit-cover round-20" />
                           </div>
                           <p class="mb-0 fs-3">中国人 (Chinese)</p>
                         </a>
                         <a href="javascript:void(0)" class="d-flex align-items-center gap-2 py-3 px-4 dropdown-item">
                           <div class="position-relative">
                             <img src="" alt="matdash-img" width="20px" height="20px" class="rounded-circle object-fit-cover round-20" />
                           </div>
                           <p class="mb-0 fs-3">français (French)</p>
                         </a>
                         <a href="javascript:void(0)" class="d-flex align-items-center gap-2 py-3 px-4 dropdown-item">
                           <div class="position-relative">
                             <img src="" alt="matdash-img" width="20px" height="20px" class="rounded-circle object-fit-cover round-20" />
                           </div>
                           <p class="mb-0 fs-3">عربي (Arabic)</p>
                         </a>
                       </div>
                     </div>
                   </li>
                   <!-- ------------------------------- -->
                   <!-- end language Dropdown -->
                   <!-- ------------------------------- -->

                   <!-- ------------------------------- -->
                   <!-- start profile Dropdown -->
                   <!-- ------------------------------- -->
                   <li class="nav-item dropdown">
                     <a class="nav-link" href="javascript:void(0)" id="drop1" aria-expanded="false">
                       <div class="d-flex align-items-center gap-2 lh-base">
                         <img src="" class="rounded-circle" width="35" height="35" alt="matdash-img" />
                         <iconify-icon icon="solar:alt-arrow-down-bold" class="fs-2"></iconify-icon>
                       </div>
                     </a>
                     <div class="dropdown-menu profile-dropdown dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop1">
                       <div class="position-relative px-4 pt-3 pb-2">
                         <div class="d-flex align-items-center mb-3 pb-3 border-bottom gap-6">
                           <img src="" class="rounded-circle" width="56" height="56" alt="matdash-img" />
                           <div>
                             <h5 class="mb-0 fs-12">David McMichael <span class="text-success fs-11">Pro</span>
                             </h5>
                             <p class="mb-0 text-dark">
                               david@wrappixel.com
                             </p>
                           </div>
                         </div>
                         <div class="message-body">

                            <a class="p-2 dropdown-item h6 rounded-1" href="{{ route('logout') }}"
                             onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                 {{ __('Logout') }}
                             </a>

                             <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                 @csrf
                             </form>
                          </div>
                       </div>
                     </div>
                   </li>
                   <!-- ------------------------------- -->
                   <!-- end profile Dropdown -->
                   <!-- ------------------------------- -->
                 </ul>
               </div>
             </div>
           </nav>

         </div>
       </header>
       <!--  Header End -->



       <div class="body-wrapper">
        @yield('content')

       </div>
       <button class="btn btn-danger p-3 rounded-circle d-flex align-items-center justify-content-center customizer-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
         <i class="icon ti ti-settings fs-7"></i>
       </button>

       <div class="offcanvas customizer offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
          <h4 class="offcanvas-title fw-semibold" id="offcanvasExampleLabel">
            Settings
          </h4>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body" data-simplebar style="height: calc(100vh - 80px)">
          <h6 class="fw-semibold fs-4 mb-2">Theme</h6>

          <div class="d-flex flex-row gap-3 customizer-box" role="group">
            <input type="radio" class="btn-check light-layout" name="theme-layout" id="light-layout" autocomplete="off" />
            <label class="btn p-9 btn-outline-primary rounded-2" for="light-layout">
              <i class="icon ti ti-brightness-up fs-7 me-2"></i>Light
            </label>

            <input type="radio" class="btn-check dark-layout" name="theme-layout" id="dark-layout" autocomplete="off" />
            <label class="btn p-9 btn-outline-primary rounded-2" for="dark-layout">
              <i class="icon ti ti-moon fs-7 me-2"></i>Dark
            </label>
          </div>

          <h6 class="mt-5 fw-semibold fs-4 mb-2">Theme Direction</h6>
          <div class="d-flex flex-row gap-3 customizer-box" role="group">
            <input type="radio" class="btn-check" name="direction-l" id="ltr-layout" autocomplete="off" />
            <label class="btn p-9 btn-outline-primary rounded-2" for="ltr-layout">
              <i class="icon ti ti-text-direction-ltr fs-7 me-2"></i>LTR
            </label>

            <input type="radio" class="btn-check" name="direction-l" id="rtl-layout" autocomplete="off" />
            <label class="btn p-9 btn-outline-primary rounded-2" for="rtl-layout">
              <i class="icon ti ti-text-direction-rtl fs-7 me-2"></i>RTL
            </label>
          </div>

          <h6 class="mt-5 fw-semibold fs-4 mb-2">Theme Colors</h6>

          <div class="d-flex flex-row flex-wrap gap-3 customizer-box color-pallete" role="group">
            <input type="radio" class="btn-check" name="color-theme-layout" id="Blue_Theme" autocomplete="off" />
            <label class="btn p-9 btn-outline-primary rounded-2 d-flex align-items-center justify-content-center" onclick="handleColorTheme('Blue_Theme')" for="Blue_Theme" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BLUE_THEME">
              <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-1">
                <i class="ti ti-check text-white d-flex icon fs-5"></i>
              </div>
            </label>

            <input type="radio" class="btn-check" name="color-theme-layout" id="Aqua_Theme" autocomplete="off" />
            <label class="btn p-9 btn-outline-primary rounded-2 d-flex align-items-center justify-content-center" onclick="handleColorTheme('Aqua_Theme')" for="Aqua_Theme" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AQUA_THEME">
              <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-2">
                <i class="ti ti-check text-white d-flex icon fs-5"></i>
              </div>
            </label>

            <input type="radio" class="btn-check" name="color-theme-layout" id="Purple_Theme" autocomplete="off" />
            <label class="btn p-9 btn-outline-primary rounded-2 d-flex align-items-center justify-content-center" onclick="handleColorTheme('Purple_Theme')" for="Purple_Theme" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="PURPLE_THEME">
              <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-3">
                <i class="ti ti-check text-white d-flex icon fs-5"></i>
              </div>
            </label>

            <input type="radio" class="btn-check" name="color-theme-layout" id="green-theme-layout" autocomplete="off" />
            <label class="btn p-9 btn-outline-primary rounded-2 d-flex align-items-center justify-content-center" onclick="handleColorTheme('Green_Theme')" for="green-theme-layout" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="GREEN_THEME">
              <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-4">
                <i class="ti ti-check text-white d-flex icon fs-5"></i>
              </div>
            </label>

            <input type="radio" class="btn-check" name="color-theme-layout" id="cyan-theme-layout" autocomplete="off" />
            <label class="btn p-9 btn-outline-primary rounded-2 d-flex align-items-center justify-content-center" onclick="handleColorTheme('Cyan_Theme')" for="cyan-theme-layout" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="CYAN_THEME">
              <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-5">
                <i class="ti ti-check text-white d-flex icon fs-5"></i>
              </div>
            </label>

            <input type="radio" class="btn-check" name="color-theme-layout" id="orange-theme-layout" autocomplete="off" />
            <label class="btn p-9 btn-outline-primary rounded-2 d-flex align-items-center justify-content-center" onclick="handleColorTheme('Orange_Theme')" for="orange-theme-layout" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ORANGE_THEME">
              <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-6">
                <i class="ti ti-check text-white d-flex icon fs-5"></i>
              </div>
            </label>
          </div>

          <h6 class="mt-5 fw-semibold fs-4 mb-2">Layout Type</h6>
          <div class="d-flex flex-row gap-3 customizer-box" role="group">
            <div>
              <input type="radio" class="btn-check" name="page-layout" id="vertical-layout" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary rounded-2" for="vertical-layout">
                <i class="icon ti ti-layout-sidebar-right fs-7 me-2"></i>Vertical
              </label>
            </div>
            <div>
              <input type="radio" class="btn-check" name="page-layout" id="horizontal-layout" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary rounded-2" for="horizontal-layout">
                <i class="icon ti ti-layout-navbar fs-7 me-2"></i>Horizontal
              </label>
            </div>
          </div>

          <h6 class="mt-5 fw-semibold fs-4 mb-2">Container Option</h6>

          <div class="d-flex flex-row gap-3 customizer-box" role="group">
            <input type="radio" class="btn-check" name="layout" id="boxed-layout" autocomplete="off" />
            <label class="btn p-9 btn-outline-primary rounded-2" for="boxed-layout">
              <i class="icon ti ti-layout-distribute-vertical fs-7 me-2"></i>Boxed
            </label>

            <input type="radio" class="btn-check" name="layout" id="full-layout" autocomplete="off" />
            <label class="btn p-9 btn-outline-primary rounded-2" for="full-layout">
              <i class="icon ti ti-layout-distribute-horizontal fs-7 me-2"></i>Full
            </label>
          </div>

          <h6 class="fw-semibold fs-4 mb-2 mt-5">Sidebar Type</h6>
          <div class="d-flex flex-row gap-3 customizer-box" role="group">
            <a href="javascript:void(0)" class="fullsidebar">
              <input type="radio" class="btn-check" name="sidebar-type" id="full-sidebar" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary rounded-2" for="full-sidebar">
                <i class="icon ti ti-layout-sidebar-right fs-7 me-2"></i>Full
              </label>
            </a>
            <div>
              <input type="radio" class="btn-check" name="sidebar-type" id="mini-sidebar" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary rounded-2" for="mini-sidebar">
                <i class="icon ti ti-layout-sidebar fs-7 me-2"></i>Collapse
              </label>
            </div>
          </div>

          <h6 class="mt-5 fw-semibold fs-4 mb-2">Card With</h6>

          <div class="d-flex flex-row gap-3 customizer-box" role="group">
            <input type="radio" class="btn-check" name="card-layout" id="card-with-border" autocomplete="off" />
            <label class="btn p-9 btn-outline-primary rounded-2" for="card-with-border">
              <i class="icon ti ti-border-outer fs-7 me-2"></i>Border
            </label>

            <input type="radio" class="btn-check" name="card-layout" id="card-without-border" autocomplete="off" />
            <label class="btn p-9 btn-outline-primary rounded-2" for="card-without-border">
              <i class="icon ti ti-border-none fs-7 me-2"></i>Shadow
            </label>
          </div>
        </div>
      </div>

       <script>
   function handleColorTheme(e) {
     document.documentElement.setAttribute("data-color-theme", e);
   }
 </script>
     </div>



   </div>
   <div class="dark-transparent sidebartoggler"></div>
   <!-- Import Js Files -->


   <script src="{{asset('js/script_dashboard/bootstrap.bundle.min.js')}}"></script>
   <script src="{{asset('js/script_dashboard/simplebar.min.js')}}"></script>
   <script src="{{asset('js/script_dashboard/app.init.js')}}"></script>
   <script src="{{asset('js/script_dashboard/theme.js')}}"></script>
   <script src="{{asset('js/script_dashboard/app.min.js')}}"></script>
   <script src="{{asset('js/script_dashboard/sidebarmenu.js')}}"></script>

   <!-- solar icons -->
   <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
   <script src="{{asset('js/script_dashboard/apexcharts.min.js')}}"></script>
   <script src="{{asset('js/script_dashboard/dashboard1.js')}}"></script>
   <script src="{{asset('js/script_dashboard/index.global.min.js')}}"></script>
   <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>



 </body>

 </html>
