<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Artisan;
session_start();
Route::get('/', function() {
    return View::make('index');
});

//Routes To Welcome Pages~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::get('/features', function() {
    return View::make('WelcomePages.Features');
});
Route::get('/contact', function() {
    return View::make('WelcomePages.Contact');
});
Route::get('/memberarea', function() {
    return View::make('WelcomePages.MembersArea', ['error' => 'null']);
});
Route::get('/products', function() {
    return View::make('WelcomePages.Products');
});

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Login | UserRegistration | LabRegistration~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::post('/userlogin', 'UserController@login');
Route::get('/wilogout', 'UserController@logout');

Route::post('/selectLabforLogin', 'UserController@SelectLabAndLogin');

Route::get('/LabRegister', function() {
    return View::make('LabRegister');
});

//tmp
Route::get('/adminreg', function() {
    return View::make('AdminReg');
});
Route::get('/selectpackage', function() {
    return View::make('FeaturesRequest');
});
//


Route::post('/regsuccess', function() {
    return View::make('RegSuccess');
});

Route::get('/wimain', function() {
    return View::make('WiMain');
});


Route::post('/registerlab', 'LabController@RegisterLab');

Route::post('/adminregister', 'UserController@RegisterAdmin');

Route::post('/selectPackege', 'LabController@SelectPackege');

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//NaviPanel Links ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::get('/addpatient', function() {
    return View::make('WiaddPatient');
});
Route::get('/addpatient/viewptients', function() {
    return View::make('WiviewPatients');
});

Route::get('/bulkadding', function() {
    return Redirect::to('http://slt.appexsl.com/synergybio_bulk/Views/bulk_sheet.php');
});

Route::get('/addpatient/enterresults', function() {
    return View::make('WienterResults');
});

Route::get('/viewptients', function() {
    return View::make('WiviewPatients');
});
//Route::get('/viewptients/addpatient', function() {
//    return View::make('WiaddPatient');
//});
//Route::get('/viewptients/enterresults', function() {
//    return View::make('WienterResults');
//});

Route::get('/enterresults', function() {
    return View::make('WienterResults');
});
Route::get('/labconfig', function() {
    return View::make('WilabConfigs');
});
Route::get('/testmanage', function() {
    return View::make('Witestmanage');
});
Route::get('/testgroups', function() {
    return View::make('Witestgroups');
});
Route::get('/stock', function() {
    return View::make('Wistock');
});
Route::get('/stock/{option}', function($option) {
    return View::make('Wistock')->with('option', $option);
});
Route::get('/materials', function() {
    return View::make('WimaterialManagement');
});

Route::get('/wastage', function() {
    return View::make('WiwastageManagement');
}); 

Route::get('/financesum', function() {
    return View::make('WiFinance');
});

Route::get('/expmanage', function() {
    return View::make('WiExpenses');
});








// **************************************Manula's Development Routes************************************************************
//~~~~~~~~~~~~~~~~~~~~~~~~Route To DOC Refference Page~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::get('/doc-reference', function () {
    return View::make('DocRefference');
});

Route::get('/getAllRefference', 'DocRefferenceController@getAllDetails');
// Route to save reference data
Route::post('/saveReference', 'DocRefferenceController@save_Reference');
// Route to delete reference data
Route::post('/deleteReference', 'DocRefferenceController@delete_Reference');


// Route to update reference data
Route::post('/updateReference', 'DocRefferenceController@update_Reference');
// Route to fetch the filtered records
Route::get('/getAllRefference', 'DocRefferenceController@getAllDetails');
//Route to view invoice count records
Route::get('/getInvoiceCountForReference', 'DocRefferenceController@getInvoiceCountFor_Reference');

// Route to Merge Refference
Route::post('/mergeReference', 'DocRefferenceController@merge_Reference');








// **************************************Manula's Development Routes************************************************************
//~~~~~~~~~~~~~~~~~~~~~~~~Route To Create Test Package Page~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Route::get('/createTestPackage', function () {
    return View::make('PackageCreate');
});


// Route to get all created test packages
Route::get('/getAllTestPackages', 'PackageCreateController@getAllTestPackages');

 //Route to save test package data
Route::post('/savePackage', 'PackageCreateController@save_Package');

// Route to fetch tests related to a clicked package
Route::get('/getTestsForPackage', 'PackageCreateController@getTestsForPackage');

// Route to load package test package data
Route::get('/loadPackageTests', 'PackageCreateController@loadPackageTests');

