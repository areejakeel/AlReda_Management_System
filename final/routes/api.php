<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClinicsController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\SecretaryController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\AdvertController;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\CenterController;
use App\Http\Middleware\CheckRole;




    Route::post('Userregister', [AuthController::class, 'Userregister']);

    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('Adminregister', [AuthController::class, 'Adminregister']);
    Route::post('patientregister', [AuthController::class, 'patientregister']);

     //--------------------------------admin------------------------------
    Route::group(['middleware' => ['role:1']], function()  {

        //-----------------Center-------------
        Route::post('storeProfile', [CenterController::class, 'storeProfile']);
        Route::post('/center/updateProfile/{id}', [CenterController::class, 'updateProfile']);
        Route::delete('/center/destroyProfile/{id}', [CenterController::class, 'destroyProfile']);
        //-----------------Clinic-------------
        Route::post('store', [ClinicsController::class, 'storeClinic']);
        Route::post('/clinic/updateClinic/{id}',[ClinicsController::class,'updateClinic']);
        Route::delete('/clinic/destroyClinic/{id}', [ClinicsController::class, 'destroyClinic']);
        //-----------------Doctors-------------
        Route::post('Doctorstore', [DoctorController::class, 'Doctorstore']);
        Route::delete('/Doctor/Doctordelete/{id}', [DoctorController::class, 'Doctordelete']);
        Route::get('showDoctorAppointments/{id}', [DoctorController::class, 'showDoctorAppointments']);

        //-----------------Secretarys-------------
        Route::post('addSecretary', [SecretaryController::class, 'addSecretary']);
        Route::delete('/Secretary/Secretarydelete/{id}', [SecretaryController::class, 'Secretarydelete']);
        Route::get('ShowSecretary', [SecretaryController::class, 'ShowSecretary']);
        Route::get('/secretar/search/{first_name}',[SecretaryController::class,'search']);

        //-----------------Appointements-------------


        Route::post('Appointment_ClinicStore', [AppointmentsController::class, 'Appointment_ClinicStore']);
        Route::get('getAppointments', [AppointmentsController::class, 'getAppointments']);

        //-----------------Adverts-------------

        Route::post ('update_advert_status/{id}',[AdvertController::class,'update_advert_status']);
        Route::get ('showWaitingAdverts2',[AdvertController::class,'showWaitingAdverts2']);
        Route::get ('show_advert_detail2/{id}',[AdvertController::class,'show_advert_detail2']);

         Route::Post('storeRecordDoctor', [MedicalRecordController::class, 'storeRecordDoctor']);

         Route::get ('getMonthlyPatientStatistics',[StatisticsController::class,'getMonthlyPatientStatistics']);
         Route::get ('getMonthlyVisitStatistics',[StatisticsController::class,'getMonthlyVisitStatistics']);
         Route::get ('getAgeGroupStatistics',[StatisticsController::class,'getAgeGroupStatistics']);
         Route::get ('getVisitCount',[StatisticsController::class,'getVisitCount']);
         Route::get ('getMostFrequentAge',[StatisticsController::class,'getMostFrequentAge']);
         Route::get ('getPatientCount',[StatisticsController::class,'getPatientCount']);

    });

     //--------------------------------Doctors------------------------------

Route::group(['middleware' => ['role:2']], function()  {
    Route::get('/profile/{id}', [DoctorController::class, 'showProfile']);
           //-----------------Records and Files-------------

    Route::get('showDoctorRecords', [DoctorController::class, 'showDoctorRecords']);
    Route::Post('/AddDescription/{id}', [MedicalRecordController::class, 'AddDescription']);
    Route::post ('/storeXrayAndAnalysis/{id}',[MedicalRecordController::class,'storeXrayAndAnalysis']);

    Route::Post('/AddReferral', [MedicalRecordController::class, 'AddReferral']);
    Route::get('/GetReferralsForDoctor', [MedicalRecordController::class, 'GetReferralsForDoctor']);
        //-----------------Articles-------------
        Route::post('store_Article/{id}',[ArticlesController::class,'store_Article']);
    Route::get ('show_articles/{id}',[ArticlesController::class,'show']);
    Route::get('getDoctorByClinic/{clinic_id}', [DoctorController::class, 'getDoctorByClinic']);
    Route::post('AppointmentStore', [AppointmentsController::class, 'AppointmentStore']);
    Route::get('doctorStatistics',[StatisticsController::class,'doctorStatistics']);

});
     //--------------------------------Secretary------------------------------
     Route::group(['middleware' => ['role:3']], function(){
              //-----------------Records and Files-------------
    Route::post ('/storeRecord',[MedicalRecordController::class,'storeRecord']);
    Route::post ('/updateRecord/{id}',[MedicalRecordController::class,'updateRecord']);
     Route::delete('/deleteRecord',[MedicalRecordController::class,'deleteRecord']);
    Route::get ('showFiles2/{id1}',[MedicalRecordController::class,'showFiles2']);

    Route::post('storeVisit',[SecretaryController::class,'storeVisit']);
        //-----------------Adverts-------------
        Route::post ('/store_advert',[AdvertController::class,'store_advert']);
    Route::get ('showWaitingAdverts',[AdvertController::class,'showWaitingAdverts']);
    Route::get ('show_advert_detail/{id}',[AdvertController::class,'show_advert_detail']);
    Route::get('/secretary/search_clinic/{name}',[ClinicsController::class,'search_clinic']);
   ///////////////////////////////////////////
   Route::get('showAllBookedAppointments',[AppointmentsController::class,'showAllBookedAppointments']);

   Route::Post('bookAppointmentSecretary/{id}',[AppointmentsController::class,'bookAppointmentSecretary']);
   Route::get('getAllVisits',[SecretaryController::class,'getAllVisits']);
});

     //-------------------------------- for All User------------------------------

