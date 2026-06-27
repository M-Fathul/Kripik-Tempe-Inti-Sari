<?php

namespace App\Policies;

use App\Models\Kategori;
use App\Models\User;

class KategoriPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Kategori $kategori): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Kategori $kategori): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Kategori $kategori): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, Kategori $kategori): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, Kategori $kategori): bool
    {
        return $this->isAdmin($user);
    }

    private function isAdmin(User $user): bool
    {
        return $user->role === 'admin';
    }
}
