<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $news = User::paginate(15);
        return new JsonResponse($news, Response::HTTP_OK);
    }
}