// Route to Update package
Route::post('/updatePackage', 'PackageCreateController@update_Package');


// Route to delete a package
Route::post('/deletePackage', 'PackageCreateController@delete_Package');

// Route to Active a package
Route::post('/activePackage', 'PackageCreateController@actiate_Package');




// **************************************Manula's Development Routes************************************************************
//~~~~~~~~~~~~~~~~~~~~~~~~Route To Create Discount Page~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Route::get('/createDiscount', function () {
    return View::make('DiscountCreate');
});
Route::get('/getAllInvoices', 'ViewInvoiceController@getAllInvoices');
//~~~~~~~~~~~~~~~~~~~~~~~~Route To load discount data~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::get('/getAllDiscount', 'DiscountCreateController@getAllDetails');

// Route to save Discount data
Route::post('/saveDiscount', 'DiscountCreateController@save_Discount');

// Route to delete Discount data
Route::post('/deleteDiscount','DiscountCreateController@delete_Discount');

// Route to update Discount data
Route::post('/updateDiscount', 'DiscountCreateController@update_Discount');


// **************************************Manula's Development Routes************************************************************
//~~~~~~~~~~~~~~~~~~~~~~~~Route To Branch WiseTest Mapping Page~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Route::get('/branchTestMapping', function () {
    return View::make('BranchWiseTestMapping');
});

Route::get('getAllBranchTests', 'BranchWiseTestMappingController@getAllBranchWiseTests');

Route::get('searchAllBranchTests', 'BranchWiseTestMappingController@getAllBranchWiseTests');

Route::get('getAllBranches', 'BranchWiseTestMappingController@getBranch_Details');

Route::post('saveBranch', 'BranchWiseTestMappingController@save_Branch');

Route::post('updateBranch', 'BranchWiseTestMappingController@update_Branch');

Route::post('updateTestBranches', 'BranchWiseTestMappingController@update_BranchTests');

Route::post('RemoveTestFromBranch', 'BranchWiseTestMappingController@delete_BranchTests');




// **************************************Manula's Development Routes************************************************************
//~~~~~~~~~~~~~~~~~~~~~~~~Route To Test Parameter Mapping Page~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


Route::get('/testParaMapping', function () {
    return View::make('TestParameterMapping');
});

Route::get('getAllSamplesParaTable', 'TestParameterMappingController@getAll_SampleDetails');

Route::post('updateTestParameters', 'TestParameterMappingController@updateTestParmeters');


// **************************************Manula's Development Routes************************************************************
//~~~~~~~~~~~~~~~~~~~~~~~~Route To Bulk Payment Update Page~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Route::get('/bulckPaymentUpdate', function () {
    return View::make('BulkPaymentUpdate');
});

Route::get('getAllSamples', 'BulkPaymentUpdateController@getAllSample_Details');
Route::post('updatePayments', 'BulkPaymentUpdateController@update_Payments');







// **************************************Manula's Development Routes************************************************************
//~~~~~~~~~~~~~~~~~~~~~~~~Route To Sample Container Configuration Page~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Route::get('/sampleContainerConfig', function () {
    return View::make('SampleContainerConfig');
});

// Route to fetch the filtered records
Route::get('/getAllcontainerdata', 'SampleContainerConfigController@getAllDetails');

//update sample containeer
Route::post('/updateContainers', 'SampleContainerConfigController@updateContainers');



// **************************************Manula's Development Routes************************************************************
//~~~~~~~~~~~~~~~~~~~~~~~~Route To Patient registration Page~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Route::get('/patientRegistration', function () {
    return View::make('PatientRegistration');
});

