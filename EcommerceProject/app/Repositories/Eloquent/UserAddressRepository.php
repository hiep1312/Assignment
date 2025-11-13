<?php

namespace App\Repositories\Eloquent;

use App\Models\UserAddress;
use App\Repositories\Contracts\UserAddressRepositoryInterface;

class UserAddressRepository extends BaseRepository implements UserAddressRepositoryInterface
{
    public function getModel()
    {
        return UserAddress::class;
    }
}
