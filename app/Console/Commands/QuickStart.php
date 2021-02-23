<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class QuickStart extends Command
{
    const SAMPLE_ROLES = [
        'Administrator',
        'Editor',
        'End User'
    ];
    const SAMPLE_PERMISSIONS = [
        'Create Posts',
        'Edit Posts',
        'Read Posts',
        'Delete Posts',
        'Comment',
    ];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quickstart
                            {--email= : The email to associate with generated permission}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->option('email');
        if (! $email) {
            $this->info('quickstart requires an email address');

            return 0;
        }
        /** @var User $user */
        $user = User::query()->where('email', '=', $email)->first();
        if (! $user->exists()) {
            $this->info("User for $email does not exist.");

            return 0;
        }

        if ($this->createRolesAndPermissions()) {
            try {
                $role = Role::findByName('Administrator', 'web');
                $user->assignRole($role);
            } catch (RoleDoesNotExist $e) {

            }

            collect(self::SAMPLE_PERMISSIONS)->each(static function (string $permissionName) use ($role) {
                try {
                    if ($permissionName !== 'Comment') {
                        $role->givePermissionTo($permissionName);
                    }
                } catch (PermissionAlreadyExists $e) {

                }
            });
        }

        $this->info("QuickStart passed for $email!");

        return 0;
    }

    protected function createRolesAndPermissions(): bool
    {
        collect(self::SAMPLE_ROLES)->each(static function (string $roleName) {
            try {
                Role::create(['name' => $roleName, 'guard_name' => 'web']);
            } catch (RoleAlreadyExists $e) {

            }
        });
        collect(self::SAMPLE_PERMISSIONS)->each(static function (string $permissionName) {
            try {
                Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
            } catch (PermissionAlreadyExists $e) {

            }
        });

        return true;
    }
}
