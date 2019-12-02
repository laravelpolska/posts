<?php

namespace Tests\Feature\Authenticated;

use App\User;
use Tests\TestCase;

abstract class AuthenticatedTestCase extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();

        $this->actingAs($this->user);
    }
}