Route::get('ShowProfile', [CenterController::class, 'ShowProfileCentre']);
Route::get('showAllClinics',[ClinicsController::class,'showAllClinics']);
Route::get('allRecords',[CenterController::class,'allRecords']);
Route::get('showAllDoctors',[DoctorController::class,'showAllDoctors']);
Route::get('showdoctor/{id}',[DoctorController::class,'showdoctor']);
Route::get('/doctor/search/{first_name}',[DoctorController::class,'search']);
Route::get ('showAd',[AdvertController::class,'showAd']);
Route::get('getDoctorByClinic/{clinic_id}', [DoctorController::class, 'getDoctorByClinic']);
Route::get('showDoctorAppointmentsCenter/{id}',[AppointmentsController::class,'showDoctorAppointmentsCenter']);
Route::Post('showDoctorAppointmentsByDay2/{doctorId}',[AppointmentsController::class,'showDoctorAppointmentsByDay2']);
Route::get ('showFiles/{id1}',[MedicalRecordController::class,'showFiles']);
///////////////////// patient

Route::group(['middleware' => ['role:4']], function()  {
    Route::get('showDoctorAppointments/{id}', [AppointmentsController::class, 'showDoctorAppointments']);
    Route::get('clinicall', [ClinicsController::class, 'clinicall']);
    Route::get('showdoctors', [DoctorController::class, 'showdoctors']);
Route::Post('showDoctorAppointmentsByDay/{id}', [AppointmentsController::class, 'showDoctorAppointmentsByDay']);
Route::Post('showDoctorAppointmenthome/{id}', [AppointmentsController::class, 'showDoctorAppointmenthome']);
// Route::Post('bookAppointment/{id}', [AppointmentsController::class, 'bookAppointment']);
    Route::Post('bookAppointment/{id}', [AppointmentsController::class, 'bookAppointment']);
    Route::Post('bookAppointmenthome/{id}', [AppointmentsController::class, 'bookAppointmenthome']);
    Route::get('getPatientRecords', [MedicalRecordController::class, 'getPatientRecords']);
    Route::get('getUserAppointments', [AppointmentsController::class, 'getUserAppointments']);
    Route::post('cancelAppointment/{id}', [AppointmentsController::class, 'cancelAppointment']);
    Route::get('getDeletedAppointments', [AppointmentsController::class, 'getDeletedAppointments']);
    Route::get('getUserAppointmentsAttended', [AppointmentsController::class, 'getUserAppointmentsAttended']);
    Route::get('getApprovedAdverts', [AdvertController::class, 'getApprovedAdverts']);
    Route::get ('show_articles2/{id}',[ArticlesController::class,'show2']);
    Route::get ('getAllArticle',[ArticlesController::class,'getAllArticle']);
    Route::delete('deleteAccount',[AuthController::class,'deleteAccount']);
    // Route::get('getDoctorByClinic/{clinic_id}', [DoctorController::class, 'getDoctorByClinic']);

});

####################################### Wallets And Payments APIs ###########################################
    Route::post('wallet-charging',[WalletController::class,'charge'])->middleware('role:3');
    Route::post('payment/{id}',[PaymentController::class,'payment'])->middleware('role:4');
    Route::post('donation',[PaymentController::class,'donation']);
    Route::get('donations',[PaymentController::class,'donations'])->middleware('role:1');
    Route::get('payments',[PaymentController::class,'payments'])->middleware('role:1');
#############################################################################################################
######################################### Notifications APIs ################################################
    Route::get('notifications', [NotificationController::class, 'notifications'])->middleware('role:1,2,3,4');
    Route::post('notification-read', [NotificationController::class, 'notification_read']);
#############################################################################################################
