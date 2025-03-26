Tutorial Membuat POS
## Installation
1. Membuat project laravel
```bash
composer create-project laravel/laravel laravel-pos-app
```

2. Konfigurasi database di file .env
```php
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_pos
DB_USERNAME=root
DB_PASSWORD=
```
3. ubah migration users menjadi seperti ini
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->enum('role', ['admin', 'kasir'])->default('kasir');
    $table->rememberToken();
    $table->timestamps();
});
```
4. membuat migrasi table kategori
```bash
php artisan make:migration create_kategoris_table
```
5. ubah file migrasi kategori 
```php
Schema::create('kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
```
6. Buat migrasi untuk tabel produk
```bash
php artisan make:migration create_produks_table
```
7. Ubah file migrasi produk
```php
 Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('gambar')->nullable();
            $table->decimal('harga', 10, 2);
            $table->timestamps();
        });
```
8. Buat migrasi untuk table produk_kategori
```bash
php artisan make:migration create_produk_kategoris_table
```
9. Ubah file migrasi table produk_kategori
```php
 Schema::create('produk_kategoris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->foreignId('kategori_id')->constrained('kategoris')->onDelete('cascade');
            $table->timestamps();
        });
```
10. Buat migrasi untuk table pembelian
```bash
php artisan make:migration create_pembelians_table
```
11. Ubah file migrasi untuk table pembelian
```php
Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pembeli');
            $table->decimal('total_harga', 12, 2);
            $table->enum('metode_pembayaran', ['tunai', 'qris', 'transfer']);
            $table->enum('status_pembayaran', ['lunas', 'pending', 'batal'])->default('pending');
            $table->timestamps();
        });
```
12. Buat migrasi untuk table pembelian_detail
```bash
php artisan make:migration create_pembelian_details_table
```
13. Ubah migrasi untuk table pembelian_detail
```php
Schema::create('pembelian_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelians')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->decimal('harga', 10, 2);
            $table->integer('jumlah')->default(1);
            $table->timestamps();
        });
