<?php

use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Support\Facades\Route;
use App\Bookings\ScheduleAvailability;

Route::get('/', function () {

    $avaibility = (new ScheduleAvailability())->forPeriod(
        now()->startOfDay(),
        now()->addMonth()->endOfDay(),

    );

    return view('welcome');
});