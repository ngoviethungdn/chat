<?php

namespace App\Services;

use App\Models\Channel as ChannelModel;

class ChannelService extends BaseService
{
    private $channelModel;

    public function __construct(ChannelModel $channelModel) {
        $this->channelModel = $channelModel;
    }

    /**
     * Get list channel
     *
     * @return collection
     */
    public function getListChannel()
    {
        return $this->channelModel->get();
    }
}
