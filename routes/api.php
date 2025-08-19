<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Admin
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\Areas\AreasController;
use App\Http\Controllers\Api\Admin\Cash_Tickets\Cash_TicketsController;
use App\Http\Controllers\Api\Admin\Collectors\CollectorController;
use App\Http\Controllers\Api\Admin\Occupants\OccupantsController;
use App\Http\Controllers\Api\Admin\Reports\ReportsController;
use App\Http\Controllers\Api\Admin\Sections\SectionController;
use App\Http\Controllers\Api\Collector\CollectorLoginController;
use App\Http\Controllers\Api\Collector\Dispense_Cash_Tickets_Controller;
use App\Http\Controllers\Api\Collector\Get_Cash_Tickets_Controller;
use App\Http\Controllers\Api\Collector\Occupant_Monthly_Payment_Controller;
use App\Http\Controllers\Api\Settings\CurrentDateCheckerController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/





//Admin Routes
Route::controller(AdminController::class)->group(function () {
    Route::post('admin/login', 'login');
    Route::post('admin/register', 'register');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('admin/area', AreasController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('admin/section', SectionController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('admin/collector', CollectorController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('admin/collector', CollectorController::class);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::resource('admin/occupant', OccupantsController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('admin/cash_ticket', Cash_TicketsController::class);
});



Route::middleware('auth:sanctum')->controller(ReportsController::class)->group(function () {
    Route::get('admin/GetAreaAndSection', 'GetAreaAndSection');
    Route::get('admin/OverAllDispenseCashTickets', 'OverAllDispenseCashTickets');
    Route::get('admin/OverAllDispenseCashTicketsPerName', 'OverAllDispenseCashTicketsPerName');
    Route::get('admin/OverAllDispenseCashTicketsPerCollector', 'OverAllDispenseCashTicketsPerCollector');
    Route::get('admin/OverAllMonthlyPayment', 'OverAllMonthlyPayment');
    Route::get('admin/OverAllMonthlyPaymentPerArea', 'OverAllMonthlyPaymentPerArea');
    Route::get('admin/OverAllMonthlyPaymentPerCollector', 'OverAllMonthlyPaymentPerCollector');


});


//END ADMIN ROUTES


//Collector Routes
Route::controller(CollectorLoginController::class)->group(function () {
    Route::post('collector/login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('collector/cash_ticket', Get_Cash_Tickets_Controller::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('collector/dispense_cash_ticket', Dispense_Cash_Tickets_Controller::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('collector/occupant_monthly_payment', Occupant_Monthly_Payment_Controller::class);
});

//END COLLECTOR ROUTES



//Settings Route
Route::controller(CurrentDateCheckerController::class)->group(function () {
    Route::get('settings/GetCurrentDate', 'GetCurrentDate');
});
