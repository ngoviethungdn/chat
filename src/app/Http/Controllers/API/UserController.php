<?php

namespace App\Http\Controllers\API;

use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private $userService;

    /**
     * Construct function
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->setData($this->userService->getListContact());

        return $this->jsonOut();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $this->setData($this->userService->getDetail(Auth::id()));

        return $this->jsonOut();
    }
}