Route::get('/getCurrentSampleNumber', 'PatientRegistrationController@loadSampleNumber');
Route::get('/getTests', 'PatientRegistrationController@loadBrachWiceTest');
Route::get('/getPackageTests', 'PatientRegistrationController@loadPackageTests');
Route::post('/savePatient', 'PatientRegistrationController@savePatientDetails');
Route::get('/getAllUsers', 'PatientRegistrationController@getAllUsers');
Route::get('/getUserDetailsByTP', 'PatientRegistrationController@getUserDetailsByTP');
Route::get('/getSelectedInvoice', 'PatientRegistrationController@getSampleTestData');
Route::get('/getRefCode', 'PatientRegistrationController@getRefCode');
Route::get('/getSearchPatient', 'PatientRegistrationController@getSearchSampleData');
Route::get('/get_Single_Barcode', 'PatientRegistrationController@getSingleBarcode');
Route::post('/checkSampleNo', 'PatientRegistrationController@checkSampleNo');
Route::get('getPatientDetailsBySample', 'PatientRegistrationController@getPatientDetailsBySample');
Route::get('getLastPatientDetails', 'PatientRegistrationController@getLastPatientDetails');
Route::post('updatePatientDetails', 'PatientRegistrationController@updatePatientDetails');
Route::get('/getRefName', 'PatientRegistrationController@getRefName');
Route::get('getTestParametersByTGID', 'PatientRegistrationController@getTestParametersByTGID');
Route::post('remove-barcode', 'PatientRegistrationController@removeBarcode');
Route::get('/barcode-feature-checking', 'PatientRegistrationController@barcodeFeatureChecking');
Route::get('/reporting-feature-checking', 'PatientRegistrationController@reportingFeatureChecking');
Route::get('/email-feature-checking', 'PatientRegistrationController@emailFeatureChecking');
Route::get('/whatsapp-feature-checking', 'PatientRegistrationController@whatsappFeatureChecking');
Route::get('/patientDetailsEditing-feature-checking', 'PatientRegistrationController@patientDetailsEditingFeatureChecking');
Route::get('/getRefByCode', 'PatientRegistrationController@getRefByCode');
Route::get('/token-feature-checking', 'PatientRegistrationController@TokenFeatureChecking');












// **************************************Manula's Development Routes************************************************************
//~~~~~~~~~~~~~~~~~~~~~~~~Route To View Invoice Page~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Route::get('/viewinvoices', function() {
    return View::make('ViewInvoice');
});
Route::get('getSampleTestData', 'ViewInvoiceController@getSampleTestData');
Route::post('cancelInvoice', 'ViewInvoiceController@cancelInvoice');
Route::get('getCashierInvoiceSummary', 'ViewInvoiceController@getCashierBalanceData');
Route::post('getInvoiceArray', array('uses' => 'ViewInvoiceController@getInvoiceArray'));







// **************************************Manula's Development Routes************************************************************
//~~~~~~~~~~~~~~~~~~~~~~~~Route To Invoice payment Page~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::get('/invoicePayments', function() {
    return View::make('invoicePayment');
});


Route::get('getAllpayments', 'invoicePaymentController@getAllPayments');
Route::get('getInvoicePatientDetails', 'invoicePaymentController@loadInvoicePatientDetails');
Route::post('savePayment', 'invoicePaymentController@savePayment');
Route::post('deletePayment', 'invoicePaymentController@deletePayment');

////~~~~~~~~~~~~~~~~~~~~~~~~text code buttons route~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Route::get('/get-test-codes', 'PatientRegistrationController@getTestCodes');



//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


Route::get('/PatientHistoryView', function () {
    return View::make('PatientHistoryView');
});

Route::get('getAllPatientHistoryRecords', 'PatientHistoryViewController@getAllPatientHistoryRecords');






























































































// ************************************************************************************************
//new Update
//Route::get('/financestat',function(){
//    return View::make('WiFinanceStatistics');
//});
//Route::get('/showgrns',function(){
//    return View::make('WiGRN');
//});
//Route::get('/managegrns',function(){
//    return View::make('WiGRNMan');
//});
//
//Route::post('/managegrns', 'GRNController@GRNSubmit');
//
//Route::get('/empprofile',function(){
//    return View::make('WiEmpProfile');
//});
//Route::get('/empmanagement',function(){
//    return View::make('WiEmployeeMan');
//});
//Route::get('/labprofile',function(){
//    return View::make('WiLabProfile');
//});
//~~~~~~~~~~~~~~~~~~~~~~~~
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Settings Panel Links~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::get('/addpconfig', function() {
    return View::make('WiaddPFormConfigs');
});
Route::get('/reportconfig', function() {
    return View::make('WireportConfigs');
});

Route::get('/loginlog', function() {
    return View::make('WiLoginLog');
});

Route::post('/loadNotifications', 'NotificationController@searchNotifications');


//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//add Patient | View Patients | View OP ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::post('/regPatient', 'PatientController@registerPatient');
Route::post('addpatient/regPatient', 'PatientController@registerPatient');

Route::post('/printinvoice', function() {
    return View::make('Reports.Invoice');
});

