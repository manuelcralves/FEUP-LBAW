@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <h1>User Information</h1>
        <p><strong>Name:</strong> {{ $user->username }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>First Name:</strong> {{ $user->first_name }}</p>
        <p><strong>Last Name:</strong> {{ $user->last_name }}</p>
        @if(Auth::user()->role != 'ADMIN')
        <p><strong>Balance:</strong> {{ $user->balance }}€</p>
        @endif
    @if(Auth::check())
        <h2>{{$user->first_name}} {{$user->last_name}} Addresses</h2>
        @foreach ($user->addresses as $address)
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
            @if(Auth::user()->role != 'ADMIN')
            <a href="{{ route('balance', ['id' => Auth::user()->id]) }}" class="button">Add Funds</a>
            <a href="{{ route('owned.auctions', ['id' => Auth::user()->id, 'pageNr' => 1]) }}" class="button">My Auctions</a>
            <a href="{{ route('myBids', ['id' => Auth::user()->id, 'pageNr' => 1]) }}" class="button">My Bids</a>
            @endif
            <a class="button" href="{{ url('/home') }}">Back to Home Page</a>
        @else
        <a class="button" href="{{ route('show.users', ['pageNr' => 1]) }}">Back to Users Page</a>
        @endif
    @endif
@endsection
