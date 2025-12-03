<?php

namespace App\Services\Domain;

use App\Models\Customer;
use App\Models\MortgageApplication;
use App\Models\MortgagePlan;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Domain;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Iodev\Whois\Factory;
use Carbon\Carbon;
class DomainService
{
    /**
     * @throws Exception
     */


public static function createDomainService(array $datas) {
    $insertedDomains = [];
    $skippedDomains = [];
    $failedDomains = [];
    $invalidDomains = [];

    if (!isset($datas['domains']) || !is_array($datas['domains'])) {
        return [
            'success_count' => 0,
            'failed_count' => 0,
            'inserted_domains' => [],
            'skipped_domains' => [],
            'failed_domains' => [],
            'message' => 'Invalid input format'
        ];
    }

    // Extract domains from the new object structure
    $domainObjects = $datas['domains'];
    $whois = Factory::get()->createWhois();

    DB::beginTransaction();
    try {
        foreach ($domainObjects as $domainObj) {
            $domain = trim($domainObj['domain']); // Extract domain name
            $acquisitionPrice = $domainObj['acquisition_price'] ?? null;
            $acquisitionMethod = $domainObj['acquisition_method'] ?? null;

            if (!preg_match('/^(?!www\.)[a-zA-Z0-9-]+\.[a-zA-Z]{2,}$/', $domain)) {
                $invalidDomains[] = $domain;
                continue;
            }

            $existing = Domain::where('domain_name', $domain)
                             ->where('user_id', $datas["user_id"])
                             ->first();
            if ($existing) {
                $skippedDomains[] = $domain;
                continue;
            }

            // Prepare data with acquisition details
            $domainData = [
                'domain_name' => $domain,
                'user_id' => $datas["user_id"],
                'status' => 'owned',
                'keywords' => self::split_words($domain),
                'acquisition_price' => $acquisitionPrice,
                'acquisition_method' => $acquisitionMethod
            ];

            // Rest of your WHOIS logic stays the same...
            try {
                $info = $whois->loadDomainInfo($domain);
                if ($info) {
                    if ($info->getExpirationDate()) {
                        $domainData['expires_at'] = Carbon::createFromTimestamp($info->getExpirationDate());
                    }
                    if ($info->getRegistrar()) {
                        $domainData['registered_with'] = $info->getRegistrar();
                    }
                    $nameServers = $info->getNameServers();
                    if (!empty($nameServers)) {
                        $name_servers = isset($nameServers[0]) ? $nameServers[0] : '';
                        $name_servers .= isset($nameServers[1]) ? ' ' . $nameServers[1] : '';
                        $domainData['dns'] = trim($name_servers);
                    }
                }
            } catch (\Exception $whoisException) {
                \Log::warning("WHOIS lookup failed for domain: {$domain}", [
                    'error' => $whoisException->getMessage()
                ]);
            }

            $newDomain = Domain::create($domainData);
            $insertedDomains[] = $domain;
        }

        DB::commit();
        DB::commit();
    } catch (\Exception $e) {
        DB::rollback();
        \Log::error("Domain service transaction failed", [
            'error' => $e->getMessage(),
            'user_id' => $datas["user_id"] ?? null
        ]);

        return [
            'success_count' => 0,
            'failed_count' => count($domainObjects),
            'inserted_domains' => [],
            'skipped_domains' => [],
            'failed_domains' => array_column($domainObjects, 'domain'),
            'message' => 'Critical error occurred: ' . $e->getMessage()
        ];
    }

    return [
        'success_count' => count($insertedDomains),
        'failed_count' => count($failedDomains) + count($invalidDomains),
        'inserted_domains' => $insertedDomains,
        'skipped_domains' => $skippedDomains,
        'failed_domains' => array_merge($failedDomains, $invalidDomains),
        'result' => self::generateResultMessage($insertedDomains, $skippedDomains, $failedDomains, $invalidDomains)
    ];
}

static function split_words($domain)
{
        $scriptPath = base_path('split_word.py');
        $command = escapeshellcmd("python3 $scriptPath " . escapeshellarg($domain));
        $output = shell_exec($command);
        return trim($output);
}

static function generateResultMessage($inserted, $skipped, $failed, $invalid)
{
    $messages = [];
    if (count($inserted) > 0) {
        $messages[] = count($inserted) . " domain(s) inserted successfully.";
    }
    if (count($skipped) > 0) {
        $messages[] = count($skipped) . " domain(s) already exist and were skipped.";
    }
    if (count($invalid) > 0) {
        $messages[] = count($invalid) . " invalid domain(s) skipped.";
    }
    if (count($failed) > 0) {
        $messages[] = count($failed) . " domain(s) failed to insert.";
    }
    return implode(' ', $messages) ?: "No valid domains processed.";
}

public static function getAllDomains($userId){
   $getDomains = Domain::where('user_id',$userId)->orderBy('id', 'desc')->get();
   return $getDomains;
}
public static function getSoldDomains($userId){
   $getDomains = Domain::where('user_id',$userId)->orderBy('id', 'desc')->where('status', 'sold')->get();
   return $getDomains;
}
public static function getExpiredDomains($userId) {
    return Domain::where('user_id', $userId)
        ->orderBy('id', 'desc')
                 ->whereDate('expires_at', '<', now())
                 ->get();
}

public static function analyticsForAddedDomains($userId)
{
    $today = now()->startOfDay();
    $weekStart = now()->startOfWeek();

    $addedDomainsToday = Domain::where('user_id', $userId)
        ->where('created_at', '>=', $today)
        ->count();

    $addedDomainsWeek = Domain::where('user_id', $userId)
        ->where('created_at', '>=', $weekStart)
        ->count();

    $addedDomainsTotal = Domain::where('user_id', $userId)->count();

    return [
        'today' => $addedDomainsToday,
        'this_week' => $addedDomainsWeek,
        'total' => $addedDomainsTotal
    ];
}
public static function analyticsForSoldDomains($userId)
{
    $today = now()->startOfDay();
    $weekStart = now()->startOfWeek();

    $addedDomainsToday = Domain::where('user_id', $userId)
        ->where('created_at', '>=', $today)
          ->where('status', '>=', 'sold')
        ->count();

    $addedDomainsWeek = Domain::where('user_id', $userId)
        ->where('created_at', '>=', $weekStart)
           ->where('status', '>=', 'sold')
        ->count();

    $addedDomainsTotal = Domain::where('user_id', $userId)->where('status', '>=', 'sold')->count();

    return [
        'today' => $addedDomainsToday,
        'this_week' => $addedDomainsWeek,
        'total' => $addedDomainsTotal
    ];
}
public static function analyticsForExpiredDomains($userId)
{
    $today = now()->startOfDay();
    $weekStart = now()->startOfWeek();

    $expiredToday = Domain::where('user_id', $userId)
        ->whereDate('expires_at', '=', $today)
        ->count();

    $expiredThisWeek = Domain::where('user_id', $userId)
        ->whereBetween('expires_at', [$weekStart, now()])
        ->count();

    $expiredTotal = Domain::where('user_id', $userId)
        ->where('expires_at', '<', now())
        ->count();

    return [
        'today' => $expiredToday,
        'this_week' => $expiredThisWeek,
        'total' => $expiredTotal,
    ];
}

public static function updateSoldDomainService(array $data){
 $domain_id = $data['domain_id'];

   $updateDomain = Domain::where('domain_id', $domain_id)->update([
        'sale_note' => $data['sale_note'],
        'sold_price' => $data['sold_price'],
        'revenue' => $data['revenue'],
        'lander_sold' => $data['lander_sold'],
        'date_sold' => $data['date_sold'],
        'sale_mode' => $data['sale_mode'],
        'status' => 'sold',
    ]);

       return $updateDomain;

}

