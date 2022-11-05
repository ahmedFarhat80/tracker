<?php

namespace App\Repositories;

use App\Interfaces\DriverRepositoryInterface;
use App\Models\Driver;

class DriverRepository implements DriverRepositoryInterface 
{
    public function getAllDrivers() {
        return Driver::selection()->orderBy('id', 'DESC')->paginate(PAGINATION_COUNT);
    }

    public function getDriverById($driverId) {
        return Driver::selection()->findOrFail($driverId);
    }

    public function deleteDriver($driverId) {
        Driver::destroy($driverId);
    }

    public function createDriver(array $driverDetails) {
        return Driver::create($driverDetails);
    }

    public function updateDriver($driverId, array $newDetails) {
        $driver = Driver::where('id', $driverId);
        $data   =  $driver->update($newDetails);
        
        return $driver = $driver->first();
    }

    public function getActiveDrivers() {
        return Driver::active()->selection()->orderBy('id', 'DESC')->paginate(PAGINATION_COUNT);
    }
}
