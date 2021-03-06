<?php

namespace Lizyu\Permission;

use Illuminate\Support\ServiceProvider;
use Lizyu\Permission\Commands\LizRoleCommand;
use Lizyu\Permission\Contracts\PermissionContract;
use Lizyu\Permission\Contracts\RoleContract;
use Lizyu\Permission\Models\Permissions;
use Lizyu\Permission\Models\Roles;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Gate;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->reigsterCommand();
        $this->registerMigration();
        $this->bindContract();
        $this->registerGate();
    }
    
    /**
     * @description:bind service
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年1月13日
     */
    protected function bindContract()
    {
        $this->app->bind(PermissionContract::class, Permissions::class);
        $this->app->bind(RoleContract::class, Roles::class);
    }
    
    /**
     * @description:register command
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年1月13日
     */
    protected function reigsterCommand()
    {
        $this->commands(LizRoleCommand::class);
    }
    
    /**
     * @description:publish migration
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年1月13日
     */
    protected function registerMigration()
    {
        $stub = __DIR__ . '/../database/migrations/create_permissions_table.php';
        
        $migration = sprintf($this->app->databasePath() . '/migrations/%s_create_permissions_table.php', date('Y_m_d_His', time()));
        
        $this->publishes([ $stub=> $migration ], 'permission.migrations');
    }
    
    /**
     * @description:注册授权策略
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月10日
     * @param Gate $gate
     * @return \Illuminate\Contracts\Auth\Access\Gate
     */
    protected function  registerGate()
    {
        return $this->app->make(Gate::class)->before(function(Authenticatable $user, string $permission){
                    return app(PermissionContract::class)->PermissionBeOwned($user, $permission);
               });
    }
}
