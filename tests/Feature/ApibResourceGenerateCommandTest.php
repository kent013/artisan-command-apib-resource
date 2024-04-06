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
        $this->artisan('generate:apib')
            ->assertExitCode(0);
    }
}
