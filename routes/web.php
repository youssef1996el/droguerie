<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TvaController;
use App\Http\Controllers\ModePaiementController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\RecouverementController;
use App\Http\Controllers\EtatController;
use App\Http\Controllers\BordereauController;

use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ChequeController;
use App\Http\Controllers\SoldecaisseController;
use App\Http\Controllers\AvoirController;
use App\Http\Controllers\VersementController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\GetMoneyController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['web','auth']], function ()
{
    Route::resource('roles',RoleController::class);
    Route::resource('users',UserController::class);

    Route::get('Dashboard'            ,[HomeController::class,'index'                         ]);
    Route::get('cleanup'              ,[HomeController::class,'cleanup'                       ]);
    Route::get('ChartCredit'          ,[HomeController::class,'ChartCredit'                   ]);
    Route::get('DisplayCreditBydate'  ,[HomeController::class,'DisplayCreditBydate'           ]);
    /******************************* Client      ***********************************************/
    Route::get('Client'              ,[ClientController::class,'index'                        ]);
    Route::get('FicheClient'         ,[ClientController::class,'getFicheClient'               ]);
    Route::get('ShowClient/{id}'     ,[ClientController::class,'ShowClient'                   ]);
    Route::post('StoreClient'        ,[ClientController::class,'StoreClient'                  ]);
    Route::post('UpdateClient'       ,[ClientController::class,'UpdateClient'                 ]);
    Route::post('TrashClient'        ,[ClientController::class,'TrashClient'                  ]);
    Route::get('StoreRemark'         ,[ClientController::class,'StoreRemark'                  ]);
    Route::post('StoreSolde'         ,[ClientController::class,'StoreSolde'                   ]);

    /******************************** End Client ***********************************************/


    /******************************** Company    ************************************************/
    Route::get('Company'             ,[CompanyController::class,'index'                        ]);
    Route::get('getCompany'          ,[CompanyController::class,'getCompany'                   ]);
    Route::post('StoreCompany'       ,[CompanyController::class,'StoreCompany'                 ]);
    Route::get('ShowCompany'         ,[CompanyController::class,'ShowCompany'                  ]);
    Route::post('EditCompany'        ,[CompanyController::class,'EditCompany'                  ]);
    /******************************** EndCompany ************************************************/


    /******************************** Stock     ************************************************/
    Route::get('Stock'               ,[StockController::class,'index'                          ]);
    Route::get('getStock'            ,[StockController::class,'getStock'                       ]);
    Route::post('StoreStock'         ,[StockController::class,'StoreStock'                     ]);
    Route::get('GetRowSelectedByTable',[StockController::class,"GetRowSelectedByTable"         ]);
    Route::post('UpdateStock'        ,[StockController::class,'UpdateStock'                    ]);
    Route::post('TrashStock'         ,[StockController::class,'TrashStock'                     ]);

    /******************************** End Stock ************************************************/


    /******************************** Order     ************************************************/
    Route::get('Order'               ,[OrderController::class,'index'                          ]);
    Route::get('DisplayProductStock' ,[OrderController::class,'DisplayProductStock'            ]);
    Route::get('sendDataToTmpOrder'  ,[OrderController::class,'sendDataToTmpOrder'             ]);
    Route::get('GetDataTmpOrderByClient',[OrderController::class,'GetDataTmpOrderByClient'     ]);
    Route::get('GetTotalByClientCompany',[OrderController::class,'GetTotalByClientCompany'     ]);
    Route::get('CheckQteProduct'     ,[OrderController::class,'CheckQteProduct'                ]);
    Route::post('TrashTmpOrder'      ,[OrderController::class,'TrashTmpOrder'                  ]);
    Route::post('StoreOrder'         ,[OrderController::class,'StoreOrder'                     ]);
    Route::get('GetMyVente'          ,[OrderController::class,'GetMyVente'                     ]);
    Route::get('invoices/{id}'      , [OrderController::class,'viewInvoice'                    ]);
    Route::get('Facture'             ,[OrderController::class,'Facture'                        ]);
    Route::get('ShowOrder/{id}'      ,[OrderController::class,'ShowOrder'                      ]);
    Route::get('ChangeQteTmpPlus'    ,[OrderController::class,'ChangeQteTmpPlus'               ]);
    Route::get('ChangeQteTmpMinus'   ,[OrderController::class,'ChangeQteTmpMinus'              ]);
    Route::get('getClientByCompany'  ,[OrderController::class,'getClientByCompany'             ]);
    Route::get('getUniteVenteByProduct',[OrderController::class,'getUniteVenteByProduct'       ]);
    Route::get('checkTableTmpHasDataNotThisClient',[OrderController::class,'checkTableTmpHasDataNotThisClient']);
    Route::get('changeAccessoireTmp',[OrderController::class,'changeAccessoireTmp'              ]);
    Route::get('ChangeQteByPress'   ,[OrderController::class,'changeQteByPress'                 ]);
    Route::post('TrashOrder'         ,[OrderController::class,'TrashOrder'                      ]);
    Route::get('verifiPaiement'      ,[OrderController::class,'verifiPaiement'                  ]);
    Route::get('ChangeLaDateVente'   ,[OrderController::class,'ChangeLaDateVente'               ]);
    Route::get('GetOrderAndPaiement/{id}' ,[OrderController::class,'GetOrderAndPaiement'        ]);
    Route::get('TableReglementByOrder/{id}',[OrderController::class,'TableReglementByOrder'     ]);
    Route::get('TablePaiementByOrder/{id}',[OrderController::class,'TablePaiementByOrder'       ]);
    Route::get('ConvertToFacture/{id}'    ,[OrderController::class,'ConvertToFacture'           ]);
    Route::get('GeneratedFactureRandom'  ,[OrderController::class,'GeneratedFactureRandom'      ]);
    Route::get('checkIsHAsFacture'        ,[OrderController::class,'checkIsHAsFacture'          ]);
    /******************************** End Order ************************************************/


    /******************************** Category  ************************************************/
    Route::get('Category'            ,[CategoryController::class,'index'                       ]);
    Route::post('StoreCategory'      ,[CategoryController::class,'StoreCategory'               ]);
    Route::get('FetchCategoryByCompanyActive' ,[CategoryController::class,'FetchCategoryByCompanyActive']);
    Route::post('UpdateCategory'       ,[CategoryController::class,'UpdateCategory'               ]);
    Route::post('TrashCategory'       ,[CategoryController::class,'TrashCategory'               ]);

    /******************************** End Category  ********************************************/
    /******************************** Setting       ********************************************/
    Route::get('Setting'              ,[SettingController::class,'index'                        ]);
    Route::get('FetchSetting'         ,[SettingController::class,'FetchSetting'                 ]);
    Route::post('StoreSetting'        ,[SettingController::class,'StoreSetting'                 ]);
    Route::post('UpdateSetting'        ,[SettingController::class,'UpdateSetting'               ]);
    Route::post('TrashSetting'        ,[SettingController::class,'TrashSetting'                 ]);
    Route::get('getNameProductByBonAndCategory',[SettingController::class,'getNameProductByBonAndCategory']);
    Route::get('getSettingByID',[SettingController::class,'getSettingByID']);

    /******************************** End Setting   ********************************************/
    /********************************  Tva          ********************************************/
    Route::get('Tva'                 ,[TvaController::class,'index'                            ]);
    Route::post('StoreTva'           ,[TvaController::class,'StoreTva'                         ]);
    Route::get('getTva'              ,[TvaController::class,'getTva'                           ]);
    Route::post('trashTva'           ,[TvaController::class,'trashTva'                         ]);
    Route::post('UpdateTva'          ,[TvaController::class,'UpdateTva'                        ]);
    /******************************** End Tva       ********************************************/
    /********************************  Mode Paiement ********************************************/
    Route::get('ModePaiement'        ,[ModePaiementController::class,'index'                   ]);
    Route::get('FetchModePaiementByCompanyActive',[ModePaiementController::class,'FetchModePaiementByCompanyActive']);
    Route::post('StoreModePaiement'  ,[ModePaiementController::class,'StoreModePaiement']);
    Route::post('TrashModePaiement'  ,[ModePaiementController::class,'TrashModePaiement']);
    Route::post('UpdateModePaiement' ,[ModePaiementController::class,'UpdateModePaiement']);
    /********************************  End Mode Paiement ****************************************/

    /********************************  Charge **************************************************/
    Route::get('Charge'             ,[ChargeController::class,'index'                          ]);
    Route::post('StoreCharge'       ,[ChargeController::class,'StoreCharge'                    ]);
    Route::post('updateCharge'      ,[ChargeController::class,'updateCharge'                   ]);
    Route::post('TrashCharge'       ,[ChargeController::class,'TrashCharge'                   ]);
    Route::post('ChangeDateCharge'       ,[ChargeController::class,'ChangeDateCharge'                   ]);
    /********************************  End Charge **********************************************/


    /********************************  Recouverement **************************************************/
    Route::get('Recouverement'              ,[RecouverementController::class,'index'                        ]);
    Route::get('GetRecouvementClient'       ,[RecouverementController::class,'GetRecouvementClient'         ]);
    Route::get('GetDataSelectedRecouvement' ,[RecouverementController::class,'GetDataSelectedRecouvement'   ]);
    Route::get('Suivirecouverement'         ,[RecouverementController::class,'Suivirecouverement'           ]);
    Route::post('StoreRecouvement'          ,[RecouverementController::class,'StoreRecouvement'             ]);
    Route::get('Listcredit'                 ,[RecouverementController::class,'Listcredit'                   ]);
    Route::post('DeletePaiement'            ,[RecouverementController::class,'DeletePaiement'               ]);
    Route::get('ListPaiement'               ,[RecouverementController::class,'ListPaiement'                 ]);
    /******************************** end Recouverement ***********************************************/

    /********************************  Etat **********************************************************/
    Route::get('Etat'                   ,[EtatController::class,'index'                             ]);
    Route::get('SearchEtat'             ,[EtatController::class,'SearchEtat'                        ]);
    Route::get('SearchEtatTable'        ,[EtatController::class,'SearchEtatTable'                   ]);
    Route::get('EtatProduction'         ,[EtatController::class,'EtatProduction'                    ]);
    Route::get('TotalUniteByDate'       ,[EtatController::class,'TotalUniteByDate'                  ]);
    Route::get('EtatByClient'           ,[EtatController::class,'EtatByClient2'                      ]);
    /******************************** End Etat *******************************************************/

    /********************************  Bordereau ********************************************************/
    Route::get('Borderau'             ,[BordereauController::class,'index'                             ]);
    Route::get('GetMyBordereau'       ,[BordereauController::class,'GetMyBordereau'                    ]);
    /******************************** End  Bordereau ****************************************************/



    /********************************  end BonEntre ****************************************************/

    /********************************  Personnel ********************************************************/
    Route::get('Personnel'           ,[PersonnelController::class,'index'                              ]);
    Route::get('getFichePersonnel'   ,[PersonnelController::class,'getFichePersonnel'                  ]);
    Route::post('StorePersonnel'     ,[PersonnelController::class,'StorePersonnel'                     ]);
    Route::post('UpdatePersonnel'    ,[PersonnelController::class,'UpdatePersonnel'                    ]);
    Route::get('SuiviPersonnel/{id}' ,[PersonnelController::class,'SuiviPersonnel'                     ]);
    Route::get('SuiviPersonnel'      ,[PersonnelController::class,'SuiviPersonnelWithoutID'            ]);
    Route::get('getFichePersonnelByPersonnel',[PersonnelController::class,'getFichePersonnelByPersonnel']);
    Route::post('StorePaiementPersonnel',[PersonnelController::class,'StorePaiementPersonnel'           ]);
    Route::get('deletereglementpersonnel',[PersonnelController::class,'deletereglementpersonnel'        ]);
    /******************************** End Personnel *****************************************************/


    /********************************  Info  *************************************************************/
    Route::get('Info'                ,[InfoController::class,'index'                                    ]);
    Route::get('FetchInformation'    ,[InfoController::class,'FetchInformation'                         ]);
    Route::post('StoreInformation'   ,[InfoController::class,'StoreInformation'                         ]);
    Route::post('UpdateInformation'  ,[InfoController::class,'UpdateInformation'                         ]);

    /******************************** End  Info  *********************************************************/

    /********************************** Cheque  **********************************************************/
    Route::get('Cheque'              ,[ChequeController::class,'index'                                  ]);
    Route::get('ChangeStatus'        ,[ChequeController::class,'ChangeStatus'                           ]);
    /********************************** End cheque *******************************************************/

    /********************************** Solde Caisse *****************************************************/
    Route::get('SoldeCaisse'         ,[SoldecaisseController::class,'index'                             ]);
    Route::get('getSoldeCaisse'      ,[SoldecaisseController::class,'getSoldeCaisse'                    ]);
    Route::post('StoreSoldeCaisse'   ,[SoldecaisseController::class,'StoreSoldeCaisse'                  ]);
    Route::post('UpdateSoldeCaisse'  ,[SoldecaisseController::class,'UpdateSoldeCaisse'                 ]);
    Route::post('TrashSoldeCaisse'  ,[SoldecaisseController::class,'TrashSoldeCaisse'                 ]);
    /******************************* End Solde Caisse *****************************************************/

    /*********************************** Avoir ************************************************************/
    Route::get('Avoir'                       ,[AvoirController::class,'index'                           ]);
    Route::get('GetDataTmpAvoirByClient'     ,[AvoirController::class,'GetDataTmpAvoirByClient'         ]);
    Route::get('GetTotalByClientCompanyaVoir',[AvoirController::class,'GetTotalByClientCompanyaVoir'    ]);
    Route::get('GetOrderClient'              ,[AvoirController::class,'GetOrderClient'                  ]);
    Route::get('checkClientHasOrder'         ,[AvoirController::class,'checkClientHasOrder'             ]);
    Route::get('GetProductByOrderClient'     ,[AvoirController::class,'GetProductByOrderClient'         ]);
    Route::post('StoreTmpAvoir'              ,[AvoirController::class,'StoreTmpAvoir'                   ]);
    Route::get('DisplayProductsTableTmpAvoir',[AvoirController::class,'DisplayProductsTableTmpAvoir'    ]);
    Route::get('CheckQteChangeNotSuperQteOrderAndUpdateQte',[AvoirController::class,'CheckQteChangeNotSuperQteOrderAndUpdateQte']);
    Route::get('TotalTmpAvoir'               ,[AvoirController::class,'TotalTmpAvoir'                   ]);
    /*********************************** End Avoir ********************************************************/

    /***********************************  versement ********************************************************/
     Route::get('versement'                  ,[VersementController::class,'index'                       ]);
     Route::post('StoreVersement'            ,[VersementController::class,'StoreVersement'              ]);
     Route::post('updateVersement'           ,[VersementController::class,'updateVersement'             ]);
     Route::post('TrashVersement'           ,[VersementController::class,'TrashVersement'               ]);
    /*********************************** End versement *****************************************************/


    /************************************ Devis  ***********************************************************/
    Route::get('Devis'                                  ,[DevisController::class,'index'                                 ]);
    Route::get('GetDataTmpDevisByClient'                ,[DevisController::class,'GetDataTmpDevisByClient'               ]);
    Route::get('GetMyDevis'                             ,[DevisController::class,'GetMyDevis'                            ]);
    Route::get('checkTableTmpHasDataNotThisClientDevis' ,[DevisController::class,'checkTableTmpHasDataNotThisClientDevis']);
    Route::get('GetTotalByClientCompanyDevis'           ,[DevisController::class,'GetTotalByClientCompanyDevis'          ]);
    Route::get('checkQteProductDevis'                   ,[DevisController::class,'checkQteProductDevis'                  ]);
    Route::get('sendDataToTmpDevis'                     ,[DevisController::class,'sendDataToTmpDevis'                    ]);
    Route::get('ChangeQteTmpMinusDevis'                 ,[DevisController::class,'ChangeQteTmpMinusDevis'                ]);
    Route::get('ChangeQteTmpPlusDevis'                  ,[DevisController::class,'ChangeQteTmpPlusDevis'                 ]);
    Route::get('changeAccessoireTmpDevis'               ,[DevisController::class,'changeAccessoireTmpDevis'              ]);
    Route::get('ChangeQteByPressDevis'                  ,[DevisController::class,'ChangeQteByPressDevis'                 ]);
    Route::post('TrashTmpDevis'                         ,[DevisController::class,'trashTmpDevis'                         ]);
    Route::post('StoreDevis'                            ,[DevisController::class,'StoreDevis'                            ]);
    Route::get('ShowDevis/{id}'                         ,[DevisController::class,'ShowDevis'                             ]);
    Route::get('invoicesDevis/{id}'                     ,[DevisController::class,'invoicesDevis'                         ]);
    Route::post('TrashDevis'                            ,[DevisController::class,'TrashDevis'                            ]);
    /************************************ End devis ********************************************************/


    /************************************ revenus ***********************************************************/
    Route::get('revenus'                                ,[GetMoneyController::class,'index'                   ]);
    Route::post('StoreRevenus'                          ,[GetMoneyController::class,'StoreRevenus'            ]);
     Route::post('updateRevenus'                        ,[GetMoneyController::class,'updateRevenus'           ]);
     Route::post('TrashRevenus'                         ,[GetMoneyController::class,'TrashRevenus'            ]);
    /************************************ revenus ***********************************************************/




});
