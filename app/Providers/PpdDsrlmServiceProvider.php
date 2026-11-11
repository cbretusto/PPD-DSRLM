<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\UserManagementInterface;
use App\Interfaces\RemarkInterface;
use App\Interfaces\DeviceInterface;
use App\Interfaces\EmailRecipientsInterface;

use App\Repositories\UserManagementRepository;
use App\Repositories\RemarkRepository;
use App\Repositories\DeviceRepository;
use App\Repositories\EmailRecipientsRepository;


Barryvdh\DomPDF\ServiceProvider::class;

class PpdDsrlmServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserManagementInterface::class, UserManagementRepository::class);
        $this->app->bind(RemarkInterface::class, RemarkRepository::class);
        $this->app->bind(DeviceInterface::class, DeviceRepository::class);
        $this->app->bind(EmailRecipientsInterface::class, EmailRecipientsRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