```
14. jalankan migrasi
```bash
php artisan migrate
```
15. edit model user Edit file app/Models/User.php pada bagian fileable
```php 
protected $fillable = [
    'name',
    'email',
    'password',
    'role',
];
```
16. Buat model untuk Kategori
```bash
php artisan make:model Kategori
```
17. Edit model Kategori
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'gambar'];

    public function produks()
    {
        return $this->belongsToMany(Produk::class, 'produk_kategoris');
    }
}
```
18. Buat model Produk
```bash
php artisan make:model Produk
```
19. Edit model Produk
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'gambar', 'harga'];

    public function kategoris()
    {
        return $this->belongsToMany(Kategori::class, 'produk_kategoris');
    }

    public function pembelianDetails()
    {
        return $this->hasMany(PembelianDetail::class);
    }
}
```
20. Buat Model ProdukKategori
```bash
php artisan make:model ProdukKategori
```
21. Edit Model ProdukKategori
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukKategori extends Model
{
    use HasFactory;

    protected $fillable = ['produk_id', 'kategori_id'];
}
```
22. Buat Model Pembelian
```bash
php artisan make:model Pembelian
```
23. Edit Model Pembelian
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $fillable = ['nama_pembeli', 'total_harga', 'metode_pembayaran', 'status_pembayaran'];

    public function detail()
    {
        return $this->hasMany(PembelianDetail::class);
    }
}
```
24. Buat Model PembelianDetail
```bash
php artisan make:model PembelianDetail
```
25. Edit Model PembelianDetail
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    use HasFactory;

    protected $fillable = ['pembelian_id', 'produk_id', 'harga', 'jumlah'];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
```
26. Membuat Seeder untuk Admin dan Kasir
```bash
php artisan make:seeder UserSeeder
```
27. Edit file database/seeders/UserSeeder.php
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@pos.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        
        // Kasir
        User::create([
            'name' => 'Kasir',
            'email' => 'kasir@pos.test',
            'password' => Hash::make('password'),
            'role' => 'kasir',
        ]);
    }
}
```
28. Edit file database/seeders/DatabaseSeeder.php
```php
public function run()
{
    $this->call(UserSeeder::class);
}
```
29. Jalankan Seeder
```bash
php artisan db:seed
```
30. Buat middleware untuk mengecek role
```bash
php artisan make:middleware CheckRole
```
31. Edit file app/Http/Middleware/CheckRole.php
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            return redirect('/dashboard');
        }
        
        return $next($request);
    }
}
```
32. Membuat Controller untuk Dashboard
```bash
php artisan make:controller DashboardController
```
33. Membuat Controller untuk Login & logout
```bash
php artisan make:controller AuthController
```
34. Membuat Controller untuk User
```bash
php artisan make:controller UserController
```
35. Membuat Controller untuk Kategori
```bash
php artisan make:controller KategoriController
```
36. Membuat Controller untuk Produk
```bash
php artisan make:controller ProdukController
```
37. Membuat Controller untuk Pembelian
```bash
php artisan make:controller PembelianController
```
38. Membuat Controller untuk POS
```bash
php artisan make:controller POSController
```
39. Membuat Middleware Untuk Mengecek Role
```bash
php artisan make:middleware CheckRole
```
40. Edit Middleware Untuk Mengecek Role app\Http\Middleware\CheckRole.php
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
```
41. Edit file DashboardController
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Pembelian;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $produkCount = Produk::count();
        $kategoriCount = Kategori::count();
        $pembelianCount = Pembelian::count();

        // Statistik pembelian per hari (7 hari terakhir)
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $dailyStats = Pembelian::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_harga) as total')
        )
        ->where('created_at', '>=', $startDate)
        ->where('created_at', '<=', $endDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Format untuk chart
        $labels = [];
        $data = [];

        // Isi data untuk 7 hari
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::now()->subDays($i)->format('d M');

            $found = false;
            foreach ($dailyStats as $stat) {
                if ($stat->date == $date) {
                    $data[] = $stat->total;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $data[] = 0;
            }
        }

        return view('dashboard', compact('produkCount', 'kategoriCount', 'pembelianCount', 'labels', 'data'));
    }
}
```
42. Edit file AuthController
```php
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect berdasarkan role
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('dashboard');
            } elseif ($user->role === 'kasir') {
                return redirect()->route('dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
```
43. Edit file UserController
```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,kasir',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,kasir',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }
}
```
44. Edit file KategoriController
```php
<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::all();
        return view('kategoris.index', compact('kategoris'));
    }

    public function create()
    {
        return view('kategoris.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'nullable|image|max:2048',
        ]);

        $data = [
            'nama' => $request->nama,
        ];

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('kategoris', $filename,'public');
            $data['gambar'] = $filename;
        }

        Kategori::create($data);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function edit(Kategori $kategori)
    {
        return view('kategoris.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'nullable|image|max:2048',
        ]);

        $data = [
            'nama' => $request->nama,
        ];

        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($kategori->gambar) {
                Storage::delete('public/kategoris/' . $kategori->gambar);
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('kategoris', $filename, 'public');
            $data['gambar'] = $filename;
        }

        $kategori->update($data);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy(Kategori $kategori)
    {
        // Delete image if exists
        if ($kategori->gambar) {
            Storage::delete('public/kategoris/' . $kategori->gambar);
        }

        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus');
    }
}
```
45. Edit file ProdukController
```php
<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = Produk::with('kategoris')->get();
        return view('produks.index', compact('produks'));
    }

    public function create()
    {
        $kategoris = Kategori::all();
        return view('produks.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|max:2048',
            'kategori_ids' => 'required|array',
            'kategori_ids.*' => 'exists:kategoris,id',
        ]);

        DB::beginTransaction();

        try {
            $data = [
                'nama' => $request->nama,
                'harga' => $request->harga,
            ];

            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('produks', $filename,'public');
                $data['gambar'] = $filename;
            }

            $produk = Produk::create($data);

            // Attach categories
            $produk->kategoris()->attach($request->kategori_ids);

            DB::commit();

            return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(Produk $produk)
    {
        $kategoris = Kategori::all();
        $selectedKategoris = $produk->kategoris->pluck('id')->toArray();

        return view('produks.edit', compact('produk', 'kategoris', 'selectedKategoris'));
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|max:2048',
            'kategori_ids' => 'required|array',
            'kategori_ids.*' => 'exists:kategoris,id',
        ]);

        DB::beginTransaction();

        try {
            $data = [
                'nama' => $request->nama,
                'harga' => $request->harga,
            ];

            if ($request->hasFile('gambar')) {
                // Delete old image
                if ($produk->gambar) {
                    Storage::delete('public/produks/' . $produk->gambar);
                }

                $file = $request->file('gambar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('produks', $filename,'public');
                $data['gambar'] = $filename;
            }

            $produk->update($data);

            // Sync categories
            $produk->kategoris()->sync($request->kategori_ids);

            DB::commit();

            return redirect()->route('produk.index')->with('success', 'Produk berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Produk $produk)
    {
        DB::beginTransaction();

        try {
            // Delete image if exists
            if ($produk->gambar) {
                Storage::delete('public/produks/' . $produk->gambar);
            }

            // Detach all categories
            $produk->kategoris()->detach();

            // Delete product
            $produk->delete();

            DB::commit();

            return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
```
46. Edit file PembelianController
```php
<?php
namespace App\Http\Controllers;

use App\Models\Pembelian;
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    public function index()
    {
        $pembelian = Pembelian::select('id', 'nama_pembeli', 'total_harga', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pembelian.index', compact('pembelian'));
    }

    public function detail($id)
    {
        $pembelian = Pembelian::with('detail.produk')
            ->findOrFail($id);

        return view('pembelian.detail', compact('pembelian'));
    }
}
```
47. Edit file POSController
```php
<?php
namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::all();
        $produks = Produk::with('kategoris')->get();
        return view('pos.index', compact('kategoris', 'produks'));
    }

    public function getProdukByKategori($kategori_id)
    {
        $produks = Produk::whereHas('kategoris', function($query) use ($kategori_id) {
            $query->where('kategoris.id', $kategori_id);
        })->get();

        return response()->json($produks);
    }

    public function prosesPembayaran(Request $request)
    {
        $request->validate([
            'nama_pembeli' => 'required|string|max:255',
            'produk' => 'required|array',
            'jumlah' => 'required|array',
            'total_harga' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris',
            'status_pembayaran' => 'required|in:lunas,belum_lunas'
        ]);

        DB::beginTransaction();
        try {
            $pembelian = Pembelian::create([
                'nama_pembeli' => $request->nama_pembeli,
                'total_harga' => $request->total_harga,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => $request->status_pembayaran
            ]);

            foreach ($request->produk as $index => $produk_id) {
                $produk = Produk::findOrFail($produk_id);
                $pembelian->detail()->create([
                    'produk_id' => $produk_id,
                    'harga' => $produk->harga,
                    'jumlah' => $request->jumlah[$index]
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'pembelian_id' => $pembelian->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cetakStruk($pembelianId)
    {
        $pembelian = Pembelian::with('detail.produk')->findOrFail($pembelianId);
        return view('pos.struck', compact('pembelian'));
    }
}
```
48. Membuat Layout untuk Login resources/views/layouts/auth.blade.php
```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    @yield('styles')
</head>
<body>
    <div class="d-flex justify-content-center align-items-center min-vh-100">
        @yield('content')
    </div>
     <script src="https://code.jquery.com/jquery-3.7.1.min.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @yield('scripts')
</body>
</html>
```
49. Membuat Layout untuk halaman admin dan kasir resources/views/layouts/app.blade.php
```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                Dashboard
                            </a>
                        </li>

                        @if(auth()->user()->role == 'admin')
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->is('kategori*') ? 'active' : '' }}" href="{{ route('kategori.index') }}">
                                Kategori
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->is('produk*') ? 'active' : '' }}" href="{{ route('produk.index') }}">
                                Produk
                            </a>
                        </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->is('pembelian*') ? 'active' : '' }}" href="{{ route('pembelian.index') }}">
                                Pembelian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->is('pos*') ? 'active' : '' }}" href="{{ route('pos.index') }}">
                                POS
                            </a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link text-white border-0 bg-transparent">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1>@yield('title')</h1>
                </div>

                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
     <script src="https://code.jquery.com/jquery-3.7.1.min.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @yield('scripts')
</body>
</html>
```
50. Membuat halaman login resources/views/auth/login.blade.php
```blade
@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Login</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```
51. Membuat halaman dashboard resources/views/dashboard.blade.php
```blade
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Produk</h5>
                <h2>{{ $produkCount }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Total Kategori</h5>
                <h2>{{ $kategoriCount }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Total Pembelian</h5>
                <h2>{{ $pembelianCount }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                Statistik Pembelian (7 Hari Terakhir)
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: {!! json_encode($data) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection
```
52. Membuat halaman user resources/views/users/index.blade.php
```blade
@extends('layouts.app')

@section('title', 'Kelola Users')

@section('content')
<div class="mb-3">
    <a href="{{ route('users.create') }}" class="btn btn-primary">Tambah User</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>

                        @if($user->id != auth()->id())
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">Hapus</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
```
53. Membuat halaman user resources/views/users/create.blade.php
```blade
@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="kasir">Kasir</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
```
54. Membuat halaman user resources/views/users/edit.blade.php
```blade
@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password (Kosongkan jika tidak diubah)</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="kasir" {{ $user->role == 'kasir' ? 'selected' : '' }}>Kasir</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
```
55. Membuat halaman kategori resources/views/kategoris/index.blade.php
```blade
@extends('layouts.app')

@section('title', 'Kelola Kategori')

@section('content')
<div class="mb-3">
    <a href="{{ route('kategori.create') }}" class="btn btn-primary">Tambah Kategori</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kategoris as $index => $kategori)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $kategori->nama }}</td>
                    <td>
                        @if($kategori->gambar)
                            <img src="{{ asset('storage/kategoris/' . $kategori->gambar) }}" alt="{{ $kategori->nama }}" height="50">
                        @else
                            <span class="text-muted">Tidak ada gambar</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('kategori.edit', $kategori->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
```
56. Membuat halaman kategori resources/views/kategoris/create.blade.php
```blade
@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('kategori.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="nama" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar (Opsional)</label>
                <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar">
                @error('gambar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
```
57. Membuat halaman kategori resources/views/kategoris/edit.blade.php
```blade
@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('kategori.update', $kategori->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nama" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $kategori->nama) }}" required>
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar (Opsional)</label>
                @if($kategori->gambar)
                    <div class="mb-2">
                        <img src="{{ asset('storage/kategoris/' . $kategori->gambar) }}" alt="{{ $kategori->nama }}" height="100">
                    </div>
                @endif
                <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar">
                <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar</small>
                @error('gambar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
```
58. Membuat halaman produk resources/views/produks/index.blade.php
```blade
@extends('layouts.app')

@section('title', 'Kelola Produk')

@section('content')
<div class="mb-3">
    <a href="{{ route('produk.create') }}" class="btn btn-primary">Tambah Produk</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Gambar</th>
                    <th>Harga</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produks as $index => $produk)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $produk->nama }}</td>
                    <td>
                        @if($produk->gambar)
                            <img src="{{ asset('storage/produks/' . $produk->gambar) }}" alt="{{ $produk->nama }}" height="50">
                        @else
                            <span class="text-muted">Tidak ada gambar</span>
                        @endif
                    </td>
                    <td>{{ number_format($produk->harga, 0, ',', '.') }}</td>
                    <td>
                        @foreach($produk->kategoris as $kategori)
                            <span class="badge bg-info">{{ $kategori->nama }}</span>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('produk.edit', $produk->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('produk.destroy', $produk->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
```
59. Membuat halaman produk resources/views/produks/create.blade.php
```blade
@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="nama" class="form-label">Nama Produk</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" class="form-control @error('harga') is-invalid @enderror" id="harga" name="harga" value="{{ old('harga') }}" required>
                </div>
                @error('harga')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="kategori_ids" class="form-label">Kategori (Multiple)</label>
                <select class="form-select @error('kategori_ids') is-invalid @enderror" id="kategori_ids" name="kategori_ids[]" multiple required>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ in_array($kategori->id, old('kategori_ids', [])) ? 'selected' : '' }}>
                            {{ $kategori->nama }}
                        </option>
                    @endforeach
                </select>
                @error('kategori_ids')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar (Opsional)</label>
                <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar">
                @error('gambar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('produk.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
```
60. Membuat halaman produk resources/views/produks/edit.blade.php
```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Edit Produk</h1>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data" class="max-w-lg">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="nama" class="block text-gray-700 text-sm font-bold mb-2">Nama Produk</label>
            <input type="text" name="nama" id="nama"
                   value="{{ old('nama', $produk->nama) }}"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                   @error('nama') border-red-500 @enderror">
            @error('nama')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="harga" class="block text-gray-700 text-sm font-bold mb-2">Harga</label>
            <input type="number" name="harga" id="harga"
                   value="{{ old('harga', $produk->harga) }}"
                   min="0" step="0.01"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                   @error('harga') border-red-500 @enderror">
            @error('harga')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="gambar" class="block text-gray-700 text-sm font-bold mb-2">Gambar Produk</label>
            <input type="file" name="gambar" id="gambar"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                   @error('gambar') border-red-500 @enderror">
            @error('gambar')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror

            @if($produk->gambar)
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Gambar Saat Ini:</p>
                    <img src="{{ asset('storage/produks/' . $produk->gambar) }}"
                         alt="{{ $produk->nama }}"
                         class="mt-2 h-32 w-auto object-cover">
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="kategori_ids" class="block text-gray-700 text-sm font-bold mb-2">Kategori (Multiple)</label>
            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                   @error('kategori_ids') border-red-500 @enderror"
                    id="kategori_ids"
                    name="kategori_ids[]"
                    multiple
                    required>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->id }}"
                            {{ in_array($kategori->id, old('kategori_ids', $selectedKategoris)) ? 'selected' : '' }}>
                        {{ $kategori->nama }}
                    </option>
                @endforeach
            </select>
            @error('kategori_ids')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Produk
            </button>
            <a href="{{ route('produk.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                Kembali
            </a>
        </div>
    </form>
</div>
@endsection
```
61. Membuat halaman pembelian resources/views/pembelian/index.blade.php
```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Pembelian</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pembeli</th>
                <th>Total Harga</th>
                <th>Tanggal Pembelian</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembelian as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->nama_pembeli }}</td>
                <td>Rp. {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                <td>
                    <a href="{{ route('pembelian.detail', $item->id) }}" class="btn btn-info btn-sm">Detail</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
```
62. Membuat halaman pembelian resources/views/pembelian/detail.blade.php
```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail Pembelian</h1>
    <div class="card">
        <div class="card-header">
            <h3>Informasi Pembelian</h3>
        </div>
        <div class="card-body">
            <p><strong>Nama Pembeli:</strong> {{ $pembelian->nama_pembeli }}</p>
            <p><strong>Total Harga:</strong> Rp. {{ number_format($pembelian->total_harga, 0, ',', '.') }}</p>
            <p><strong>Metode Pembayaran:</strong> {{ $pembelian->metode_pembayaran }}</p>
            <p><strong>Status Pembayaran:</strong> {{ $pembelian->status_pembayaran }}</p>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3>Produk yang Dibeli</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembelian->detail as $index => $detail)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $detail->produk->nama }}</td>
                        <td>Rp. {{ number_format($detail->harga, 0, ',', '.') }}</td>
                        <td>{{ $detail->jumlah }}</td>
                        <td>Rp. {{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
```
63. Membuat halaman pos resources/views/pos/index.blade.php
```blade
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Kategori dan Produk -->
        <div class="col-md-8">
            <div class="row">
                <!-- Kategori -->
                <div class="col-md-3">
                    <div class="list-group" id="kategori-list">
                    <a href="#" class="list-group-item list-group-item-action kategori-item active"
                           data-kategori-id="all">

                            Semua Kategori
                        </a>
                        @foreach($kategoris as $kategori)
                        <a href="#" class="list-group-item list-group-item-action kategori-item"
                           data-kategori-id="{{ $kategori->id }}">
                            {{ $kategori->nama }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Produk -->
                <div class="col-md-9">
                    <div class="row" id="produk-list">
                        @foreach($produks as $produk)
                        <div class="col-md-4 mb-3 produk-item"
                             data-kategori-ids="{{ $produk->kategoris->pluck('id')->implode(',') }}">
                            <div class="card">
                                <img src="{{ asset('storage/produks/' . $produk->gambar) }}" class="card-img-top" alt="{{ $produk->nama }}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $produk->nama }}</h5>
                                    <p class="card-text">Rp. {{ number_format($produk->harga, 0, ',', '.') }}</p>
                                    <button class="btn btn-primary tambah-produk"
                                            data-produk-id="{{ $produk->id }}"
                                            data-produk-nama="{{ $produk->nama }}"
                                            data-produk-harga="{{ $produk->harga }}">
                                        Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Keranjang Belanja -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Keranjang Belanja</div>
                <div class="card-body">
                    <form id="form-pembayaran">
                        <div class="mb-3">
                            <label>Nama Pembeli</label>
                            <input type="text" class="form-control" name="nama_pembeli" required>
                        </div>

                        <table class="table" id="keranjang-list">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="keranjang-body"></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">Total</td>
                                    <td id="total-harga">0</td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="mb-3">
                            <label>Metode Pembayaran</label>
                            <select class="form-control" name="metode_pembayaran" required>
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Status Pembayaran</label>
                            <select class="form-control" name="status_pembayaran" required>
                                <option value="lunas">Lunas</option>
                                <option value="belum_lunas">Belum Lunas</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success btn-block">Bayar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cetak Struk -->
<div class="modal fade" id="struk-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Struk Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="struk-content"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="cetakStruk()">Cetak Struk</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let keranjang = [];
    let totalHarga = 0;

    // Filter produk berdasarkan kategori
   $('.kategori-item').on('click', function() {
        $('.kategori-item').removeClass('active');
        $(this).addClass('active');

        let kategoriId = $(this).data('kategori-id');

        if (kategoriId === "all") {
            $('.produk-item').show();
        } else {
            $('.produk-item').hide();
            $(`.produk-item[data-kategori-ids*="${kategoriId}"]`).show();
        }
    });


    // Tambah produk ke keranjang
     $(document).on('click', '.tambah-produk', function() {
        let produkId = $(this).data('produk-id');
        let produkNama = $(this).data('produk-nama');
        let produkHarga = $(this).data('produk-harga');

        let existingItem = keranjang.find(item => item.produk_id === produkId);
        if (existingItem) {
            existingItem.jumlah++;
        } else {
            keranjang.push({
                produk_id: produkId,
                nama: produkNama,
                harga: produkHarga,
                jumlah: 1
            });
        }

        updateKeranjang();
    });

     $(document).on('click', '.btn-plus', function() {
        let produkId = $(this).data('produk-id');
        let item = keranjang.find(item => item.produk_id === produkId);
        if (item) {
            item.jumlah++;
            updateKeranjang();
        }
    });

    // Kurangi jumlah produk di keranjang
    $(document).on('click', '.btn-minus', function() {
        let produkId = $(this).data('produk-id');
        let item = keranjang.find(item => item.produk_id === produkId);
        if (item && item.jumlah > 1) {
            item.jumlah--;
        } else {
            keranjang = keranjang.filter(item => item.produk_id !== produkId);
        }
        updateKeranjang();
    });

     $(document).on('input', '.input-jumlah', function() {
        let produkId = $(this).data('produk-id');
        let item = keranjang.find(item => item.produk_id === produkId);
        let jumlahBaru = parseInt($(this).val());

        if (item) {
            item.jumlah = jumlahBaru > 0 ? jumlahBaru : 1;
            updateKeranjang();
        }
    });

    // Hapus produk dari keranjang
    $(document).on('click', '.hapus-produk', function() {
        let produkId = $(this).data('produk-id');
        keranjang = keranjang.filter(item => item.produk_id !== produkId);
        updateKeranjang();
    });

    // Update keranjang
       function updateKeranjang() {
        let keranjangBody = $('#keranjang-body');
        keranjangBody.empty();
        totalHarga = 0;

        keranjang.forEach(item => {
            let subtotal = item.harga * item.jumlah;
            totalHarga += subtotal;

            keranjangBody.append(`
                <tr>
                    <td>${item.nama}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-secondary btn-minus" data-produk-id="${item.produk_id}">-</button>
                        <input type="number" class="form-control d-inline input-jumlah" style="width: 50px; text-align: center;"
                            value="${item.jumlah}" min="1" data-produk-id="${item.produk_id}">
                        <button class="btn btn-sm btn-outline-secondary btn-plus" data-produk-id="${item.produk_id}">+</button>
                    </td>
                    <td>Rp. ${subtotal.toLocaleString('id-ID')}</td>
                    <td>
                        <button class="btn btn-sm btn-danger hapus-produk" data-produk-id="${item.produk_id}">
                            Hapus
                        </button>
                    </td>
                </tr>
            `);
        });

        $('#total-harga').text("Rp. " + totalHarga.toLocaleString('id-ID'));
    }


    // Proses pembayaran
   $('#form-pembayaran').on('submit', function(e) {
    e.preventDefault();

    let produkIds = keranjang.map(item => item.produk_id);
    let jumlahProduk = keranjang.map(item => item.jumlah);

    let formData = {
        _token: $('meta[name="csrf-token"]').attr('content'), // Ambil CSRF token
        nama_pembeli: $('input[name="nama_pembeli"]').val(),
        metode_pembayaran: $('select[name="metode_pembayaran"]').val(),
        status_pembayaran: $('select[name="status_pembayaran"]').val(),
        produk: produkIds,  // Pastikan ini berbentuk array
        jumlah: jumlahProduk,  // Pastikan ini berbentuk array
        total_harga: totalHarga
    };

    $.ajax({
        url: '{{ route("pos.proses-pembayaran") }}',
        method: 'POST',
        contentType: "application/json",
        data: JSON.stringify(formData), // Konversi ke JSON
        success: function(response) {
            tampilkanStruk(response.pembelian_id);
        },
        error: function(xhr) {
            alert('Gagal memproses pembayaran');
        }
    });
});

   // Modifikasi fungsi tampilkanStruk
function tampilkanStruk(pembelianId) {
    $.get(`/pos/cetak-struk/${pembelianId}`, function(html) {
        // Reset keranjang
        keranjang = [];
        updateKeranjang();

        // Tampilkan modal struk
        $('#struk-content').html(html);
        $('#struk-modal').modal('show');
    });
}

});
function cetakStruk() {
    let printContents = document.getElementById('struk-content').innerHTML;
    let originalContents = document.body.innerHTML;

    let printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.open();
    printWindow.document.write(`
        <html>
        <head>
            <title>Cetak Struk</title>
            <style>
                body { font-family: Arial, sans-serif; }
                table { width: 100%; border-collapse: collapse; }
                td, th { border: 1px solid #000; padding: 8px; text-align: left; }
            </style>
        </head>
        <body>
            ${printContents}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
    printWindow.close();
}
</script>
@endsection
```
64. Membuat halaman pos resources/views/pos/struk.blade.php
```blade
<div class="text-center">
    <h4>STRUK PEMBAYARAN</h4>
    <hr>
    <div class="row">
        <div class="col-6 text-start">Nama Pembeli:</div>
        <div class="col-6 text-end">{{ $pembelian->nama_pembeli }}</div>
    </div>
    <div class="row">
        <div class="col-6 text-start">Tanggal:</div>
        <div class="col-6 text-end">{{ $pembelian->created_at->format('d M Y H:i') }}</div>
    </div>
    <hr>
    <table class="table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($pembelian->detail as $detail)
            <tr>
                <td>{{ $detail->produk->nama }}</td>
                <td>{{ $detail->jumlah }}</td>
                <td>Rp. {{ number_format($detail->harga, 0, ',', '.') }}</td>
                <td>Rp. {{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                @php $total += $detail->harga * $detail->jumlah @endphp
            </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <div class="row">
        <div class="col-6 text-start">Total Harga:</div>
        <div class="col-6 text-end">Rp. {{ number_format($total, 0, ',', '.') }}</div>
    </div>
    <div class="row">
        <div class="col-6 text-start">Metode Pembayaran:</div>
        <div class="col-6 text-end">{{ ucfirst($pembelian->metode_pembayaran) }}</div>
    </div>
    <div class="row">
        <div class="col-6 text-start">Status Pembayaran:</div>
        <div class="col-6 text-end">{{ ucfirst($pembelian->status_pembayaran) }}</div>
    </div>
    <hr>
    <p class="text-center">Terima Kasih</p>
</div>
```
65. Edit routes/web.php
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;
use App\Http\Middleware\CheckRole;

// Route utama
Route::get('/', [AuthController::class, 'index'])->name('index');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth'])->group(function() {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route untuk Admin
    Route::middleware([CheckRole::class . ':admin'])->group(function() {
        Route::get('/pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
        Route::get('/pembelian/{id}', [PembelianController::class, 'detail'])->name('pembelian.detail');
    });

    // Route untuk Kasir dan Admin
    Route::middleware([CheckRole::class . ':kasir,admin'])->group(function() {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class);
        Route::resource('kategori', KategoriController::class);
        Route::resource('produk', ProdukController::class);

        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        Route::get('/pos/produk-kategori/{kategori_id}', [POSController::class, 'getProdukByKategori']);
        Route::post('/pos/proses-pembayaran', [POSController::class, 'prosesPembayaran'])->name('pos.proses-pembayaran');
        Route::get('/pos/cetak-struk/{pembelianId}', [POSController::class, 'cetakStruk']);
    });
});
```
66. Storage link
```bash
php artisan storage:link
```
67. Jalankan Aplikasi
```bash
php artisan serve --host=127.0.0.1
```
68. Buka Browser
```bash
http://127.0.0.1:8000
```
69. Login
```bash
-admin
email: admin@pos.test
password: password
-kasir
email: kasir@pos.test
password: password
```


