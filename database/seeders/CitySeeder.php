<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Arr;
use Illuminate\Database\Seeder;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use League\Csv\Reader;
use Str;

class CitySeeder extends Seeder
{
    /** @var array */
    protected $cache = [];

    /** @var string[] */
    protected $files = [
        'usa.csv',
        'poland.csv'
    ];

    /** @var string */
    protected $path = 'database/seeders/csv/cities/';

    public function run()
    {
        foreach ($this->files as $file) {
            $path = base_path($this->path) . $file;

            if ( ! is_file($path)) {
                throw new FileNotFoundException(sprintf('Cities CSV file (%s) was not found', $file));
            }

            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords();

            foreach ($records as $record) {
                $stateName = $record['state'] ?? null;

                if ( ! empty($stateName)) {

                    if ( ! isset($this->cache['states'][$stateName])) {
                        $field = 'name';

                        if ($record['country'] == Country::NAME_UNITED_STATES) {
                            $field = 'abbreviation';
                        }

                        $state = State::where($field, $stateName)->first();

                        if ($state !== null) {
                            $this->cache['states'][$stateName] = $state->id;
                        }
                    }
                }

                if ( ! empty($stateName)) {

                    if (isset($this->cache['states'][$stateName])) {
                        $city = [
                            'name' => $record['name'],
                            'latitude' => $record['latitude'],
                            'longitude' => $record['longitude'],
                            'population' => $record['population'] ?? null,
                            'state_id' => $this->cache['states'][$stateName]
                        ];

                        if ($record['country'] == Country::NAME_POLAND) {
                            $city['timezone'] = 'Europe/Warsaw';
                        }

                        City::firstOrCreate(
                            Arr::only($city, ['name', 'state_id']),
                            Arr::except($city, ['name', 'state_id'])
                        );
                    }
                }
            }
        }
    }
}