//Route::get('/addpatient/{id}', function($id) {
//    return View::make('WiaddPatient')->with('pid', $id);
//});

Route::get('/viewOP', function() {
    return View::make('WiViewOP');
});

Route::get('/bulkenter', function() {
    return View::make('WiBulkEnter');
});


Route::get('/addpatientto', 'PatientController@addSampleToPatient');
Route::post('/searchPHistory', 'PatientController@loadSuggestions');
Route::post('/getlastpatient', 'PatientController@LoadLastPatient');
Route::post('/SearchPatientView', 'PatientController@SearchPatientView');
Route::post('/SearchPatientViewbulk', 'PatientController@SearchPatientViewforBulk');
Route::post('/viewOPSubmit', 'PatientController@manageOnePatient');
Route::post('/selectPTestbyDate', 'SampleController@getTestbyDate');
Route::post('/viewOPSampleSubmit', 'SampleController@updateTestResult');

Route::post('/updateResultBulk', 'SampleController@updateTestResultBulk');
Route::post('/updateDetailsBulk', 'SampleController@updateDetailsBulk');

Route::post('/acceptsample', 'SampleController@acceptSample');

Route::get('/patientworksheet/{id}/{date}', function($id, $date) {

//    $arr = explode("#", $id);
//    return $id." ".$date;
    return View::make('Reports.PatientWorkSheet')->with('sno', $id)->with('date', $date);
});


//client
Route::get('/regPViaC', 'PatientController@registerPatientViaClient');
Route::get('/regPViaCbulk', 'PatientController@registerPatientViaClientBulk');
Route::get('/SearchPatientView', 'PatientController@SearchPatientViewGET');
Route::get('/SearchPatientViewNew', 'PatientController@SearchPatientViewGETNew'); 
Route::get('/SearchPatientViewOneSample', 'PatientController@SearchPatientViewOneSample'); 
Route::get('/viewOPClient', 'SampleController@ViewOPGET');
Route::get('/updateOPClient', 'SampleController@updateOPClient');
Route::get('/getlastpatient', 'PatientController@LoadLastPatientGET');
Route::get('/searchcontacts', 'PatientController@searchcontacts');


//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Enter Test Results~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::post('SearchSampleByDtnSno', 'SampleController@searchSample');
Route::post('UpdateTestResults', 'SampleController@updateSample');

Route::get('lisupdate', 'LISController@updateSample');
Route::get('lisupdate8', 'LISController@updateSample8');
 
Route::post('lisupdategraph', 'LISController@updateSampleGraph');

Route::get('lisupdatemexKX21', 'LISController@updateSampleSysmexKX21');
Route::get('lisupdate12', 'LISController@updateSampleTOSOHAIA360');


Route::post('SearchSampleLIS', 'LISController@searchSample');
Route::post('markblooddrew', 'SampleController@enterBloodDrew');
Route::post('markreportcollected', 'SampleController@enterReportCollected');
Route::post('loadPendingSamples_er', 'SampleController@loadPendings');

Route::post('reportauth', 'SampleController@reportAuthentication');

//Route::get('/lisupdate', function() {
//    return View::make('lisupdate');
//});

Route::get('/printreport/{id}', function($id) {
//    return View::make('Reports.TestingReport')->with('lpsid', $id);

    $arr = explode("&", $id);

    $lid = $_SESSION["lid"];
    return View::make('Reports.TestingReports.tr' . $lid)->with('lpsid', $arr[0])->with('repHead', $arr[1]);
});

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//labconfigurations~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::post('addpatientformconfig', 'LabConfigsController@updatepatientaddformconfigs');
Route::post('addreportconfig', 'LabConfigsController@updatereportconfigs');
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Test Management // Test Group~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::post('managetestings', 'TestController@manageTest');
Route::post('addreference', 'TestController@addReferance'); 
Route::post('loadreference', 'TestController@loadReferances'); 
Route::post('deleteReferenceRange', 'TestController@deleteReferances');

Route::post('testGroupsubmit', 'TestGroupController@manageTestGroups');
Route::post('analyzermanagesubmit', 'AnalyzerController@manageAnalyzers');
Route::post('gettgcomment', 'TestGroupController@getTGComment');
Route::post('updatetgcomment', 'TestGroupController@updateComment');
Route::post('getTGCosts', 'TestGroupController@getTGCosts');  
Route::post('addTGCosts', 'TestGroupController@addTGCosts');  
Route::post('removeTGCosts', 'TestGroupController@removeTGCosts');  
Route::post('toAllTests', 'TestGroupController@toAllTests');   
Route::post('toAllCenters', 'TestGroupController@toAllCenters');  
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Stock Management // Material Manage~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::post('/MatManSubmit', 'MaterialController@submit');
Route::post('MatManSubmitTest', 'MaterialController@getMatTests');
// Route::get('/MatManSubmitTest', 'MaterialController@getMatTests');