   public static function getSoldDomainSearch($user_id, $request)
    {
        $query = Domain::where('status', 'sold')
                       ->where('user_id', $user_id);

        if ($request->has('start_date') && $request->start_date) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('date_sold', '>=', $startDate);
        }

        if ($request->has('end_date') && $request->end_date) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('date_sold', '<=', $endDate);
        }

        if ($request->has('search') && $request->search) {
            $query->where('domain_name', 'LIKE', '%' . $request->search . '%');
        }

        return $query->orderBy('date_sold', 'desc')->get();
    }

    public static function getRenewDomainSearch($user_id, $request)
    {
       $query = Domain::where('renewed_times', '>', 0)
                       ->where('user_id', $user_id);

        if ($request->has('start_date') && $request->start_date) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('date_sold', '>=', $startDate);
        }

        if ($request->has('end_date') && $request->end_date) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('date_sold', '<=', $endDate);
        }

        if ($request->has('search') && $request->search) {
            $query->where('domain_name', 'LIKE', '%' . $request->search . '%');
        }

        return $query->orderBy('date_sold', 'desc')->get();
    }


public static function renewedDomainService(array $data) {
    $domain_id = $data['domain_id'];
    $domain = Domain::where('domain_id', $domain_id)->first();
    if (!$domain) {
        return false;
    }

    $domain->total_acquisition_amount += $data['renewed_price'];
    $domain->renewed_times += 1;
    $domain->save();
    return $domain;
}

public static function updateDomainAsOwnedService(array $data) {
    $domain_id = $data['domain_id'];
    $domain = Domain::where('domain_id', $domain_id)->first();
    if (!$domain) {
        return false;
    }
    $current_acqusition_price = $domain->total_acquisition_amount -  $domain->acquisition_price;

    $domain->total_acquisition_amount = $current_acqusition_price + $data['acquisition_price'];
       $domain->acquisition_price = $data['acquisition_price'];
    $domain->acquisition_method = $data['acquisition_method'];
    $domain->save();
    return $domain;
}


}
