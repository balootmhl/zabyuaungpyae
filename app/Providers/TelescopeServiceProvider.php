<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    public function register(): void
    {
        if (defined('PHPUNIT_COMPOSER_INSTALL') || class_exists(\PHPUnit\Framework\TestCase::class) || $this->app->environment('testing')) {
            return;
        }

        parent::register();

        $this->hideSensitiveRequestDetails();

        Telescope::filter(function (IncomingEntry $entry) {
            if ($this->app->environment('local')) {
                return true;
            }

            return $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    public function boot(): void
    {
        if (defined('PHPUNIT_COMPOSER_INSTALL') || class_exists(\PHPUnit\Framework\TestCase::class) || $this->app->environment('testing')) {
            return;
        }

        parent::boot();
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if (defined('PHPUNIT_COMPOSER_INSTALL') || class_exists(\PHPUnit\Framework\TestCase::class) || $this->app->environment('testing')) {
            return;
        }

        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }
}
