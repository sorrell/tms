<?php

namespace App\Actions\Documents\Generators;

use App\Actions\Documents\CreateDocument;
use App\Enums\Documents\Documentable;
use App\Enums\Documents\DocumentFolder;
use App\Enums\StopType;
use App\Models\Shipments\Shipment;
use Exception;
use Illuminate\Http\File;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Lorisleiva\Actions\Concerns\AsAction;

class GenerateRateConfirmation
{
    use AsAction;

    public function handle(
        Shipment $shipment
    )
    {
        // Prepare data for the view
        $data = $this->prepareViewData($shipment);
        
        // Generate HTML from the view
        $html = View::make('documents.carrier-rate-confirmation', $data)->render();
        
        // Ensure directory exists
        $directory = 'rate-confirmations';
        if (!Storage::disk('local')->exists($directory)) {
            Storage::disk('local')->makeDirectory($directory);
        }
        
        // Generate PDF from HTML
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        
        // Save PDF file to local storage
        $fileName = $directory . '/carrier-rc-' . $shipment->id . '-' . time() . '.pdf';
        Storage::disk('local')->put($fileName, $pdf->output());

        // Create a File object from the saved PDF
        $filePath = Storage::disk('local')->path($fileName);
        $file = new File($filePath);

        $document = CreateDocument::run(
            Documentable::SHIPMENT->value,
            $shipment->id,
            "RateConfirmation.pdf",
            $file,
            DocumentFolder::RATECONS->value
        );

        // Delete the temp doc
        Storage::disk('local')->delete($fileName);

        return $document;
    }
    
