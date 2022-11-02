<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiAuthMiddleware;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', 'PruebasController@testORM');

Route::resource('/api/participant', 'ParticipantController');
Route::get('/api/candidates', 'ParticipantController@candidates');
Route::post('/api/participant/updateFotos', 'ParticipantController@updateFotos');
Route::get('/api/rptSinFotos', 'rptInformesController@rptSinFotos');



Route::resource('/api/city', 'CityController');
Route::resource('/api/report', 'ReportController');



Route::resource('/api/circuit', 'CircuitController');
Route::get('/api/circuitofc/{id}', 'CircuitController@getCircuitsOfCity');

Route::resource('/api/location', 'LocationController');
Route::get('/api/locationofc/{id}', 'LocationController@getLocationsOfCity');

Route::resource('/api/schedule', 'ScheduleController');
Route::get('/api/scheduleofc/{id}', 'ScheduleController@getSchedulesOfCity');

Route::resource('/api/shift', 'ShiftsController');
Route::get('/api/shiftofc/{id}', 'ShiftsController@getShiftsOfCity');
Route::get('/api/shiftofu/{id}', 'ShiftsController@getShiftsforUsr');



Route::resource('/api/component', 'ComponentController');

Route::post('/api/user/login', 'UserController@login');
Route::get('/api/user/accesacomp/{id_rol}', 'UserController@accesoaComponentes');
Route::resource('/api/user', 'UserController');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);

Route::resource('/api/rol', 'RolesController');
Route::resource('/api/access', 'AccessController');
Route::get('/api/access/accesacomp/{id_rol}', 'AccessController@accesoaComponentes');

Route::resource('/api/assigned', 'AssignedToController');
Route::get('/api/shift_assigned/{id_turno}', 'AssignedToController@shift_index');


Route::resource('/api/event', 'EventController');
Route::resource('/api/training', 'TrainingController');
Route::resource('/api/experience', 'ExperienceController');
Route::resource('/api/absence', 'AbsenceController');
Route::get('/api/absence/{id_turno}/{sem}', 'AbsenceController@id_informe');
Route::get('/api/absence_pinf/{id_informe}', 'AbsenceController@abs_id_informe');


Route::get('/api/informe1', 'rptInformesController@index');
Route::post('/api/informes', 'rptInformesController@rptInformes');
Route::post('/api/informe', 'rptInformesController@rptInforme');
Route::post('/api/informexubi', 'rptInformesController@rptReportLocation');

Route::get('/api/reporte1', 'rptInformesController@rptReporte1');
Route::get('/api/reporte2', 'rptInformesController@rptReporte2');
Route::get('/api/reporte4', 'rptInformesController@rptReporte4');
Route::get('/api/reporte7', 'rptInformesController@rptReporte7');
Route::get('/api/reporte9', 'rptInformesController@rptReporte9');
Route::get('/api/reporte10', 'rptInformesController@rptReporte10');
Route::get('/api/reporte12', 'rptInformesController@rptReporte12');
Route::get('/api/reporte13', 'rptInformesController@rptReporte13');

Route::resource('/api/reminder', 'ReminderController');
Route::resource('/api/addressee', 'AddresseeController');

Route::get('/api/presigned', 'CredentialsController@presigned');
Route::get('/api/awsprueba', 'CredentialsController@awsprueba');
Route::get('/api/bucketcontent', 'CredentialsController@bucketcontent');