Route::get('/WimaterialManagement', function() {
    return View::make('WimaterialManagement');
});

Route::post('/stocksubmit', 'StockController@submit');

Route::post('/searchWastages', 'WasteController@searchWastages');

Route::post('/Wastagessubmit', 'WasteController@submit');
 
Route::post('/grnmaintain', 'GRNController@manageGRN');

Route::post('/searchgrn', 'GRNController@ViewGRNs'); 
Route::post('searchgrn', 'GRNController@ViewGRNs'); 

Route::get('/Wistock', function() {
    return View::make('Wistock');
});

Route::get('/wigrn', function() {
    return View::make('WiGRN');
});

Route::get('/wiviewgrn', function() {
    return View::make('WiViewGRN');   
});


//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//finance controller~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::post('/financecontroller', 'FinancesummeryController@submit');

Route::get('/WiFinance', function() {
    return View::make('WiFinance');
});
Route::post('/financeReport', function() {
    if ($_REQUEST["submit"] == "Genarate Detailed Report") {
        return View::make('Reports.FinanceReport_Detailed');
    } else if ($_REQUEST["submit"] == "Genarate Patient Details") {
        return View::make('Reports.Patient_Detailed');
    } {
        return View::make('Reports.FinanceReport');
    }
});
Route::post('/ipsummaryReport', function() {
    if ($_REQUEST["submit2"] == "Invoice Payment Summary") {
        return View::make('Reports.InvoicePaymentSummaryReport');
    } else if ($_REQUEST["submit2"] == "Due Payments Report") {
        return View::make('Reports.DuePaymentReport');
    }
});

Route::post('/getLabRefferences', 'FinanceController@getRefferences');
Route::post('/getLabBranches', 'FinanceController@getBranches');
Route::post('/getLabTests', 'FinanceController@getTests');

Route::post('/getPaymentMethods', 'FinanceController@getPaymentMethodTypes');

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Employee Managemnt~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::get('/manageemployee', function() {
    return View::make('WiEmployeeMan');
});
Route::get('EmpMan', 'EmployeeController@manageEmployee');
Route::post('EmpMan', 'EmployeeController@manageEmployee');
Route::get('loadUserPrivillages', 'EmployeeController@getPrivilleges');
Route::post('updatesignimage', 'EmployeeController@updateSignImage');


//Expenses~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::post('expensessubmit', 'ExpensesController@manageExpenses');
Route::post('/expensessearch', function() {
    return View::make('WiExpenses')->with('date1', "OKOKOK");
});

Route::post('searchexpenses', 'ExpensesController@searchExpenses');

Route::post('searchexpensesbillc', 'ExpensesController@getBillAmunt');

//for preview report with heading
Route::get('/printreportWithHeading/{id}', function($id) {

    
    $arr = explode("&", $id);
    
    $lid = $_SESSION["lid"];
    return View::make('Reports.TestingReports.tr'.$lid)->with('lpsid', $arr[0])->with('repHead',$arr[1])->with('onlprep',$arr[2]);
});

//client
Route::get('getFinanceSummery', 'FinanceController@getDayFinanceSummeryClient');
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//report view for patient
//Route::get('/report/{id}', function($id) {
//    
//    //min and max date for selecting range
//    $mindate = date('Y-m-d',strtotime("-30 days"));
//    //min and max date for selecting range
//    $maxdate = date('Y-m-d',strtotime("0 days"));
//    
//    return View::make('WiViewPReports')->with('sno', $id)->with('mindate',$mindate)->with('maxdate',$maxdate);
//});

Route::get('/report/{lid}/{id}/{date}', function($lid, $id, $date) {

    //min and max date for selecting range
    $mindate = date('Y-m-d', strtotime("0 days"));
    //min and max date for selecting range 
    $maxdate = date('Y-m-d', strtotime("0 days"));

    return View::make('WiViewPReports')->with('sno', $id)->with('lid', $lid)->with('sdate', $date)->with('mindate', $mindate)->with('maxdate', $maxdate);
});

