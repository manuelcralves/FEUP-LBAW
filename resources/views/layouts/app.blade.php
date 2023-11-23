<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->
        <link href="{{ url('css/milligram.min.css') }}" rel="stylesheet">
        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        <script type="text/javascript">
            // Fix for Firefox autofocus CSS bug
            // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
        </script>
        <script type="text/javascript" src={{ url('js/app.js') }} defer>
        </script>
    </head>
    <body>
        <main>
            <header>
                <h1><a href="{{ url('/home') }}">StyleSwap!</a></h1>
                @if (!Auth::check())
                <a class="button" href="{{ url('/login') }}">Login</a>
                <a class="button" href="{{ route('auction.index', ['pageNr' => 1]) }}">Auctions</a>
                <a class="button" href="{{ route('show.users', ['pageNr' => 1]) }}">Users</a>
                <a class="button" href="{{ route('register', ['pageNr' => 1]) }}">Register</a>
                <form id="search-form" action="{{ route('search.results') }}" method="GET">
                    <input type="text" name="query" placeholder="Search...">
                    <button type="submit">Search</button>
                </form>
                @elseif(Auth::check())
                    <form id="search-form" action="{{ route('search.results') }}" method="GET">
                        <input type="text" name="query" placeholder="Search...">
                        <button type="submit">Search</button>
                    </form>
                    <a class="button" href="{{ route('show', ['id' => Auth::user()->id]) }}">My Profile</a>
                    <a class="button" href="{{ route('auction.index', ['pageNr' => 1]) }}">Auctions</a>
                    <a class="button" href="{{ route('show.users', ['pageNr' => 1]) }}">Users</a>
                    <a class="button" href="{{ url('/logout') }}">Logout</a>
                    <span>{{ Auth::user()->name }}</span>
                    @if(Auth::user()->role != 'ADMIN')
                    <a class="button" href="{{ route('auction.create') }}">Create Auction</a>
                    @endif
                    
                @endif
            </header>
            <section id="content">
                @yield('content')
            </section>
        </main>
    </body>
</html>