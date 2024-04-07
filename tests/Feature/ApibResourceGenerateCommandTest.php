<?php declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ApibResourceGenerateCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testHandle(): void
    {
        if (file_exists(base_path('tmp/apib/Region.md'))) {
            unlink(base_path('tmp/apib/Region.md'));
        }

        if (file_exists(base_path('tmp/apib/Language.md'))) {
            unlink(base_path('tmp/apib/Language.md'));
        }

        if (file_exists(base_path('tmp/apib/SomeObjectResource.md'))) {
            unlink(base_path('tmp/apib/SomeObjectResource.md'));
        }
        $this->artisan('generate:apib App/Enums/Region --file')
            ->assertExitCode(0);
        $this->assertFileExists(base_path('tmp/apib/Region.md'));
        $this->assertFileEquals(__DIR__ . '/../Fixtures/Markdowns/Enums/Region.md', base_path('tmp/apib/Region.md'));

        $this->artisan('generate:apib App/Enums/Language --file')
            ->assertExitCode(0);
        $this->assertFileExists(base_path('tmp/apib/Language.md'));
        $this->assertFileEquals(__DIR__ . '/../Fixtures/Markdowns/Enums/Language.md', base_path('tmp/apib/Language.md'));

        $this->artisan('generate:apib App/Http/Resources/SomeObjectResource --file')
            ->assertExitCode(0);
        $this->assertFileExists(base_path('tmp/apib/SomeObjectResource.md'));
    }
}