Route::get('/report', function() {

    //min and max date for selecting range
    $mindate = date('Y-m-d', strtotime("-30 days"));
    //min and max date for selecting range
    $maxdate = date('Y-m-d', strtotime("0 days"));

    return View::make('WiViewPReports')->with('mindate', $mindate)->with('maxdate', $maxdate);
});

Route::post('/getPRDetails', 'PatientReporting@getDetails');
Route::post('/report/getPRDetails', 'PatientReporting@getDetails');

Route::post('/getPRcheck', 'PatientReporting@checkDetails');
Route::post('/report/getPRcheck', 'PatientReporting@checkDetails');



Route::get('/printreportpvd/{id}', function($id) {
//  return View::make('Reports.TestingReport')->with('lpsid', $id);

    $arr = explode("&", $id);

    $lid = $arr[2];

    $_SESSION['lid'] = $lid;
    $_SESSION['luid'] = $lid;

    return View::make('Reports.TestingReports.tr' . $lid)->with('lpsid', $arr[0])->with('repHead', $arr[1])->with('onlprep', 'true');
});

Route::get('/report/printreportpvd/{id}', function($id) {
//  return View::make('Reports.TestingReport')->with('lpsid', $id);

    $arr = explode("&", $id);

    $lid = $arr[2]; //this is temporary. need a method to auto detect lid 

    $_SESSION['lid'] = $lid;
    $_SESSION['luid'] = $lid;

    return View::make('Reports.TestingReports.tr' . $lid)->with('lpsid', $arr[0])->with('repHead', $arr[1])->with('onlprep', 'true');
});
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// send SMS ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Route::post('/sendsms', 'SMSController@sendSMS');
Route::get('/smslog', function() {
    return View::make('WiSMSLog');
});

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// send Email ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Route::post('/sendemail', 'EmailController@sendEmail');
Route::get('/emaillog', function() {
    return View::make('WiEmailLog');
});

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Excel Export ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Route::post('/export_excel', function() {

    $_SESSION["exportdata"] = $_POST["data"];

    echo $_SESSION["exportdata"];
});

Route::get('/exported_report', function() {
    return View::make('Reports.export_report');
});
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    echo "Cache Cleared!";
}); 


//Print Invoice Route~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Route::get('/printinvoice/{id}', function($id) {

//     $arr = explode("&", $id);

//     $lid = $_SESSION["lid"];
    
    
//     return View::make('Invoices.Inv' . $lid)->with('sno', $arr[0])->with('date', $arr[1]);
// });

// Default invoice route
Route::get('/printinvoice/{id}', function($id) {
    $arr = explode("&", $id);
    $lid = $_SESSION["lid"];
    return View::make('Invoices.Inv' . $lid)
        ->with('sno', $arr[0])
        ->with('date', $arr[1]);
});

// Claim Bill invoice route
Route::get('/printinvoice/claim/{id}', function($id) {
    $arr = explode("&", $id);
    $lid = $_SESSION["lid"];
    return View::make('Invoices.InvClaimBill' . $lid)
        ->with('sno', $arr[0])
        ->with('date', $arr[1]);
});

//Token print route

Route::get('/printtoken/{id}', function($id) {
    $arr = explode(",", $id); 
    $lid = $_SESSION["lid"];
    return View::make('Tokens.tokenLab' . $lid)
        ->with('sno', $arr[0])
        ->with('date', $arr[1]);
});



//Print Barcode Route~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::get('/printBarcode/{id}', function($id) {

    $arr = explode("&", $id);

    $lid = $_SESSION["lid"];
    
    
    return View::make('Barcodes.Common_Barcode' )->with('sno', $arr[0])->with('date', $arr[1])->with('isGroup', $arr[2])->with('tgid', $arr[3])->with('testGroupName', $arr[4])->with('rep_barcode', $arr[5]);
});

//Print parameeter Barcode Route~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::get('/printParameterBarcode/{id}', function($id) {

    $arr = explode("&", $id);

    $lid = $_SESSION["lid"];
    
    
    return View::make('Barcodes.Test_parameeter_wise_Bcode' )->with('sno', $arr[0])->with('date',  $arr[1])->with('tgid', $arr[2])->with('testGroupName', $arr[3])->with('orderNo', $arr[4]);
});

// text code buttons route~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Route::get('/get-testcodes', 'YourController@getTestCodes');
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~