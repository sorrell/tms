<?php

namespace App\Integrations;

use Illuminate\Support\Facades\Http;

class FmcsaService
{

    const BASE_URL = 'https://mobile.fmcsa.dot.gov/qc/services/';

    public function getFullReport(string $dotNumber) : array
    {
        $report = [];
        

        $searchResult = $this->searchCarrierDOT($dotNumber);

        if (isset($searchResult['error'])) {
            return $searchResult;
        }

        if (empty($searchResult['content'])) {
            throw new \Exception('Failed to get data from FMCSa API');
        }

        $report['general'] = $searchResult['content'];
        $report['basics'] = $this->getCarrierBasics($dotNumber)['content'] ?? [];
        $report['cargo-carried'] = $this->getCarrierCargoCarried($dotNumber)['content'] ?? [];
        $report['operation-classification'] = $this->getCarrierOperationClassification($dotNumber)['content'] ?? [];
        $report['oos'] = $this->getCarrierOos($dotNumber)['content'] ?? [];
        $report['docket-numbers'] = $this->getCarrierDocketNumbers($dotNumber)['content'] ?? [];
        $report['authority'] = $this->getCarrierAuthority($dotNumber)['content'] ?? [];
        
        return $report;
    }

    public function searchCarrierName($carrierName)
    {
        return $this
            ->getWithKey('carriers/name/' . $carrierName);
    }

    public function searchCarrierDOT($dotNumber)
    {
        return $this
            ->getWithKey('carriers/' . $dotNumber);
    }

    public function searchCarrierMC($mcNumber)
    {
        return $this
            ->getWithKey('carriers/docket-number/' . $mcNumber);
    }

    public function getCarrierMC($dotNumber)
    {
        return $this
            ->getWithKey('carriers/' . $dotNumber . '/mc-numbers');
    }

    public function getCarrierBasics($dotNumber)
    {
        return $this
            ->getWithKey('carriers/' . $dotNumber . '/basics');
    }

    public function getCarrierCargoCarried($dotNumber)
    {
        return $this
            ->getWithKey('carriers/' . $dotNumber . '/cargo-carried');
    }

    public function getCarrierOperationClassification($dotNumber)
    {
        return $this
            ->getWithKey('carriers/' . $dotNumber . '/operation-classification');
    }

    public function getCarrierOos($dotNumber)
    {
        return $this
            ->getWithKey('carriers/' . $dotNumber . '/oos');
    }

    public function getCarrierDocketNumbers($dotNumber)
    {
        return $this
            ->getWithKey('carriers/' . $dotNumber . '/docket-numbers');
    }

    public function getCarrierAuthority($dotNumber)
    {
        return $this
            ->getWithKey('carriers/' . $dotNumber . '/authority');
    }

    private function getWithKey($path)
    {
        $result = Http::withQueryParameters([
            'webKey' => config('fmcsa.api_key'),
        ])->get(self::BASE_URL . $path);   
        
        $resultJson = $result->json();

        if ($result->status() === 404 || empty($resultJson['content'])) {
            return [
                'error' => 'Carrier not found',
            ];
        }

        if ($result->successful()) {
            return $resultJson;
        }

        throw new \Exception('Failed to get data from FMCSa API - Unexpected server response');
    }
}