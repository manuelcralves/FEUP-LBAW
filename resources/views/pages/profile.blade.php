@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(Auth::check())
        <h2>Your Addresses</h2>
        @foreach (Auth::user()->addresses as $address)
            <div>
                <p>Street: {{ $address->street }}</p>
                <p>Postal Code: {{ $address->postal_code }}</p>
                <p>City: {{ $address->city }}</p>
                <p>Country: {{ $address->country }}</p>
            </div>
        @endforeach
        @if(Auth::user()->role === 'ADMIN' && Auth::user()->id != $user->id && $user->role !== 'ADMIN')
            <a href="{{ route('edit', ['id' => $user->id]) }}" class="button">Edit User</a>
            <form method="POST" action="{{ route('promote.admin', ['id' => $user->id]) }}">
                @csrf
                <button type="submit" class="button">Promote to ADMIN</button>
            </form>
        @endif
        @if(Auth::user()->id == $user->id) <!-- Check if the user is on their own profile -->
            <a href="{{ route('edit', ['id' => Auth::user()->id]) }}" class="button">Edit Profile</a>
        @endif
        <a href="{{ route('balance', ['id' => Auth::user()->id]) }}" class="button">Add Funds</a>
        <a href="{{ route('owned.auctions', ['id' => Auth::user()->id, 'pageNr' => 1]) }}" class="button">My Auctions</a>
        <a href="{{ route('myBids', ['id' => Auth::user()->id, 'pageNr' => 1]) }}" class="button">My Bids</a>
    @else
        <p>User is not logged in.</p>
    @endif
@endsection
