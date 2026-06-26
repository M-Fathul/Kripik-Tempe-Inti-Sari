<?php

namespace App\Policies;

use App\Models\Transaksi;
use App\Models\User;

class TransaksiPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, Transaksi $transaksi): bool
    {
        return $this->isStaff($user);
    }

    public function create(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function update(User $user, Transaksi $transaksi): bool
    {
        return $this->isStaff($user);
    }

    public function delete(User $user, Transaksi $transaksi): bool
    {
        return $this->isStaff($user);
    }

    public function restore(User $user, Transaksi $transaksi): bool
    {
        return $this->isStaff($user);
    }

    public function forceDelete(User $user, Transaksi $transaksi): bool
    {
        return $this->isStaff($user);
    }

    private function isStaff(User $user): bool
    {
        return in_array($user->role, ['admin', 'karyawan'], true);
    }
}
