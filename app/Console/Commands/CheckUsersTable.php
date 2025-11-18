<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckUsersTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:users-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the structure of the users table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $columns = Schema::getColumnListing('users');
        
        $this->info('Users table columns:');
        foreach ($columns as $column) {
            $this->line("- {$column}");
        }
        
        // Check if phone column exists
        if (in_array('phone', $columns)) {
            $this->info('Phone column exists');
        } else {
            $this->error('Phone column does not exist');
        }
        
        // Check if provider columns exist
        if (in_array('provider', $columns)) {
            $this->info('Provider column exists');
        } else {
            $this->error('Provider column does not exist');
        }
        
        if (in_array('provider_id', $columns)) {
            $this->info('Provider ID column exists');
        } else {
            $this->error('Provider ID column does not exist');
        }
    }
}