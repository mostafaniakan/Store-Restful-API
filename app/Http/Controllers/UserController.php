<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UsersInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = User::paginate(5);
        return $this->successResponse('', [
            'status' => UserResource::collection($user),
            'links' => UserResource::collection($user)->response()->getData()->links,
            'meta' => UserResource::collection($user)->response()->getData()->meta,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
            'cellphone' => 'required|numeric',
            'email' => ['required', "email:rfc,dns"],
            'email_verified_at' => 'required|email|same:email',
            'password' => ['required', Password::min(8)],
        ]);
        if ($validation->fails()) {
            return $this->errorResponse($validation->messages(), 422);
        }

        if (User::where('cellphone', $request->cellphone)->where('email', $request->email)->exists() === true) {
            return $this->errorResponse('user exists', 500);

        } else {

            $user = User::create([
                'name' => $request->name,
                'cellphone' => $request->cellphone,
                'email' => $request->email,
                'email_verified_at' => $request->email_verifi,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('token', ['server:update'])->plainTextToken;

            User::where('id', $user->id)->update(['remember_token' => $token]);

            return $this->successResponse('', [
                $user,
                $token
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $user = User::find($id);
        if ($user != null) {
            return $this->successResponse('', new UserResource($user), 200);
        } else {
            return $this->errorResponse('user not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(int $id, Request $request)
    {
        $user = User::find($id);

        $validation = Validator::make($request->all(), [
            'name' => 'string',
            'cellphone' => 'numeric',
            'email' => ["email:rfc,dns"],
            'password' => [Password::min(8)],
        ]);
        if ($validation->fails()) {
            return $this->errorResponse($validation->messages(), 422);
        }
        if (User::where('cellphone', $request->cellphone)->where('email', $request->email)->exists() === true) {
            return $this->errorResponse('user exists', 500);
        } else {
            $user->update([
                'name' => $request->has('name') ? $request->name : $user->name,
                'cellphone' => $request->has('cellphone') ? $request->cellphone : $user->cellphone,
                'email' => $request->has('email') ? $request->email : $user->email,
                'password' => $request->has('password') ? Hash::make($request->password) : $user->password,
            ]);
            $token = $user->createToken('token', ['server:update'])->plainTextToken;

            User::where('id', $user->id)->update(['remember_token' => $token]);

            return $this->successResponse('', [
                $user,
                $token
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $user = User::find($id);
        if ($user != null) {
            $user->delete();
            return $this->successResponse('', new UserResource($user), 200);
        } else {
            return $this->errorResponse('user not found', 404);
        }
    }

    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
            'cellphone' => 'required|numeric',
            'email' => ['required', "email:rfc,dns"],
            'password' => ['required', Password::min(8)],
        ]);
        if ($validation->fails()) {
            return $this->errorResponse($validation->messages(), 422);
        }

        $user = User::where('cellphone', $request->cellphone)->orWhere('email', $request->email)->first();
        if (!$user) {
            return $this->errorResponse('user not found', 404);
        }
        if (!Hash::check($request->password, $user->password)) {
            return $this->errorResponse('password is incorrect', 401);
        }
        $token = $user->createToken('token', ['server:update'])->plainTextToken;
        $user->update([
            'remember_token' => $token
        ]);
        return $this->successResponse('', [
            'user' => new UserResource($user),
            'token' => $token
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
//     find user login;
        return response()->json('user logout');
    }

    public function userInfo(Request $request)
    {
        dd($request->all());
        $user_id = auth()->user()->id;
        $validation = Validator::make($request->all(), [
            'address' => 'required',
            'postal_code' => 'required',
            'province_id ' => 'required',
            'city_id ' => 'required',

        ]);
        if ($validation->fails()) {
            return $this->errorResponse($validation->messages(), 422);
        }

        $userInfo = UsersInfo::create([
            'user_id' => $user_id,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
        ]);
        return $this->successResponse('ok', $userInfo, 200);
    }

    public function orders(User $user)
    {
        return $this->successResponse('', new UserResource($user->load('orders')));
    }
}
