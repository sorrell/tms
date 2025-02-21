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
            logger()->error('Failed to get data from FMCSa API', $searchResult);
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
        $report['full-report'] = 'true';

        return $report;
    }

    public function searchCarrierName($carrierName, int $limit = 10)
    {
        $report = [];

        $searchResult = $this->getWithKey('carriers/name/' . $carrierName, ['size' => $limit]);

        if (isset($searchResult['error'])) {
            return $searchResult;
        }

        foreach($searchResult['content'] as $carrier) {
            $report[] = [
                'general' => $carrier,
                'full-report' => 'false',
            ];
        }

        return $report;
    }

    protected function searchCarrierDOT($dotNumber)
    {
        return $this
            ->getWithKey('carriers/' . $dotNumber);
    }

    protected function searchCarrierMC($mcNumber)
    {
        throw new \Exception('Not implemented');
        // return $this
        //     ->getWithKey('carriers/docket-number/' . $mcNumber);
    }

    protected function getCarrierMC($dotNumber)
    {
        return $this
            ->getWithKey('carriers/' . $dotNumber . '/mc-numbers');
    }

    protected function getCarrierBasics($dotNumber)
    {
        return $this
            ->getWithKey('carriers/' . $dotNumber . '/basics');
    }

    protected function getCarrierCargoCarried($dotNumber)
    {
        return $this
            ->getWithKey('carriers/' . $dotNumber . '/cargo-carried');
    }

    protected function getCarrierOperationClassification($dotNumber)
    {
        return $this
            ->getWithKey('carriers/' . $dotNumber . '/operation-classification');
    }

    protected function getCarrierOos($dotNumber)
    {
        return $this
            ->getWithKey('carriers/' . $dotNumber . '/oos');
    }

    protected function getCarrierDocketNumbers($dotNumber)
    {
        return $this
            ->getWithKey('carriers/' . $dotNumber . '/docket-numbers');
    }

    protected function getCarrierAuthority($dotNumber)
    {
        return $this
            ->getWithKey('carriers/' . $dotNumber . '/authority');
    }

    protected function getWithKey(string $path, array $params = [])
    {
        return cache()->remember('fmcsa_api_' . $path . '_' . md5(serialize($params)), now()->addHours(12), function () use ($path, $params) {
            $result = Http::withQueryParameters([
                'webKey' => config('fmcsa.api_key'),
                ...$params
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
        });
    }
}