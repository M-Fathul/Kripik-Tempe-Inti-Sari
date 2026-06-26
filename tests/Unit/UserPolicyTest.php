<?php

namespace Tests\Unit;

use App\Models\User;
use App\Policies\UserPolicy;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    public function test_admin_can_manage_users(): void
    {
        $admin = new User(['role' => 'admin']);
        $target = new User(['role' => 'karyawan']);
        $policy = new UserPolicy;

        $this->assertTrue($policy->viewAny($admin));
        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin, $target));
        $this->assertTrue($policy->delete($admin, $target));
    }

    public function test_karyawan_cannot_manage_users(): void
    {
        $karyawan = new User(['role' => 'karyawan']);
        $target = new User(['role' => 'admin']);
        $policy = new UserPolicy;

        $this->assertFalse($policy->viewAny($karyawan));
        $this->assertFalse($policy->create($karyawan));
        $this->assertFalse($policy->update($karyawan, $target));
        $this->assertFalse($policy->delete($karyawan, $target));
    }
}
