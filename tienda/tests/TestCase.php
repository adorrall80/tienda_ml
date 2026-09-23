<?php

namespace Tests;

use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('roles')) {
            return;
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['admin', 'vendedor', 'cliente'] as $role) {
            Role::findOrCreate($role);
        }
    }
}
