<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ExpertsOrderJob;

class TestJobsCommand extends Command
{
    protected $signature = 'test:jobs';

    protected $description = 'Test if ExpertsOrderJob is working by dispatching it';

    public function handle()
    {
        dispatch(new ExpertsOrderJob());

        $this->info('ExpertsOrderJob dispatched successfully!');
    }
}
