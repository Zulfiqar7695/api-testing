<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $users = User::all();
    //    p($users);
       if( count ($users)> 0){
        $response = [ 'message' => count ($users) . ' users found',
        'status' => 1,
        'data' => $users

   ];

       }
       else {
        $response = [ 'message' => count ($users) . ' users found',
        'status' => 0,
        ];
       }
       return response()->json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'first_name' => ['required', 'unique:users'],
        'last_name' => ['required', 'unique:users'],
        'email' => ['required', 'email', 'unique:users'],
        'password' => ['required', 'min:8'],
        'image' => 'required|mimes:png,jpg,jpeg,gif',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->messages(), 400);
    }

    $data = [
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ];

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);

        $data['image'] = $imageName;
    }

    DB::beginTransaction();
    try {
        $user = User::create($data);
        DB::commit();

        return response()->json([
            'message' => 'User was added successfully'
        ], 200);
    } catch (\Exception $e) {
        DB::rollback();
        p($e->getMessage());

        return response()->json([
            'message' => 'Adding user resulted in an error'
        ], 500);
    }
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validator = Validator::make($request->all(), [

            'first_name' => ['required', 'unique:users'],
            'last_name' => ['required', 'unique:users'],
            'email' => ['required', 'email', 'unique:users', 'email'],
            'image' => 'required|mimes:png,jpg,jpeg,gif',



        ]);
        if ($validator->fails()){

            return response()->json([$validator->messages()],400);
        }

        else{
$data = [
    'first_name' => $request->first_name,
    'last_name' => $request->last_name,
    'email' => $request->email,
    'image' => $request->image,


];
        }

        $user = User::find($id);
        if (is_null($user)) {
           return response()-> json ([
            'message' => "user doesn't exists",
            'status' => 0
           ],
           404
        );

        } else {
            DB::beginTransaction();
            try {
                $user -> first_name = $request ['first_name'];
                $user -> last_name = $request ['last_name'];
                $user -> email = $request ['email'];
                $user -> image = $request ['image'];
                $user -> save();
                DB::commit();
            }
                catch(\Exception $err){
                    DB::rollBack();
                    $user = null;
                }
                if (is_null($user)) {
                    return response()-> json ([
                        'message' => "internal server error",
                        'status' => 0
                       ],
                       500);
                } else{
                    return response()-> json ([
                        'message' => "User Data updated successfully",
                        'status' => 1
                       ],
                       200);
                }
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            $response = [
                'message' => "user doesn't exists",
                'status' => 0
            ];
            $respCode =  404;
        }
        else {
            DB::beginTransaction();
            try {
                $user -> delete();
                DB::commit();
                $response = [
                    'message' => "user deleted successfully",
                    'status' => 1
                ];
                $respCode =  200;
            } catch (\Exception $err){
                DB::rollback();
                $response = [
                    'message' => "Server error",
                    'status' => 0
                ];
                $respCode =  500;
            }
        }
        return response ()->json($response, $respCode);
    }



    public function changePassword(Request $request, $id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return response()->json([
                'message' => "User doesn't exist",
                'status' => 0
            ], 404);
        } else {
            $validator = Validator::make($request->all(), [
                'old_password' => ['required'],
                'new_password' => ['required', 'min:8'],
                'password_confirmed' => ['required', 'min:8', 'same:new_password'],
            ]);

            if ($validator->fails()) {
                return response()->json($validator->messages(), 400);
            }

            if ($user->password == $request['old_password']) {
                // Change password
                DB::beginTransaction();
                try {
                    $user->password = $request['new_password'];
                    $user->save();
                    DB::commit();
                } catch (\Exception $err) {
                    $user = null;
                    DB::rollBack();
                }

                if (is_null($user)) {
                    return response()->json([
                        'message' => "Internal server error",
                        'status' => 0
                    ], 500);
                } else {
                    return response()->json([
                        'message' => "Password updated successfully",
                        'status' => 1
                    ], 200);
                }
            } else {
                return response()->json([
                    'message' => "Old password doesn't match",
                    'status' => 0
                ]);
            }
        }
    }


}
