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
