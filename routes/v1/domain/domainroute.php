<?php

use App\Http\Controllers\api\v1\domain\DomainController;
use Illuminate\Support\Facades\Route;


Route::middleware(['api.key', 'access_token'])->controller(DomainController::class)->group(function () {
  Route::post('/create-domain', 'createDomain');
  Route::post('/update-sold-domain', 'updateSoldDomain');
  Route::post('/mark-as-owned', 'updateDomainAsOwned');
  Route::post('/renewed-domain', 'renewedDomain');
  Route::get('/get-all-domains', 'getAllDomain');
  Route::get('/get-sold-domains', 'getSoldDomain');
  Route::post('/delete-domain', 'bulkDeleteDomains');
  Route::get('/get-expired-domains', 'getExpiredDomains');
Route::get('/get-domain-search', 'getSoldDomainSearch');
Route::get('/get-renewed-domain-search', 'getRenewDomainSearch');
  Route::get('/get-expiring-domain-search', 'getExpiringDomainSearch');
  Route::get('/get-sold-domains-analytics', 'analyticsForSoldDomain');
  Route::get('/get-added-domains-analytics', 'analyticsForAddedDomain');
  Route::get('/get-expired-domains-analytics', 'analyticsForExpiredDomain');
   Route::get('/get-userid', 'getUserId');
  Route::get('/get-single-domain/{id}', [DomainController::class, 'getSingleDomain']);


});
