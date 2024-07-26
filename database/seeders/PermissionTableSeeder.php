<?php


namespace Database\Seeders;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Permission;



class PermissionTableSeeder extends Seeder

{

    /**

     * Run the database seeds.

     *

     * @return void

     */

    public function run()

    {

       $permissions = [

           'role-list',

           'role-create',

           'role-edit',

           'role-delete',

            'company',

            'clients',
            'clients-ajoute',
            'clients-voir',
            'clients-modifier',
            'clients-supprimer',

            'catégorie',
            'catégorie-ajoute',
            'catégorie-modifier',
            'catégorie-supprimer',

            'paramètre',
            'paramètre-ajoute',
            'paramètre-modifier',
            'paramètre-supprimer',

            'tva',
            'tva-ajoute',
            'tva-modifier',
            'tva-supprimer',

            'mode paiement',
            'mode paiement-ajoute',
            'mode paiement-modifier',
            'mode paiement-supprimer',

            'information',
            'information-ajoute',
            'information-modifier',

            'utilisateur',
            'utilisateur-ajoute',
            'utilisateur-voir',
            'utilisateur-modifier',
            'utilisateur-supprimer',

            'rôles',
            'rôles-ajoute',
            'rôles-voir',
            'rôles-modifier',
            'rôles-supprimer',

            'stock',
            'stock-ajoute',
            'stock-modifier',
            'stock-supprimer',

            'vente',
            'vente-ajoute',
            'vente-voir',
            'vente-imprimer',

            'facture',
            'facture-voir',
            'facture-imprimer',

            'bordereau journalier',
            'bordereau journalier-recherche',

            'charge',
            'charge-ajoute',
            'charge-modifier',
            'charge-supprimer',

            'personnel',
            'personnel-ajoute',
            'personnel-paiement',
            'personnel-voir',
            'personnel-modifier',

            'suivi personnel',

            'recouverement',
            'recouverement-payé',

            'etat',
            'etat-recherche',

            'Cheque',
            'Cheque-voir',
            'Cheque-modifier',

            'Solde',
            'Solde-ajoute',
            'Solde-modifier',
            'Solde-supprimer',

            'Change',
            'Change-ajoute',
            'Change-modifier',
            'Change-supprimer',

            'Versement',
            'Versement-ajoute',
            'Versement-modifier',
            'Versement-supprimer',



        ];

        foreach ($permissions as $permission)
        {
            if (Permission::where('name', $permission)->doesntExist()) {
                Permission::create(['name' => $permission]);
            }
             //Permission::create(['name' => $permission]);

        }

    }

}
