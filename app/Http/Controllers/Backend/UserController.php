<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('role:users-list', ['only' => ['index', 'show']]);
        $this->middleware('role:users-create', ['only' => ['create', 'store']]);
        $this->middleware('role:users-edit', ['only' => ['edit', 'update']]);
        $this->middleware('role:users-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        $config['title'] = "Users";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Users"],
        ];
        if ($request->ajax()) {
            $data = User::with('roles');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('users.edit', $row->id) . '"><i class="fas fa-edit"></i></a>
                       <button type="button" data-bs-toggle="modal" data-bs-target="#modalReset" data-id="' . $row->id . '" class="btn btn-warning"><i class="fas fa-retweet"></i></button> 
                        <a class="btn btn-danger btn-delete" href="#" data-id ="' . $row->id . '" ><i class="fas fa-trash"></i></a>';
                    return $actionBtn;
                })->make();
        }
        return view('backend.users.index', compact('config'));
    }

    public function create()
    {
        $config['title'] = "Tambah User";
        $config['breadcrumbs'] = [
            ['url' => route('users.index'), 'title' => "Role"],
            ['url' => '#', 'title' => "Tambah User"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('users.store')
        ];
        return view('backend.users.form', compact('config'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|integer',
            'name' => 'required',
            'kode_satker' => 'required',
            'username' => 'required|alpha_dash|unique:users',
            'password' => 'required|between:6,255|confirmed',
            'email' => 'required|unique:users,email|email',
            'active' => 'required|between:0,1'
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            $dimensions = [array('300', '300', 'thumbnail')];
            try {

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = 'users_' . $file->getClientOriginalName();
                    // $file->storeAs('public/cover/', $filename);
                    $file->move(public_path('storage/user'), $filename);
                } else {
                    $filename = '-';
                }

                $data = User::create([
                    'role_id' => $request['role_id'],
                    'kode_satker' => $request['kode_satker'],
                    'name' => ucwords($request['name']),
                    'image' => $filename,
                    'email' => $request['email'],
                    'username' => $request['username'],
                    'password' => Hash::make($request['password']),
                    'active' => $request['active'],
                ]);

                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('users.index')]);
            } catch (\Throwable $throw) {
                DB::rollBack();
                Log::error($throw);
                $response = response()->json(['status' => 'error', 'message' => $throw->getMessage()]);
            }
        } else {
            $response = response()->json(['status' => 'error', 'message' => $validator->errors()->all()]);
        }
        return $response;
    }

    public function edit($id)
    {
        $config['title'] = "Edit User";
        $config['breadcrumbs'] = [
            ['url' => route('users.index'), 'title' => "Users"],
            ['url' => '#', 'title' => "Edit User"],
        ];
        $data = User::with(['roles', 'satker'])->where('id', $id)->first();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('users.update', $id)
        ];
        return view('backend.users.form', compact('config', 'data'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|integer',
            'name' => 'required',
            'kode_satker' => 'required',
            'username' => 'required|alpha_dash|unique:users,username,' . $request['username'] . ',username',
            'password' => 'between:6,255|confirmed|nullable',
            'email' => 'required|email|unique:users,email,' . $request['email'] . ',email',
            'active' => 'required|between:0,1'
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            $image = NULL;
            $dimensions = [array('300', '300', 'thumbnail')];
            try {
                $data = User::findOrFail($id);
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = 'users_' . $file->getClientOriginalName();
                    // $file->storeAs('public/cover/', $filename);
                    $file->move(public_path('storage/user'), $filename);
                } else {
                    $filename = $data->image;
                }
                $data->update([
                    'role_id' => $request['role_id'],
                    'kode_satker' => $request['kode_satker'],
                    'name' => ucwords($request['name']),
                    'email' => $request['email'],
                    'username' => $request['username'],
                    'active' => $request['active'],
                    'image' => $filename,
                ]);
                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('users.index')]);
            } catch (\Throwable $throw) {
                DB::rollBack();
                Log::error($throw);
                $response = response()->json(['status' => 'error', 'message' => $throw->getMessage()]);
            }
        } else {
            $response = response()->json(['status' => 'error', 'message' => $validator->errors()->all()]);
        }
        return $response;
    }

    public function destroy($id)
    {
        $response = response()->json([
            'status' => 'error',
            'message' => 'Data gagal dihapus'
        ]);
        $data = User::find($id);
        DB::beginTransaction();
        try {
            $data->delete();
            DB::commit();
            $response = response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Throwable $throw) {
            Log::error($throw);
            $response = response()->json(['error' => $throw->getMessage()]);
        }
        return $response;
    }

    public function resetpassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->passes()) {
            $data = User::find($request->id);
            $data->password = Hash::make($data['email']);
            if ($data->save()) {
                $response =  response()->json(['status' => 'success', 'message' => 'OK']);;
            }
        } else {
            $response = response()->json(['status' => 'error', 'message' => $validator->errors()->all()]);
        }
        return $response;
    }

    public function changepassword()
    {
        $config['title'] = "Ganti Password";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Ganti Password"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('update-change-password', auth()->id())
        ];
        return view('backend.users.change-password', compact('config'));
    }

    public function updatechangepassword(Request $request)
    {
        $data = Auth::user();

        $validator = Validator::make($request->all(), [
            'old_password' => ['required', new MatchOldPassword(Auth::id())],
            'password' => 'required|between:6,255|confirmed',
        ]);

        if ($validator->passes()) {
            $data->password = Hash::make($request['password']);
            if ($data->save()) {
                $response = response()->json($this->responseUpdate(true, route('dashboard')));
            }
        } else {
            $response = response()->json(['error' => $validator->errors()->all()]);
        }
        return $response;
    }

    public function profile()
    {
        $config['title'] = "Ubah Profile";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Ubah Profile"],
        ];

        $data = User::with('roles')->find(auth()->id());

        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('users.profileStore')
        ];
        return view('backend.users.profile', compact('config', 'data'));
    }

    public function profile_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'poster' => 'image|mimes:jpg,png,jpeg|max:5000',
            'name' => 'required',
            'username' => 'required|alpha_dash|unique:users,username,' . $request['username'] . ',username',
            'email' => 'required|email|unique:users,email,' . $request['email'] . ',email',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            $image = NULL;
            $dimensions = [array('300', '300', 'thumbnail')];
            try {
                $data = User::findOrFail(auth()->id());

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = 'users_' . $file->getClientOriginalName();
                    // $file->storeAs('public/cover/', $filename);
                    $file->move(public_path('storage/user'), $filename);
                } else {
                    $filename = $data->image;
                }

                $data->update([
                    'name' => ucwords($request['name']),
                    'email' => $request['email'],
                    'username' => $request['username'],
                    'image' => $filename,
                ]);

                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('dashboard')]);
            } catch (\Throwable $throw) {
                Log::error($throw);
                DB::rollBack();
                $response = response()->json(['error' => $throw->getMessage()]);
            }
        } else {
            $response = response()->json(['error' => $validator->errors()->all()]);
        }
        return $response;
    }

    public function select2(Request $request)
    {
        $page = $request->page;
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;
        $data = User::select('*')
            ->where('name', 'LIKE', '%' . $request->q . '%')
            ->orderBy('name')
            ->skip($offset)
            ->take($resultCount)
            ->selectRaw('id, name as text')
            ->get();

        $count = User::select('*')
            ->where('name', 'LIKE', '%' . $request->q . '%')
            ->get()
            ->count();

        $endCount = $offset + $resultCount;
        $morePages = $count > $endCount;

        $results = array(
            "results" => $data->map(function ($category) {
                return [
                    'id' => $category->id,  // Correct id reference
                    'text' => $category->text,  // Correct text reference
                    'ariaSelected' => false // Optional, can adjust based on selection logic
                ];
            }),
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }

    public function sync(Request $request)
    {
        ini_set('max_execution_time', 1800);

        // 1. Ambil list ID berita dari API native
        $response = Http::get("http://localhost/portal/api/user.php");

        if (!$response->successful()) {
            return response()->json(['status' => 'error', 'message' => 'Gagal ambil ID dari API']);
        }

        $ids = $response->json();

        foreach ($ids as $row) {
            $id = $row['id_user'];

            // 2. Ambil detail berita per ID
            $detail = Http::get("http://localhost/portal/api/user.php", [
                'id' => $id,
            ])->json();


            if (!$detail || !isset($detail['id_user'])) {
                continue;
            }

            DB::beginTransaction();
            try {
                // 3. Insert atau update ke DB Laravel
                User::updateOrCreate(
                    [
                        'id'   => $detail['id_user'],
                    ],
                    [
                        'name'       => $detail['nama_user'],
                        'username'   => preg_replace('/\s+/', '.', strtolower($detail['nama_user'])),
                        'email'      => $detail['email'],
                        'password'   => $detail['password'],
                        'role_id'    => $detail['id_role'],
                        'image'      => $detail['gambar'],
                        'active'     => 1,
                    ]
                );
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error("Gagal insert berita id=$id : " . $e->getMessage());
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Sync user selesai',
        ]);
    }
}
