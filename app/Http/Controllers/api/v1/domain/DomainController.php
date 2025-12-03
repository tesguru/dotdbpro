<?php

namespace App\Http\Controllers\api\v1\domain;

use App\Http\Controllers\Controller;
use App\Traits\JsonResponseTrait;
use App\Services\Domain\DomainService;
use App\Http\Requests\Domain\DomainCreationRequest;
use App\Http\Requests\Domain\CreateOwnedRequest;
use App\Http\Requests\Domain\UpdateSoldDomainRequest;
use App\Http\Requests\Domain\UpdateDomainAsOwnedRequest;
use App\Http\Requests\Domain\UpdateRenewedDomainRequest;
use Illuminate\Http\Request;
use robertogallea\LaravelPython\Services\LaravelPython;
use App\Models\Domain;
use Exception;

class  DomainController extends Controller
{
    use JsonResponseTrait;

    public function createDomain(DomainCreationRequest $request){
          try {
         $data = $request->validated();
            $user = $request->user();
            $data['user_id'] = $user->user_id;
        $getDomainResult = DomainService::createDomainService($data);

        return $this->successDataResponse(data: $getDomainResult);
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
    }
public function getAlldomain(Request $request){
   try {
            $user = $request->user();
            $user_id = $user->user_id;
        $getAllDomains = DomainService::getAllDomains($user_id);
        return $this->successDataResponse(data: $getAllDomains);
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }

}

public function getSolddomain(Request $request){
   try {

            $user = $request->user();
            $user_id = $user->user_id;
        $getAllSoldDomain = DomainService::getSoldDomains($user_id);
        return $this->successDataResponse(data: $getAllSoldDomain);
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
}
public function getExpiredDomains(Request $request){
   try {
            $user = $request->user();
            $user_id = $user->user_id;
        $getAllExpiredDomain = DomainService::getExpiredDomains($user_id);
        return $this->successDataResponse(data: $getAllExpiredDomain);
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
}
public function analyticsForAddedDomain(Request $request){
   try {

            $user = $request->user();
            $user_id = $user->user_id;
        $getAllAddedDomainsAnalytics = DomainService::analyticsForAddedDomains($user_id);
        return $this->successDataResponse(data: $getAllAddedDomainsAnalytics);
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
}
public function analyticsForSoldDomain(Request $request){
   try {

            $user = $request->user();
            $user_id = $user->user_id;
        $getAllSoldDomainsAnalytics = DomainService::analyticsForSoldDomains($user_id);
        return $this->successDataResponse(data: $getAllSoldDomainsAnalytics);
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
}



public function analyticsForExpiredDomain(Request $request){
   try {

            $user = $request->user();
            $user_id = $user->user_id;
        $getAllSoldDomainsAnalytics = DomainService::analyticsForExpiredDomains($user_id);
        return $this->successDataResponse(data: $getAllSoldDomainsAnalytics);
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
}

public function getSoldDomainSearch(Request $request){
   try {

            $user = $request->user();
            $user_id = $user->user_id;
        $getAllSoldSearch = DomainService::getSoldDomainSearch($user_id, $request);
        return $this->successDataResponse(data: $getAllSoldSearch);
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
}

public function getRenewDomainSearch(Request $request){
   try {

            $user = $request->user();
            $user_id = $user->user_id;
        $getAllDropSearch = DomainService::getRenewDomainSearch($user_id, $request);
        return $this->successDataResponse(data: $getAllDropSearch);
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
}




   public function getSingleDomain(String $domain_id, Request $request)

    {

        try {
             $user = $request->user();
            $user_id = $user->user_id;
          $getDomainDetails = Domain::where("domain_id", $domain_id)->first();
       return $this->successDataResponse(data: $getDomainDetails);
        } catch (Exception $ex) {
            return $this->errorResponse(message: $ex->getMessage());
        }
    }
   public function bulkDeleteDomains(Request $request)
{
    try {
        $user = $request->user();
        $user_id = $user->user_id;

        $domainIds = $request->input('domain_ids');

        if (empty($domainIds)) {
            return $this->errorResponse(message: "No domains selected");
        }

        Domain::whereIn('domain_id', $domainIds)->delete();

        return $this->successResponse(message: "Selected domains deleted successfully");
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
}





    public function getUserId(Request $request)
{
    try {
        $user = $request->user();
        $user_id = $user->user_id;
        $userdata = [];
        $userdata['user_id'] = $user_id;
        return $this->successDataResponse(data: $userdata);
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
}


    public function updateSoldDomain(UpdateSoldDomainRequest $request){
    try {
          $data = $request->validated();
            $user = $request->user();
            $data['user_id'] = $user->user_id;
        $getDomainResult = DomainService::updateSoldDomainService($data);
        return $this->successDataResponse(data: $getDomainResult);
    } catch (Exception $ex) {
         return $this->errorResponse(message: $ex->getMessage());
    }
    }

      public function updateDomainAsOwned(UpdateDomainAsOwnedRequest $request){
     try {

        $data = $request->validated();
        $user = $request->user();
        $data['user_id'] = $user->user_id;

        $getDomainResult = DomainService::updateDomainAsOwnedService($data);
        return $this->successDataResponse(data: $getDomainResult);
      } catch (Exception $ex) {
         return $this->errorResponse(message: $ex->getMessage());
     }
    }
    public function RenewedDomain(UpdateRenewedDomainRequest $request){
     try {
        $data = $request->validated();
        $user = $request->user();
        $data['user_id'] = $user->user_id;
        $getDomainResult = DomainService::renewedDomainService($data);
        return $this->successDataResponse(data: $getDomainResult);
      } catch (Exception $ex) {
         return $this->errorResponse(message: $ex->getMessage());
     }
    }

}


