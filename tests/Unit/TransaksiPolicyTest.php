<?php

namespace Tests\Unit;

use App\Models\Transaksi;
use App\Models\User;
use App\Policies\TransaksiPolicy;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class TransaksiPolicyTest extends TestCase
{
    public function test_transaksi_policy_is_registered(): void
    {
        $policy = Gate::getPolicyFor(Transaksi::class);

        $this->assertInstanceOf(TransaksiPolicy::class, $policy);
    }

    public function test_karyawan_has_full_transaksi_access(): void
    {
        $karyawan = new User(['role' => 'karyawan']);
        $transaksi = new Transaksi;
        $policy = new TransaksiPolicy;

        $this->assertTrue($policy->viewAny($karyawan));
        $this->assertTrue($policy->view($karyawan, $transaksi));
        $this->assertTrue($policy->create($karyawan));
        $this->assertTrue($policy->update($karyawan, $transaksi));
        $this->assertTrue($policy->delete($karyawan, $transaksi));
        $this->assertTrue($policy->restore($karyawan, $transaksi));
        $this->assertTrue($policy->forceDelete($karyawan, $transaksi));
    }

    public function test_admin_has_full_transaksi_access(): void
    {
        $admin = new User(['role' => 'admin']);
        $transaksi = new Transaksi;
        $policy = new TransaksiPolicy;

        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->delete($admin, $transaksi));
    }
}
