<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;
    // izinkan semua kolom diisi secara masal
    protected $guarded = [];

    // format data saat di panggil
    protected $casts = ['is_available' => 'boolean'];

    // sembunykan kolom tertebtu
    protected $hidden = ['image_path'];

    // sisipkan data baru pada object produk
    protected $appends = ['image_url'];

    // format alamat gambar menjadi ulr
    public function imageUrl(): Attribute
    {
        return attribute::make(
            // format data saat di panggil dari database
            // ternary (short) if untuk memeriksa kolom image_path
            get: fn () => $this ->image_path
                            // retrun nrl foto
                            ? Storage::disk('public')->url
                            ($this->image_path) 
                            // retrun null jika tidak ada
                            : null,
            // set : format data yg akan di simpan ke database
        );
    }
    // sambungannya
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
