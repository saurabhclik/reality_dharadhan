<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SoftwareFeatureController;
use App\Http\Controllers\Api\SupportTicketController;
use App\Http\Controllers\Api\CrmUserController;
use App\Http\Controllers\Api\LeadIntegrationController;
use App\Http\Controllers\Api\UniversalLinkController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) 
{
    return $request->user();
});

Route::prefix('software')->group(function () 
{
    Route::get('/info', [SoftwareFeatureController::class, 'getSoftwareInfo']);
    Route::put('/update-type', [SoftwareFeatureController::class, 'updateSoftwareType']);
    Route::get('/', [SoftwareFeatureController::class, 'getSoftwareRequests']);
    Route::get('/{id}', [SoftwareFeatureController::class, 'getSoftwareRequest']);
    Route::post('/', [SoftwareFeatureController::class, 'createSoftwareRequest']);
    Route::put('/{id}', [SoftwareFeatureController::class, 'updateSoftwareRequest']);
    Route::delete('/{id}', [SoftwareFeatureController::class, 'deleteSoftwareRequest']);
});

Route::prefix('features')->group(function () 
{
    Route::get('/', [SoftwareFeatureController::class, 'getFeatures']);         
    Route::get('/{id}', [SoftwareFeatureController::class, 'getFeature']);        
    Route::post('/', [SoftwareFeatureController::class, 'createFeature']);      
    Route::put('/{id}', [SoftwareFeatureController::class, 'updateFeature']);    
    Route::delete('/{id}', [SoftwareFeatureController::class, 'deleteFeature']); 
});

Route::prefix('trials')->group(function () 
{
    Route::get('/', [SoftwareFeatureController::class, 'getTrials']);
    Route::get('/{id}', [SoftwareFeatureController::class, 'getTrial']);
    Route::post('/', [SoftwareFeatureController::class, 'createTrial']);
    Route::put('/{id}', [SoftwareFeatureController::class, 'updateTrial']);
    Route::delete('/{id}', [SoftwareFeatureController::class, 'deleteTrial']);
});

Route::prefix('faqs')->group(function () 
{
    Route::get('/', [SoftwareFeatureController::class, 'getFaqs']);
    Route::get('/{id}', [SoftwareFeatureController::class, 'getFaq']);
    Route::post('/', [SoftwareFeatureController::class, 'createFaq']);
    Route::put('/{id}', [SoftwareFeatureController::class, 'updateFaq']);
    Route::delete('/{id}', [SoftwareFeatureController::class, 'deleteFaq']);
});

Route::prefix('advertisements')->group(function () 
{
    Route::get('/', [SoftwareFeatureController::class, 'getAdvertisements']);       
    Route::get('/{id}', [SoftwareFeatureController::class, 'getAdvertisement']);    
    Route::post('/', [SoftwareFeatureController::class, 'createAdvertisement']);    
    Route::put('/{id}', [SoftwareFeatureController::class, 'updateAdvertisement']);   
    Route::delete('/{id}', [SoftwareFeatureController::class, 'deleteAdvertisement']);
});

Route::prefix('support')->group(function () 
{
    Route::get('/tickets', [SoftwareFeatureController::class, 'getTickets']);
    Route::get('/tickets/{id}', [SoftwareFeatureController::class, 'getTicket']);
    Route::put('/tickets/{id}', [SoftwareFeatureController::class, 'updateTicket']);
    Route::put('/tickets/{id}/reopen', [SoftwareFeatureController::class, 'reopenTicket']);
    Route::get('/tickets/{id}/messages', [SoftwareFeatureController::class, 'getMessages']);
    Route::put('/messages/{id}/read', [SoftwareFeatureController::class, 'markMessageRead']);
    Route::get('/tickets/export', [SoftwareFeatureController::class, 'exportTickets']);
});
Route::prefix('apk')->group(function () 
{
    Route::get('/{id}/info', [SoftwareFeatureController::class, 'getApkInfo']);
    Route::post('/{id}/upload', [SoftwareFeatureController::class, 'uploadApk']);
    Route::delete('/{id}/delete', [SoftwareFeatureController::class, 'deleteApk']);
    Route::get('/{id}/download', [SoftwareFeatureController::class, 'downloadApk']);
});
Route::post('/crm/register', [CrmUserController::class, 'register'])->middleware('throttle:10,1');
Route::prefix('realestate')->group(function () 
{
    Route::post('/submit-lead', [LeadIntegrationController::class, 'insertLead']);
    Route::get('/categories', [LeadIntegrationController::class, 'getCategories']);
    Route::get('/categories/{type?}', [LeadIntegrationController::class, 'getCategories']);
    Route::get('/subcategories/{catgId}', [LeadIntegrationController::class, 'getSubCategories']);
    Route::get('/states', [LeadIntegrationController::class, 'getStates']);
    Route::get('/get-projects', [LeadIntegrationController::class, 'getProjectName']);
    Route::get('/districts/{state}', [LeadIntegrationController::class, 'getDistricts']);
    Route::get('/fetch-lead',  [LeadIntegrationController::class, 'getLead']);
    Route::get('/get-location', [LeadIntegrationController::class, 'getLocation']);
});
Route::get('/crm/universal-link/{id}', [UniversalLinkController::class, 'getUniversalLink']);