<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

// Defaut endpoint API: http://api-5sia1.test/api

// product controller
// menampung semua logika dan perontah yang diarahkan
// dari endpoint url di sini (api.php)
use App\Http\Controllers\ProductController;

/**
 * Api Resoucre untuk model Product
 */
// 1. Ambil semua data product berserta pemiliknya (user)
// action url = [NamaController::class, 'method']
Route::get('/products/semuanya', [ProductController::class,'index']); 

// 2. Cari produk tersedia berdasarkan nama
Route::get('/products/cari', [ProductController::class, 'search']);

// route Tambah product baru
Route::post('/products', [ProductController::class, 'store']);

// route Lihat detail product berdasarkan ID
Route::get('/products/find', [ProductController::class, 'show']);

// route Update product
Route::put('/products/update', [ProductController::class, 'update']);

// route Hapus product
Route::delete('/products/delete', [ProductController::class,'destroy']);

// route ambil semua data user
// Method Get
Route::get('/users',function(){
    // Panggil semua data user dan simpan dalam variabel $User
    $users = User::query()->with('products')->get();
    // Kembalikan data user dalam bentuk JSON
    // method with() digunakan untu mengikutsertakan relasi
    // relasi yang disebutkan sesuai dengan nama method pada model
    $json_users = json_encode($users);
    // berikan data (response) json ke apliasi yang meminta(request)
    return $json_users;
});

// route cari user berdasarkan id
// Method Get
Route::get('/user/find', function(Request $request){
    // cari user tanpa relasi ke tabel product
    // $user = User::find($request->id);
    // cari user dengan relasi
    $user = User::query()
        ->where('id', $request->id)
        ->with('products')
        ->get();
    return json_encode($user);
});

// route cari user berdasarkan kemiripan nama atau email
// Method Get
Route::get('/user/search', function(Request $request){
    // cari user berdasarkan string nama
    $users = User::where('name', 'like', '%'.$request->nama.'%')
    ->orWhere('email', 'like', '%'.$request->nama.'%')->get();
    // SELECT * FROM users WHERE name OR email LIKE '%ahmad%';
    return json_encode($users);
});

// registrasi user
// parameter nama, email, phone, password
// password harus di hash sebelum disimpan ke table


Route::post('/register', function (Request $r) {
   try {
     // validasi data
    $validate = $r->validate([
        // parameter = > rules
        'nama' => 'required|max:255',
        'surel' => 'required|email|unique:users,email',
        'sandi' => 'required|min:6',
        'telp' => 'required|unique:users,phone'
    ]);
        // tambahkan data userbaru
        $new_user = User::query()->create([
            // field => parameter
            'name' => $r->nama,
            'email' => $r->surel,
            'password' => Hash::make($r->sandi),
            'phone' => $r->telp
        ]);
        return response()->json($new_user);
   } catch (ValidationException $e){
    return $e->validator->errors();
   }

});


// Ubah data user
// parameter nama, surel, telp, sandi
// method 'PUT' atau 'PATCH'
// data user yg akan di ubah dicari berdasarkan id yg dikirim
// pada contoh ini, id akan langsung diasosiasikan ke model User
Route::put('/user/edit/{user}' ,function (Request $r, User $user) {
    // validasi ubah data
     try {
        //code..
    $validate = $r->validate([
        // parameter = > rules
        'nama' => 'max:255',
        'surel' => 'email|unique:users,email'.$user->id,
        'sandi' => 'min:6',
        'telp' => 'unique:users,phone'.$user->id
    ]);
    //......cara sederhana
    


    // .......cara yg komplek
    // salin data dari postmen ke variabel baru
    $data = $r->all();
    // persiapan jika pada array $data
    if (array_key_exists('sandi', $data)) {
        // replace isi sandi dengan hasil Hash sandi
        $data['sandi'] = Hash::make($data['sandi']);
    }
     // ubah data user
    $user->update([
        'nama' => $data['nama'] ?? $user->name,
        'email' => $data['surel'] ?? $user->email,
        'password' => $data['sandi'] ?? $user->password,
        'phone' => $data['telp'] ?? $user->phone
    ]);
    // kembalikan data user yg sudah di ubah berserta pesan sukses
    return response()->json([
        'pesan' => 'Sukser diubah!', 'user' => $user,
    ]);

    }catch (ValidationException $e){
    return $e->validator->errors();
    }
});

// Hapus data user
// method 'DELETE'
// request dilakukan dengan mernyertakan id user yg akan di hapus
Route::delete('/user/delete', function (Request $r){
    $user = User::find($r->id);
    //respon jika user tidak ditemukan
    if (! $user)
        return response()->json([
    'pesan' => 'Gagal! User tidak di temukan.'
        ]);

    //hapus data user
    $user->delete();
    return response()->json([
    'pesan' => 'Sukses! User berhasil dihapus.'
        ]);
});



// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');