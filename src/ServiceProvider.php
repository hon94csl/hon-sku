<?php
/**
 * Created by PhpStorm
 * User: hon(陈烁临) qq: 2275604210
 * Date: 2022/1/10
 * Time: 3:40 下午
 */

namespace Hon\HonSku;


use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        //加载配置文件
        $this->mergeConfigFrom(__DIR__ . '/../config/hon-sku.php', 'hon-sku');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            //发布配置文件
            $this->publishes([
                __DIR__ . '/../config/hon-sku.php' => config_path('hon-sku.php')
            ], 'hon-sku-config');

            //发布数据库迁移文件
            $this->publishes([
                __DIR__.'/../database/migrations/create_morph_sku_tables.php.stub' => $this->getMigrationFileName()
            ], 'hon-sku-migrations');
        }
    }

    protected function getMigrationFileName()
    {
        $timestamp = date('Y_m_d_His');

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) {
                return (new Filesystem)->glob($path.'*_create_morph_sku_tables.php');
            })
            ->push($this->app->databasePath()."/migrations/{$timestamp}_create_morph_sku_tables.php")
            ->first();
    }
}