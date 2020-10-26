<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Trait ApiResponse
 *
 * @package App\Traits
 */
trait ApiResponse
{
    /**
     * Meta property
     *
     * @var null Meta content
     */
    protected $meta;

    /**
     * Data property
     *
     * @var array Data response
     */
    protected $data = [];

    /**
     * Status property
     *
     * @var int Status code
     */
    protected $status = JsonResponse::HTTP_OK;

    /**
     * Response json context
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function jsonOut()
    {
        $response = [];
        $this->meta['status'] = $this->status;

        $isStatusOK = JsonResponse::HTTP_OK === $this->meta['status'];

        if (!empty($this->data)) {
            $response[$isStatusOK ? 'data' : 'error'] = $this->data;
        }

        if (is_null($this->data) || (empty($this->data) && $isStatusOK)) {
            $response['data'] = null;
        }

        return response($response, $this->status);
    }

    /**
     * Set meta content of response
     *
     * @param string $message  Message of response
     * @param array  $optional Adding optional
     *
     * @return $this
     */
    public function setMeta(string $message = '', $optional = [])
    {
        $this->meta['message'] = $message;
        if (!empty($optional)) {
            $this->meta = array_merge($this->meta, $optional);
        }
        return $this;
    }

    /**
     * Set data of response
     *
     * @param array|mixed $data Data of response
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set status code
     *
     * @param int $status Status code
     *
     * @return $this
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Set error
     *
     * @param string     $message Error message
     * @param string|int $code    Error code
     * @param array      $data    Error list
     *
     * @return $this
     */
    public function setError(string $message, $code, $data = [])
    {
        $this->setData([
            'code' => $code,
            'message' => $message,
            'errors' => $data
        ]);

        return $this;
    }
}