    private function prepareViewData(Shipment $shipment): array
    {
        // Get origin and destination stops
        $originStop = $shipment->stops->where('stop_number', 1)->first();
        $destinationStop = $shipment->stops->sortByDesc('stop_number')->first();
        
        // Extract broker info (from organization config)
        $broker = [
            'broker_company' => config('company.name'),
            'broker_address' => config('company.address'),
            'broker_city' => config('company.city'),
            'broker_state' => config('company.state'),
            'broker_zip' => config('company.zip'),
            'broker_mc' => config('company.mc_number'),
            'broker_phone' => config('company.phone'),
            'broker_email' => config('company.email'),
            'company_logo' => config('company.logo_url'),
        ];
        
        // Extract rate confirmation details
        $rateConfirmation = [
            'rate_con_number' => "RC-{$shipment->id}",
            'date' => now()->format('m/d/Y'),
            'load_number' => $shipment->shipment_number,
            'reference_number' => $originStop?->reference_numbers ?? '',
        ];
        
        // Extract carrier information
        $carrier = [
            'carrier_name' => $shipment->carrier->name ?? '',
            'carrier_contact' => $shipment->carrier->contact_name ?? '',
            'carrier_address' => $shipment->carrier->address ?? '',
            'carrier_phone' => $shipment->carrier->phone ?? '',
            'carrier_mc' => $shipment->carrier->mc_number ?? '',
            'carrier_dot' => $shipment->carrier->dot_number ?? '',
            'carrier_scac' => $shipment->carrier->scac ?? '',
            'carrier_email' => $shipment->carrier->email ?? '',
        ];
        
        // Extract pickup information from origin stop
        $pickup = [];
        if ($originStop) {
            $facility = $originStop->facility;
            $location = $facility->location;
            $pickupDate = $originStop->appointment_at ? \Carbon\Carbon::parse($originStop->appointment_at) : null;
            $pickup = [
                'pickup_company' => $facility->name ?? '',
                'pickup_address' => $location->address_line_1 . ($location->address_line_2 ? ', ' . $location->address_line_2 : '') ?? '',
                'pickup_city' => $location->address_city ?? '',
                'pickup_state' => $location->state_shorthand ?? '',
                'pickup_zip' => $location->address_zipcode ?? '',
                'pickup_date' => $pickupDate?->format('m/d/Y') ?? '',
                'pickup_time' => $pickupDate?->format('h:i A') ?? '',
                'pickup_contact' => $facility->contacts->first()->name ?? '',
                'pickup_phone' => $facility->contacts->first()->phone ?? '',
                'special_instructions' => $originStop->special_instructions ?? '',
            ];
        }
        
        // Extract delivery information from destination stop
        $delivery = [];
        if ($destinationStop) {
            $facility = $destinationStop->facility;
            $location = $facility->location;
            $deliveryDate = $destinationStop->appointment_at ? \Carbon\Carbon::parse($destinationStop->appointment_at) : null;
            $delivery = [
                'delivery_company' => $facility->name ?? '',
                'delivery_address' => $location->address_line_1 . ($location->address_line_2 ? ', ' . $location->address_line_2 : '') ?? '',
                'delivery_city' => $location->address_city ?? '',
                'delivery_state' => $location->state_shorthand ?? '',
                'delivery_zip' => $location->address_zipcode ?? '',
                'delivery_date' => $deliveryDate?->format('m/d/Y') ?? '',
                'delivery_time' => $deliveryDate?->format('h:i A') ?? '',
                'delivery_contact' => $facility->contacts->first()->name ?? '',
                'delivery_phone' => $facility->contacts->first()->phone ?? '',
            ];
        }
        
        // Extract cargo details
        $cargo = [
            'commodity' => '', // Implement based on available data
            'weight' => $shipment->weight ?? '',
            'pieces' => '', // Implement based on available data
            'pallets' => '', // Implement based on available data
            'dimensions' => '', // Implement based on available data
            'hazmat' => 'No', // Implement based on available data
            'temperature' => $shipment->trailer_temperature ?? 'N/A',
            'trailer_type' => $shipment->trailer_type?->name ?? '',
            'equipment_requirements' => '', // Implement based on available data
        ];
        
        // Collect rates and payment info
        $rate = [
            'linehaul_rate' => number_format($this->getLineHaulRate($shipment) ?? 0, 2),
            'fuel_surcharge' => number_format($this->getFuelSurcharge($shipment) ?? 0, 2),
            'detention' => '0.00', // Implement based on available data
            'total_rate' => number_format($this->getTotalRate($shipment) ?? 0, 2),
            'payment_terms' => 'Net 30 days from receipt of complete and accurate invoice and all required documentation',
            'quick_pay' => '3% discount for payment within 7 days of receipt of required documentation',
            'invoice_email' => config('company.accounting_email'),
        ];
        
        // Additional charges
        $additionalCharges = $this->getAdditionalCharges($shipment);
        
        return array_merge(
            $broker, 
            $rateConfirmation, 
            $carrier, 
            $pickup, 
            $delivery, 
            $cargo, 
            $rate, 
            ['additional_charges' => $additionalCharges]
        );
    }
    
    private function getLineHaulRate(Shipment $shipment): float
    {
        // Get linehaul rate from payables if available
        $linehaul = $shipment->payables->where('type', 'linehaul')->first();
        return $linehaul ? $linehaul->amount : 0;
    }
    
    private function getFuelSurcharge(Shipment $shipment): float
    {
        // Get fuel surcharge from payables if available
        $fuel = $shipment->payables->where('type', 'fuel_surcharge')->first();
        return $fuel ? $fuel->amount : 0;
    }
    
    private function getTotalRate(Shipment $shipment): float
    {
        // Sum all payables
        return $shipment->payables->sum('amount');
    }
    
    private function getAdditionalCharges(Shipment $shipment): array
    {
        $charges = [];
        // Get all payables that are not linehaul or fuel surcharge
        $additionalPayables = $shipment->payables->whereNotIn('type', ['linehaul', 'fuel_surcharge']);
        
        foreach ($additionalPayables as $payable) {
            $charges[] = [
                'description' => ucfirst(str_replace('_', ' ', $payable->type)),
                'amount' => number_format($payable->amount, 2)
            ];
        }
        
        return $charges;
    }
}