<?php

use App\Models\Permission\Permission;
use App\Models\Role\Role;
use App\Models\User\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LaratrustSeeder extends Seeder
{
    private $permission_role;
    private $permission_user;
    private $role_user;

    public function __construct()
    {
        $this->permission_role = config('laratrust.tables.permission_user');
        $this->permission_user = config('laratrust.tables.permission_role');
        $this->role_user = config('laratrust.tables.role_user');
    }


    /**
     * Run the database seeds.
     *
     * @return  void
     */
    public function run()
    {
        $this->command->info('Truncating User, Role and Permission tables');
        $this->truncateLaratrustTables();

        $config = config('laratrust_seeder.role_structure');
        $userPermission = config('laratrust_seeder.permission_structure');
        $mapPermission = collect(config('laratrust_seeder.permissions_map'));
        $accessLevels = config('laratrust_seeder.access_levels');
        $dashboards = config('dashboard.list');

        foreach ($config as $key => $modules) {
            $defaultDashboard = (($a = array_search(ucfirst($key), $dashboards)) !== false) ? $dashboards[$a]
                : ((($b = array_search(str_singular(ucfirst($key)), $dashboards)) !== false) ? $dashboards[$b] : null);

            // Create a new role
            $role = Role::create([
                'name' => $key,
                'display_name' => ucwords(str_replace('_', ' ', $key)),
                'description' => ucwords(str_replace('_', ' ', $key)),
                'level' => $accessLevels[$key],
                'dashboard' => $defaultDashboard,
            ]);
            $permissions = [];

            $this->command->info('Creating Role ' . strtoupper($key));

            // Reading role permission modules
            foreach ($modules as $module) {
                $title = ucfirst(str_replace('.', ' ', $module));
                $permissions[] = Permission::firstOrCreate([
                    'name' => $module,
                    'display_name' => $title,
                    'description' => $title,
                ])->id;

                $this->command->info('Creating Permission to ' . $module
                    . ' for ' . $key);
            }

            // Attach all permissions to the role
            $role->permissions()->sync($permissions);

            $this->command->info("Creating '{$key}' user");

            // Create default user for each role
            $user = factory(User::class)->create([
                'name' => ucwords(str_replace('_', ' ', $key)),
                'email' => $key . '@app.com',
                'password' => 'password'
            ]);

            $user->detachRoles();
            $user->attachRole($role);
        }

        // Creating user with permissions
        if (!empty($userPermission)) {
            foreach ($userPermission as $key => $modules) {
                foreach ($modules as $module => $value) {
                    // Create default user for each permission set
                    $user = User::create([
                        'name' => ucwords(str_replace('_', ' ', $key)),
                        'email' => $key . '@app.com',
                        'password' => bcrypt('password'),
                        'remember_token' => str_random(10),
                    ]);
                    $permissions = [];

                    foreach (explode(',', $value) as $p => $perm) {
                        $permissionValue = $mapPermission->get($perm);

                        $permissions[] = Permission::firstOrCreate([
                            'name' => $permissionValue . '-' . $module,
                            'display_name' => ucfirst($permissionValue) . ' '
                                . ucfirst($module),
                            'description' => ucfirst($permissionValue) . ' '
                                . ucfirst($module),
                        ])->id;

                        $this->command->info('Creating Permission to '
                            . $permissionValue . ' for ' . $module);
                    }
                }

                // Attach all permissions to the user
                /** @noinspection PhpUndefinedVariableInspection */
                $user->permissions()->sync($permissions);
            }
        }
    }

    /**
     * Truncates all the laratrust tables and the users table
     *
     * @return    void
     */
    public function truncateLaratrustTables()
    {
        Schema::disableForeignKeyConstraints();
        DB::table($this->permission_role)->truncate();
        DB::table($this->permission_user)->truncate();
        DB::table($this->role_user)->truncate();
        User::truncate();
        Role::truncate();
        Permission::truncate();
        Schema::enableForeignKeyConstraints();
    }
}
