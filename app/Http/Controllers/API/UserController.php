<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function fetchAllUser()
    {
        try {
            $users = User::get();

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched all users',
                'data' => $users
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function store(Request $request) {
        $requestData = $request->all();
        $userInputData = $this->userStoreMapping($requestData);
        try {
            $existingUser = User::where('email', $userInputData['email'])->first();

            if($existingUser) {
                return response()->json([
                'success' => false,
                'message' => 'User is already registered'
                ], 400);
            }

            $user = new User();
            $user->name = $userInputData['name'];
            $user->email = $userInputData['email'];
            $user->password = $userInputData['password'];
            $user->save();

            $newUser['name'] = $user->name;
            $newUser['email'] = $user->email;

            return response()->json([
                'success' => true,
                'message' => 'Successfully create new User',
                'data' => $newUser
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request) {
        $requestData = $request->all();
        $userInputData = $this->userStoreMapping($requestData);
        try {
            $user = User::where('email', $userInputData['email'])->first();
            if(!$user) {
                return response()->json([
                'success' => false,
                'message' => 'User is not found'
                ], 400);
            }

            $user->name = $userInputData['name'];
            $user->email = $userInputData['email'];
            $user->password = $userInputData['password'];
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Successfully update User'
            ], 201);
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function userStoreMapping($data) {
        $userData['name'] = $data['name'];
        $userData['email'] = $data['email'];
        $userData['password'] = \bcrypt($data['password']);

        return $userData;
    }

    public function destroy(Request $request) {
        try {
            $user = User::find($request->user_id);
            if(!$user) {
                return response()->json([
                'success' => false,
                'message' => 'User is not found'
                ], 400);
            }
            User::destroy($user->id);

            return response()->json([
                'success' => false,
                'message' => 'User is deleted'
                ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
