<?php declare(strict_types=1);

namespace ArtisanCommandApibResource;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ApibResourceGenerateCommandServiceProvider extends LaravelServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(): void
    {
        $this->commands([
            ApibResourceGenerateCommand::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function boot(): void
    {
    }

    /**
     * @inheritdoc
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [
            ApibResourceGenerateCommand::class,
        ];
    }
}
