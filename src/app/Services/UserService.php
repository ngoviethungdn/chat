<?php

namespace App\Services;

use App\Models\User as UserModel;
use Illuminate\Support\Facades\Auth;

class UserService extends BaseService
{
    private $userModel;

    public function __construct(UserModel $userModel) {
        $this->userModel = $userModel;
    }

    /**
     * Get list online user
     *
     * @return collection
     */
    public function getListContact()
    {
        return $this->userModel
            ->select(['id', 'name'])
            ->get();
    }

    /**
     * Get user detail
     *
     * @return collection
     */
    public function getDetail(int $userId)
    {
        return $this->userModel->find($userId);
    }
}
