<?php

use App\Models\MyProject\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $holidays = [
            [
                'name' => 'Shaheed Day',
                'full_date' => null,
                'date' => 21,
                'month' => 2,
                'year' => null,
                'special_holiday' => false
            ],
            [
                'name' => 'Sheikh Mujibur Rahman\'s Birthday',
                'full_date' => null,
                'date' => 17,
                'month' => 3,
                'year' => null,
                'special_holiday' => false
            ],
            [
                'name' => 'Independence Day',
                'full_date' => null,
                'date' => 26,
                'month' => 3,
                'year' => null,
                'special_holiday' => false
            ],
            [
                'name' => 'Bengali New Year',
                'full_date' => null,
                'date' => 14,
                'month' => 4,
                'year' => null,
                'special_holiday' => false
            ],
            [
                'name' => 'May Day',
                'full_date' => null,
                'date' => 1,
                'month' => 5,
                'year' => null,
                'special_holiday' => false
            ],
            [
                'name' => 'National Mourning Day',
                'full_date' => null,
                'date' => 15,
                'month' => 8,
                'year' => null,
                'special_holiday' => false
            ],
            [
                'name' => 'Victory Day',
                'full_date' => null,
                'date' => 16,
                'month' => 12,
                'year' => null,
                'special_holiday' => false
            ]
        ];

        $h = [];
        for($y = 0; $y < 100; $y++){
            $year = date('Y',time()) + $y;
            foreach ($holidays as $holiday){
                $h[] = [
                    'name' => $holiday['name'],
                    'full_date' => $year.'-'.$holiday['month'].'-'.$holiday['date'],
                    'date' => $holiday['date'],
                    'month' => $holiday['month'],
                    'year' => $year,
                    'special_holiday' => $holiday['special_holiday']
                ];
            }
        }
        Holiday::insert($h);
    }
}
