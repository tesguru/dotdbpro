<?php

namespace App\Http\Controllers\api\v1\analytics;

use App\Http\Controllers\Controller;
use App\Traits\JsonResponseTrait;
use App\Services\Analytics\AnalyticsService;
use Illuminate\Http\Request;
use Exception;

class AnalyticsController extends Controller{
 protected $analytics;
      public function __construct(AnalyticsService $analytics)
    {
        $this->analytics = $analytics;
    }
 use JsonResponseTrait;
public function getDataLimit( Request $request){
try {

    // $user = $request->user();
    // $user_id = $user->user_id;
    $getDataLimit = $this->analytics->getDataLimit();
    return $this->successDataResponse(data: $getDataLimit);
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
}

public function getRelatedKeywordsj( Request $request){
try {

    // $user = $request->user();
     $keyword = "cook";
     $related = $this->analytics->getRelatedDomains($keyword);
    return $this->successDataResponse(data:  $related);
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
}


public function getRelatedKeywords(Request $request)
{
    try {
        $keyword = $request->input('keyword', 'tech');
        $filters = $request->input('filters', []);

        $related = $this->analytics->getRelatedDomains($keyword, $filters);
        return $this->successDataResponse(data: $related);
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
}








public function getAiMeaning( Request $request){
try {

    $result = $this->analytics->analyzeDomain('Texasdiesel.com');
    return $this->successDataResponse(data:  $result);
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
}

}


