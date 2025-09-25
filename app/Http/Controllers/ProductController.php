<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * Tampilkan semua data produk
     * Berserta pemiliknya (user)
     */
    public function index()
    {
        // untuk memanggil relasi terkait, sebutkan
        // nama method relasi yang ada di model tersebut
        // gunakan method with() untuk menyertakan relasi table
        // pada data yang dipanggil
        $products = Product::query()
        ->where('is_available', true) // hanya produk tersedia
        ->with('user')                // sertakan pemiliknya
        ->get();                      // eksekusi query
        return response()->json([
            'status' => 'Sukses',
            'data' => $products
        ]);
    }

    /**
     * cari produk berdasarkan name
     * dan ikutkan relasinya
     */
    public function search(Request $req)
    {
        try {
            // validasi minimal 3 huruf untuk pencarian
            $validated = $req->validate([
                'teks' => 'required|min:3', 
            ],[
                // pesan eror custom
                'teks.required' => ':Attribute jangan di kosongkan lah bos!',
                'teks.min' => 'Ini kurang dari :min bos!',
            ],[
                // custom attribute
                'teks' => "huruf"
            ]);
            
            // proses pencarian berdasarkan teks yang dikirim
            $products = Product::query()
            ->where('name', 'like', '%'. $req->teks.'%')
            ->with('user')
            ->get();
            // retrun hasil pencarian
            return response()->json([
                'pesan' => 'Sukses!',
                'data' => $products,
            ]);

        } // use Illuminate\Validation\ValidationException;
            catch (ValidationException $ex) {
            return response()->json([
                'pesan' => 'Gagal!',
                'data' => $ex->getMessage(),
            ]);
        }
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
