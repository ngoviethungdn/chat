<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\SendMessagePost;
use App\Http\Requests\ListMessageGet;
use App\Services\MessageService;
use App\Http\Resources\Message as MessageResource;

class MessageController extends Controller
{
    private $messageService;

    /**
     * Construct function
     *
     * @param MessageService $messageService
     */
    public function __construct(MessageService $messageService) {
        $this->messageService = $messageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListMessageGet $request)
    {
        $messages = $this->messageService
            ->getListMessage(
                $request->input('to'),
                $request->input('type'),
                $request->input('last_message_id')
            );

        $this->setData(MessageResource::collection($messages));

        return $this->jsonOut();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SendMessagePost $request)
    {
        $message = $this->messageService
            ->createMessage(
                $request->input('to'),
                $request->input('type'),
                $request->input('message')
            );

        if (empty($message)) {
            abort(400);
        } 

        $this->setData($message);

        return $this->jsonOut();
    }
}
