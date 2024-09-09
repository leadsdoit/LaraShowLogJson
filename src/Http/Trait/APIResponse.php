<?php

namespace Ldi\LogSpaViewer\Http\Trait;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;

trait APIResponse
{
    /**
     * Send response with success.
     *
     * @param  mixed  $result
     *
     * @return JsonResponse
     */
    protected function sendCreated(mixed $result = null): JsonResponse
    {
        return response()->json($this->makeResponse(true, $result), 201);
    }


    /**
     * Send response with success.
     *
     * @param  mixed  $result
     * @param  string|null  $message
     * @param  int  $code
     *
     * @return JsonResponse
     */
    protected function sendResponse(mixed $result = null, ?string $message = null, int $code = 200): JsonResponse
    {
        return response()->json($this->makeResponse(true, $result, $message), $code);
    }


    /**
     * Send response with success.
     *
     * @param  string|null  $message
     * @param  int  $code
     *
     * @return JsonResponse
     */
    protected function sendMessageResponse(?string $message = null, int $code = 202): JsonResponse
    {
        return response()->json($this->makeResponse(true, null, $message), $code);
    }

    /**
     * Send response with success.
     *
     * @param  int  $code
     * @return Response
     */
    protected function sendEmptyResponse(int $code = 204): Response
    {
        return response()->noContent($code);
    }

    /**
     * Send response with error.
     *
     * @param  string|null  $error
     * @param  int  $code
     * @param  Collection|array|null  $data
     *
     * @return JsonResponse
     */
    protected function sendError(?string $error = '', int $code = 400, Collection|array|null $data = null): JsonResponse
    {
        return response()->json($this->makeResponse(false, $data, $error), $code);
    }

    /**
     * Generate data array for response.
     *
     * @param  bool  $success
     * @param  mixed  $data
     *
     * @param  string|null  $message
     *
     * @return array
     */
    #[ArrayShape(['success' => "bool", 'message' => "string", 'data' => "array|\Illuminate\Support\Collection"])]
    protected function makeResponse(bool $success = true, mixed $data = null, ?string $message = null): array
    {
        $result = [
            'success' => $success,
        ];
        if (null !== $data) {
            $result['data'] = $data;
        }
        if (null !== $message) {
            $result['message'] = $message;
        }

        return $result;
    }

}
