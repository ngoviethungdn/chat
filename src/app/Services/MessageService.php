<?php

namespace App\Services;

use App\Models\User as UserModel;
use App\Models\Channel as ChannelModel;
use App\Models\Message as MessageModel;
use App\Models\GroupMessage as GroupMessageModel;
use Illuminate\Support\Facades\Auth;
use App\Events\SendPrivateMessageEvent;
use App\Events\SendChannelMessageEvent;
use App\Http\Resources\Message as MessageResource;

class MessageService extends BaseService
{
    const MESSAGE_TYPE_PRIVATE = 'private';
    const MESSAGE_TYPE_GROUP = 'group';

    private $messageModel;
    private $groupMessageModel;
    private $userModel;
    private $channelModel;

    public function __construct(
        MessageModel $messageModel,
        GroupMessageModel $groupMessageModel,
        UserModel $userModel,
        ChannelModel $channelModel
    ) {
        $this->messageModel = $messageModel;
        $this->groupMessageModel = $groupMessageModel;
        $this->userModel = $userModel;
        $this->channelModel = $channelModel;
    }

    /**
     * Get list message
     *
     * @param int $to Channel id or user id
     * @param string $type Message type
     * @param int|null $lasMessageId Channel id or user id
     *
     * @return array
     */
    public function getListMessage(int $to, string $type, $lasMessageId = null)
    {
        $model = null;

        switch ($type) {
            case self::MESSAGE_TYPE_PRIVATE:
                $model = $this->messageModel
                    ->where(function($query) use ($to) {
                        $query->where([
                            'message_from' => Auth::id(),
                            'message_to' => $to
                        ]);
                        $query->orWhere(function($q) use ($to) {
                            $q->where([
                                'message_from' => $to,
                                'message_to' => Auth::id()
                            ]);
                        });
                    });
                break;
            case self::MESSAGE_TYPE_GROUP:
                $model = $this->groupMessageModel
                    ->where('channel_id', $to);
                break;
            default:
                return [];
        }

        if (!empty($lasMessageId)) {
            $model = $model->where('id', '<', intval($lasMessageId));
        }

        return $model->with(['fromUser' => function ($query) {
                return $query->select(['id', 'name']);
            }])
            ->orderBy('id', 'DESC')
            ->limit(self::DEFAULT_LIMIT)
            ->get();
    }
    
    /**
     * Create message
     *
     * @param int $to Channel id or user id
     * @param string $type Message type
     * @param string $content Message content
     *
     * @return boolean|object
     */
    public function createMessage(int $to, string $type, string $content)
    {
        $message = null;

        switch ($type) {
            case self::MESSAGE_TYPE_PRIVATE:
                return $this->createPrivateMessage($to, $content);
                break;
            case self::MESSAGE_TYPE_GROUP:
                return $this->createChannelMessage($to, $content);
                break;
            default:
                return false;
        }
    }

    /**
     * Create private message
     *
     * @param int $to
     * @param string $content
     * @return boolean|object
     */
    public function createPrivateMessage(int $to, string $content)
    {
        $receiver = $this->userModel->find($to);

        if (empty($receiver)) {
            return false;
        }

        $message = $this->messageModel
            ->create([
                'message_from' => Auth::id(),
                'message_to' => $to,
                'content' => $content
            ]);
        
        if (empty($message)) {
            return false;
        }

        $message = new MessageResource($message);
        broadcast(new SendPrivateMessageEvent($receiver, $message))->toOthers();

        return $message;
    }

    /**
     * Create channel message
     *
     * @param int $channelId
     * @param string $content
     * @return boolean|object
     */
    public function createChannelMessage(int $channelId, string $content)
    {
        $receiver = $this->channelModel->find($channelId);

        if (empty($receiver)) {
            return false;
        }

        $message = $this->groupMessageModel
            ->create([
                'channel_id' => $channelId,
                'user_id' => Auth::id(),
                'content' => $content
            ]);
        
        if (empty($message)) {
            return false;
        }

        $message = new MessageResource($message);
        broadcast(new SendChannelMessageEvent($receiver, $message))->toOthers();

        return $message;
    }
}
