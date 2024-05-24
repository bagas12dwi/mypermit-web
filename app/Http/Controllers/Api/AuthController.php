<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PermitNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'role' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return ApiFormatter::createApi(400, 'Validation failed', $validator->errors());
        }

        // Simpan data pengguna baru ke dalam basis data
        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->role = $request->role;
        $user->fcm_token = $request->fcm_token;
        $user->save();

        // Autentikasi pengguna yang baru saja didaftarkan
        if (Auth::attempt($request->only('username', 'password'))) {
            // Autentikasi berhasil, kirimkan respons sukses bersama dengan data pengguna
            return ApiFormatter::createApi(200, 'success', $user);
        } else {
            // Autentikasi gagal, kirimkan pesan kesalahan
            return ApiFormatter::createApi(400, 'Login failed', ['error' => 'Authentication failed']);
        }
    }

    public function login(Request $request)
    {
        $inputan = $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        $data = User::select('id', 'name', 'username', 'role', 'fcm_token')->where('username', $inputan['username'])->first();

        if ($request->input('fcm_token') != $data->fcm_token) {
            User::where('id', $data->id)->update([
                'fcm_token' => $request->input('fcm_token')
            ]);
        }
        // $manager = User::where('role', 'Manager')->get();
        // $manager->each(function ($manager) {
        //     $notification = new PermitNotification('test pesan');

        //     // Send notification
        //     try {
        //         $response = $manager->notify($notification);
        //         // Check if the notification was sent successfully
        //         if ($response['success']) {
        //             return json_encode('Notification sent successfully to user ' . $manager->id);
        //         } else {
        //             return json_encode('Failed to send notification to user ' . $manager->id . ': ' . $response['error']);
        //         }
        //     } catch (\Exception $e) {
        //         return json_encode('Failed to send notification to user ' . $manager->id . ': ' . $e->getMessage());
        //     }
        // });
        // return;

        if (Auth::attempt($inputan)) {
            // $request->session()->regenerate();
            return ApiFormatter::createApi(200, 'success', $data);
        }
        return ApiFormatter::createApi(400, 'failed');
    }

    public function getDetail(Request $request)
    {
        $id = $request->id;
        $data = User::select('id', 'name', 'username', 'role')->where('id', $id)->first();

        if ($data) {
            return ApiFormatter::createApi(200, 'success', $data);
        } else {
            return ApiFormatter::createApi(400, 'failed', $data);
        }
    }
}
