<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected function clearUsersCache(int $perPage = 50, int $maxPages = 100000000)
    {
        for ($page = 1; $page <= $maxPages; $page++) {
            $key = "users.page.$page.perpage.$perPage";
            Cache::forget($key);
        }
    }
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 50);

        $cacheKey = "users.page.$page.perpage.$perPage";

        return Cache::remember($cacheKey, 60, function () use ($perPage) {
            return User::orderBy('created_at', 'asc')->paginate($perPage);
        });
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $this->clearUsersCache();

        return response()->json($user, 201);
    }

    public function show($id)
    {
        return Cache::remember("users.$id", 60, function () use ($id) {
            return User::findOrFail($id)->only(['id', 'name', 'email']);
        });
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => "sometimes|required|email|unique:users,email,$id",
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        $this->clearUsersCache();


        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        $this->clearUsersCache();


        return response()->json(null, 204);
    }
}
