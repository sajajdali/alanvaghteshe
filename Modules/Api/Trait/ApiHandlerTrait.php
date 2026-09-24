<?php

namespace Modules\Api\Trait;

use Symfony\Component\HttpFoundation\Response;

trait ApiHandlerTrait
{

    const PAGINATE = 50;

    //create function for too many request
    public function tooManyRequest(
        string $message
    ): \Illuminate\Http\JsonResponse {
        return response()->json([
            'message' => $message
        ], Response::HTTP_TOO_MANY_REQUESTS, [
            'Content-Type' => 'application/json;charset=UTF-8',
            'Charset' => 'utf-8'
        ], JSON_UNESCAPED_UNICODE);//429
    }

    public function ok(mixed $data = [], bool $paginateData = false): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json;charset=UTF-8',
            'Charset' => 'utf-8'
        ], JSON_UNESCAPED_UNICODE);//200
    }


    //return created response
    public function created(mixed $data = []): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, Response::HTTP_CREATED, [
            'Content-Type' => 'application/json;charset=UTF-8',
            'Charset' => 'utf-8'
        ], JSON_UNESCAPED_UNICODE);//201
    }

    public function accepted(mixed $data = []): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, Response::HTTP_ACCEPTED, [
            'Content-Type' => 'application/json;charset=UTF-8',
            'Charset' => 'utf-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function badRequest(mixed $data = []): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, Response::HTTP_BAD_REQUEST, [
            'Content-Type' => 'application/json;charset=UTF-8',
            'Charset' => 'utf-8'
        ], JSON_UNESCAPED_UNICODE);//400
    }
    public function requestException(mixed $data = []): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, Response::HTTP_NOT_ACCEPTABLE, [
            'Content-Type' => 'application/json;charset=UTF-8',
            'Charset' => 'utf-8'
        ], JSON_UNESCAPED_UNICODE);//406
    }

    public function notFound(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => 'not found'
        ], Response::HTTP_NOT_FOUND, [
            'Content-Type' => 'application/json;charset=UTF-8',
            'Charset' => 'utf-8'
        ], JSON_UNESCAPED_UNICODE);//404
    }



}
