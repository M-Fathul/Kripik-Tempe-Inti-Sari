<?php

namespace App\Policies;

use App\Models\Produk;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProdukPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Produk $produk): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Produk $produk): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Produk $produk): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, Produk $produk): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, Produk $produk): bool
    {
        return $this->isAdmin($user);
    }

    private function isAdmin(User $user): bool
    {
        return $user->role === 'admin';
    }
}
