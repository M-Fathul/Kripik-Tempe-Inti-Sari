<?php

namespace Tests\Unit;

use App\Models\Kategori;
use App\Models\User;
use App\Policies\KategoriPolicy;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class KategoriPolicyTest extends TestCase
{
    public function test_kategori_policy_is_registered(): void
    {
        $policy = Gate::getPolicyFor(Kategori::class);

        $this->assertInstanceOf(KategoriPolicy::class, $policy);
    }

    public function test_admin_can_create_kategori(): void
    {
        $admin = new User(['role' => 'admin']);
        $policy = new KategoriPolicy;

        $this->assertTrue($policy->create($admin));
    }

    public function test_karyawan_cannot_create_kategori(): void
    {
        $karyawan = new User(['role' => 'karyawan']);
        $policy = new KategoriPolicy;

        $this->assertFalse($policy->create($karyawan));
    }
}
