<?php

namespace App\Actions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * ZipToTimezone
 * 
 * This action is used to get the timezone for a given zipcode using the 'zip3_to_timezone' database.
 * 
 * 
 */
class ZipToTimezone
{
    use AsAction;

    public function handle(string $zipcode): string
    {

        return Cache::remember("zip3_to_timezone.{$zipcode}", 60, function () use ($zipcode) {
            $zip3 = substr($zipcode, 0, 3);

            $timezone = DB::connection('zip3_to_timezone')->table('zip3_to_timezone')->where('zip3', $zip3)->first();

            if (!$timezone) {
                return 'local';
            }

            // if daylight savings then timezone_2 else timezone_1
            return now()->isDST() ? $timezone->timezone_2 : $timezone->timezone_1;
        });
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle($request->zipcode);
    }

    public function rules(): array
    {
        return [
            'zipcode' => ['required', 'string', 'size:5'],
        ];
    }
}
