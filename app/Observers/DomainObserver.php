<?php

namespace App\Observers;

use App\Models\Domain;
use App\Models\DomainHistory;

class DomainObserver
{
    public function created(Domain $domain)
    {
        $this->logHistory($domain, 'created');
    }

    public function updated(Domain $domain)
    {
        $this->logHistory($domain, 'updated');
    }

    public function deleted(Domain $domain)
    {
        $this->logHistory($domain, 'deleted');
    }


    protected function logHistory(Domain $domain, $changeType)
    {
        DomainHistory::create([
            'user_id' => $domain->user_id,
            'domain_id' => $domain->domain_id,
            'domain_name' => $domain->domain_name,
            'status' => $domain->status,
            'sold_at' => $domain->sold_at,
            'sold_price' => $domain->sold_price,
            'keywords' => $domain->keywords,
            'description' => $domain->description,
            'date_sold' => $domain->date_sold,
            'sale_note' => $domain->sale_note,
            'sale_mode' => $domain->sale_mode,
            'total_acquisition_amount'=>$domain->total_acquisition_amount,
            'revenue' => $domain->revenue,
            'registered_with' => $domain->registered_with,
            'dns' => $domain->dns,
            'expires_at' => $domain->expires_at,
            'change_type' => $changeType,
            'snapshot_at' => now()
        ]);
    }
}


