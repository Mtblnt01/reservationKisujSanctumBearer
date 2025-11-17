<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reservation;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Reservation::create([
            'name'=>'mozso15',
            'email'=>'mozso15@moriczref.hu',
            'reservation_time'=>'2025-12-01 08:48:00',
            'guests'=>4,
            'note'=>'Szülinapi vacsi'
        ]);

        //Reservation::factory()->count(10)->create();
    }
}
