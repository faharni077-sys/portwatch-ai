<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\Port;

class ImportPorts extends Command
{
    protected $signature = 'app:import-ports';

    protected $description = 'Import World Port CSV';

    public function handle()
    {
        $file = storage_path('app/UPPLY-SEAPORTS.csv');

        if (!file_exists($file)) {
            $this->error('CSV file tidak ditemukan!');
            return;
        }

        $handle = fopen($file, 'r');

        // Skip header
        fgetcsv($handle, 1000, ';');

        $count = 0;

        while (($row = fgetcsv($handle, 1000, ';')) !== false) {

            $country = Country::where('code', trim($row[4]))->first();

            if (!$country) {
                continue;
            }

            Port::updateOrCreate(

                [
                    'country_id' => $country->id,
                    'port_name'  => trim($row[1])
                ],

                [
                    'city'      => trim($row[1]),
                    'latitude'  => floatval($row[2]),
                    'longitude' => floatval($row[3])
                ]

            );

            $count++;
        }

        fclose($handle);

        $this->info("Berhasil import {$count} ports.");
    }
}