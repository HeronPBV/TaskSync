<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\Board;
use App\Models\Column;
use App\Policies\TaskPolicy;
use App\Policies\BoardPolicy;
use App\Policies\ColumnPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [

        Board::class => BoardPolicy::class,
        Column::class => ColumnPolicy::class,
        Task::class => TaskPolicy::class,

    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
