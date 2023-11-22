@extends('layouts.app')

@section('title', 'General Search')

@section('content')
    <section>
        <h2>Auctions</h2>
        @if ($auctions->count() > 0)
            <ul>
                @foreach ($auctions as $auction)
                    <li><a href="{{ route('auction.show', ['id' => $auction->id]) }}">{{ $auction->title }}</a></li>
                @endforeach
            </ul>
        @else
            <p>No auctions found.</p>
        @endif
    </section>

    <section>
        <h2>Users</h2>
        @if ($users->count() > 0)
            <ul>
                @foreach ($users as $user)
                    <li><a href="{{ route('show', ['id' => $user->id]) }}">{{ $user->username }}</a></li>
                @endforeach
            </ul>
        @else
            <p>No users found.</p>
        @endif
    </section>
@endsection
