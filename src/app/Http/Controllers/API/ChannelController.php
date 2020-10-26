<?php

namespace App\Http\Controllers\API;

use App\Services\ChannelService;

class ChannelController extends Controller
{
    private $channelService;

    /**
     * Construct function
     *
     * @param ChannelService $channelService
     */
    public function __construct(ChannelService $channelService) {
        $this->channelService = $channelService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->setData($this->channelService->getListChannel());

        return $this->jsonOut();
    }
}
