<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserSearchResource;

class UserController extends Controller
{
    use ApiResponse;
    
    public function searchUsers(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:1'
        ]);

        $query = $request->input('q');

        $users = User::where('id', '!=', $request->user()->id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                ->orWhere('email', 'like', "%$query%");
            })
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get();

        return $this->successResponse(
            UserSearchResource::collection($users),
            'Users fetched successfully'
        );
    }
}
