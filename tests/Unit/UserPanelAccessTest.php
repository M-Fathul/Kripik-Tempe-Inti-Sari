<?php

namespace Tests\Unit;

use App\Models\User;
use Filament\Panel;
use Tests\TestCase;

class UserPanelAccessTest extends TestCase
{
    public function test_admin_can_access_filament_panel(): void
    {
        $admin = new User(['role' => 'admin']);
        $panel = Panel::make('admin');

        $this->assertTrue($admin->canAccessPanel($panel));
    }

    public function test_karyawan_can_access_filament_panel(): void
    {
        $karyawan = new User(['role' => 'karyawan']);
        $panel = Panel::make('admin');

        $this->assertTrue($karyawan->canAccessPanel($panel));
    }

    public function test_unknown_role_cannot_access_filament_panel(): void
    {
        $user = new User(['role' => 'guest']);
        $panel = Panel::make('admin');

        $this->assertFalse($user->canAccessPanel($panel));
    }
}
