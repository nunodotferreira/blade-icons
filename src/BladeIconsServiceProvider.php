<?php

declare(strict_types=1);

namespace BladeUI\Icons;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class BladeIconsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerConfig();
        $this->registerFactory();
    }

    public function boot(): void
    {
        $this->bootDirectives();
        $this->bootPublishing();
    }

    private function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/blade-icons.php', 'blade-icons');
    }

    private function registerFactory(): void
    {
        $config = $this->app->make('config')->get('blade-icons');

        $factory = new Factory(new Filesystem(), $config['class']);

        foreach ($config['sets'] as $set => $options) {
            $options['path'] = $this->app->basePath($options['path']);

            $factory->add($set, $options);
        }

        $this->app->instance(Factory::class, $factory);
    }

    private function bootDirectives(): void
    {
        Blade::directive('svg', function ($expression) {
            return "<?php echo e(svg($expression)); ?>";
        });
    }

    private function bootPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/blade-icons.php' => $this->app->configPath('blade-icons.php'),
            ], 'blade-icons');
        }
    }
}
