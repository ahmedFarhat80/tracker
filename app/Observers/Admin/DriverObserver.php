<?php

namespace App\Observers\Admin;

use App\Models\Driver;

class DriverObserver
{
    /**
     * Handle the Driver "created" event.
     *
     * @param  \App\Models\Driver  $driver
     * @return void
     */
    public function created(Driver $driver)
    {
        //
    }

    /**
     * Handle the Driver "updated" event.
     *
     * @param  \App\Models\Driver  $driver
     * @return void
     */
    public function updated(Driver $driver)
    {
        //
    }

    /**
     * Handle the Driver "deleted" event.
     *
     * @param  \App\Models\Driver  $driver
     * @return void
     */
    public function deleted(Driver $driver)
    {
        /* Unlink old image from helper function call */
        !empty($driver->photo) ? UnlinkImage($driver->photo) : '';
        $driver->users()->detach();
    }

    /**
     * Handle the Driver "restored" event.
     *
     * @param  \App\Models\Driver  $driver
     * @return void
     */
    public function restored(Driver $driver)
    {
        //
    }

    /**
     * Handle the Driver "force deleted" event.
     *
     * @param  \App\Models\Driver  $driver
     * @return void
     */
    public function forceDeleted(Driver $driver)
    {
        //
    }
}
