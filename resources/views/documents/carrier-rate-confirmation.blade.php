<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrier Rate Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #555;
        }
        .logo-placeholder {
            height: 80px;
            margin-bottom: 10px;
            text-align: left;
        }
        .doc-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .doc-subtitle {
            font-size: 14px;
            margin-bottom: 10px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            background-color: #f0f0f0;
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
        }
        .columns {
            display: flex;
            justify-content: space-between;
        }
        .column {
            width: 48%;
        }
        .signature-area {
            border-top: 1px solid #ddd;
            padding-top: 15px;
            margin-top: 30px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 250px;
            margin-top: 50px;
            display: inline-block;
        }
        .signature-field {
            margin-bottom: 30px;
        }
        .small-text {
            font-size: 10px;
        }
        .important {
            font-weight: bold;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-placeholder">
            <img src="{{ $company_logo ?? '' }}" alt="Company Logo" height="80">
        </div>
        <div class="doc-title">CARRIER RATE CONFIRMATION</div>
        <div class="doc-subtitle">AGREEMENT BETWEEN BROKER AND CARRIER</div>
    </div>

    <div class="columns">
        <div class="column">
            <div class="box">
                <strong>Broker Information:</strong><br>
                <strong>{{ $broker_company ?? 'COMPANY NAME' }}</strong><br>
                {{ $broker_address ?? 'Address Line 1' }}<br>
                {{ $broker_city ?? 'City' }}, {{ $broker_state ?? 'State' }} {{ $broker_zip ?? 'ZIP' }}<br>
                MC#: {{ $broker_mc ?? 'MC#' }}<br>
                Phone: {{ $broker_phone ?? 'Phone' }}<br>
                Email: {{ $broker_email ?? 'Email' }}
            </div>
        </div>
        <div class="column">
            <div class="box">
                <strong>Rate Confirmation Details:</strong><br>
                Rate Con #: <strong>{{ $rate_con_number ?? 'RC-00000' }}</strong><br>
                Date: {{ $date ?? date('m/d/Y') }}<br>
                Load #: {{ $load_number ?? 'LOAD-00000' }}<br>
                Ref #: {{ $reference_number ?? '' }}
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">CARRIER INFORMATION</div>
        <table>
            <tr>
                <td width="50%"><strong>Carrier Name:</strong> {{ $carrier_name ?? '' }}</td>
                <td width="50%"><strong>Contact Name:</strong> {{ $carrier_contact ?? '' }}</td>
            </tr>
            <tr>
                <td><strong>Address:</strong> {{ $carrier_address ?? '' }}</td>
                <td><strong>Phone:</strong> {{ $carrier_phone ?? '' }}</td>
            </tr>
            <tr>
                <td><strong>MC#:</strong> {{ $carrier_mc ?? '' }}</td>
                <td><strong>DOT#:</strong> {{ $carrier_dot ?? '' }}</td>
            </tr>
            <tr>
                <td><strong>SCAC:</strong> {{ $carrier_scac ?? '' }}</td>
                <td><strong>Email:</strong> {{ $carrier_email ?? '' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">SHIPMENT DETAILS</div>
        <table>
            <tr>
                <th width="50%">PICKUP</th>
                <th width="50%">DELIVERY</th>
            </tr>
            <tr>
                <td>
                    <strong>{{ $pickup_company ?? '' }}</strong><br>
                    {{ $pickup_address ?? '' }}<br>
                    {{ $pickup_city ?? '' }}, {{ $pickup_state ?? '' }} {{ $pickup_zip ?? '' }}<br>
                    Date: {{ $pickup_date ?? '' }}<br>
                    Time: {{ $pickup_time ?? '' }}
                </td>
                <td>
                    <strong>{{ $delivery_company ?? '' }}</strong><br>
                    {{ $delivery_address ?? '' }}<br>
                    {{ $delivery_city ?? '' }}, {{ $delivery_state ?? '' }} {{ $delivery_zip ?? '' }}<br>
                    Date: {{ $delivery_date ?? '' }}<br>
                    Time: {{ $delivery_time ?? '' }}
                </td>
            </tr>
            <tr>
                <td><strong>Contact:</strong> {{ $pickup_contact ?? '' }}</td>
                <td><strong>Contact:</strong> {{ $delivery_contact ?? '' }}</td>
            </tr>
            <tr>
                <td><strong>Phone:</strong> {{ $pickup_phone ?? '' }}</td>
                <td><strong>Phone:</strong> {{ $delivery_phone ?? '' }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Special Instructions:</strong> {{ $special_instructions ?? '' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">CARGO DETAILS</div>
        <table>
            <tr>
                <th>Commodity</th>
                <th>Weight (lbs)</th>
                <th>Pieces</th>
                <th>Pallets</th>
                <th>Dimensions</th>
                <th>Hazmat</th>
            </tr>
            <tr>
                <td>{{ $commodity ?? '' }}</td>
                <td>{{ $weight ?? '' }}</td>
                <td>{{ $pieces ?? '' }}</td>
                <td>{{ $pallets ?? '' }}</td>
                <td>{{ $dimensions ?? '' }}</td>
                <td>{{ $hazmat ?? 'No' }}</td>
            </tr>
        </table>
        <div class="small-text">
            <strong>Temperature Requirements:</strong> {{ $temperature ?? 'N/A' }}<br>
            <strong>Trailer Type Required:</strong> {{ $trailer_type ?? '' }}<br>
            <strong>Equipment Requirements:</strong> {{ $equipment_requirements ?? '' }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">RATE AND PAYMENT INFORMATION</div>
        <table>
            <tr>
                <th width="70%">Description</th>
                <th width="30%">Amount</th>
            </tr>
            <tr>
                <td>Line Haul Rate</td>
                <td>${{ $linehaul_rate ?? '0.00' }}</td>
            </tr>
            @if(isset($fuel_surcharge) && $fuel_surcharge > 0)
            <tr>
                <td>Fuel Surcharge</td>
                <td>${{ $fuel_surcharge ?? '0.00' }}</td>
            </tr>
            @endif
            @if(isset($detention) && $detention > 0)
            <tr>
                <td>Detention</td>
                <td>${{ $detention ?? '0.00' }}</td>
            </tr>
            @endif
            @if(isset($additional_charges) && count($additional_charges) > 0)
                @foreach($additional_charges as $charge)
                <tr>
                    <td>{{ $charge['description'] }}</td>
                    <td>${{ $charge['amount'] }}</td>
                </tr>
                @endforeach
            @endif
            <tr>
                <th>TOTAL RATE</th>
                <th>${{ $total_rate ?? '0.00' }}</th>
            </tr>
        </table>

        <div class="box small-text">
            <p><strong>Payment Terms:</strong> {{ $payment_terms ?? 'Net 30 days from receipt of complete and accurate invoice and all required documentation' }}</p>
            <p><strong>Required Documentation:</strong> Invoice, signed BOL, signed Rate Confirmation, and any additional delivery receipts</p>
            <p><strong>Quick Pay:</strong> {{ $quick_pay ?? '3% discount for payment within 7 days of receipt of required documentation' }}</p>
            <p><strong>Send Invoice To:</strong> {{ $invoice_email ?? 'accounting@company.com' }}</p>
        </div>
    </div>

    <div class="section">
        <div class="section-title">TERMS AND CONDITIONS</div>
        <div class="small-text">
            <ol>
                <li>Carrier agrees to transport the above shipment in accordance with the terms of this Rate Confirmation and all applicable federal, state, and local laws and regulations.</li>
                <li>Carrier must notify Broker immediately of any delays, accidents, or other issues affecting the shipment.</li>
                <li>Carrier warrants that it possesses all required operating authority, permits, insurance coverage, and is authorized to transport the shipment described.</li>
                <li>Carrier must maintain insurance coverage at all times including: Auto Liability ($1,000,000 min), Cargo ($100,000 min), Workers Compensation (as required by law), and General Liability.</li>
                <li>Carrier is responsible for ensuring the safe loading, proper securement, and delivery of the cargo.</li>
                <li>Double brokering is strictly prohibited. Loads may not be re-brokered, co-brokered, subcontracted, or transferred to any other carrier without prior written consent.</li>
                <li>This shipment is subject to the Broker-Carrier Agreement between the parties. In the absence of such agreement, this Rate Confirmation and terms constitutes the entire agreement.</li>
                <li>Carrier must provide proof of delivery documents to receive payment. No payment will be made without required documentation.</li>
                <li>Carrier waives any right to lien on cargo transported under this Rate Confirmation.</li>
                <li>By signing below, Carrier acknowledges and agrees to all terms and conditions.</li>
            </ol>
        </div>
    </div>

    <div class="signature-area">
        <div class="columns">
            <div class="column">
                <div class="signature-field">
                    <div><strong>BROKER AUTHORIZED SIGNATURE</strong></div>
                    <div class="signature-line"></div>
                    <div>Name: {{ $broker_name ?? '____________________' }}</div>
                    <div>Title: {{ $broker_title ?? '____________________' }}</div>
                    <div>Date: {{ $broker_date ?? '____________________' }}</div>
                </div>
            </div>
            <div class="column">
                <div class="signature-field">
                    <div><strong>CARRIER AUTHORIZED SIGNATURE</strong></div>
                    <div class="signature-line"></div>
                    <div>Name: ____________________</div>
                    <div>Title: ____________________</div>
                    <div>Date: ____________________</div>
                </div>
            </div>
        </div>
        <div class="small-text important">
            THIS RATE CONFIRMATION IS NOT VALID UNLESS SIGNED BY BOTH PARTIES AND RETURNED TO BROKER
        </div>
    </div>
</body>
</html>
