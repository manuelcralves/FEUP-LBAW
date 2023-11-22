@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <h1>User Information</h1>
        <p><strong>Name:</strong> {{ Auth::user()->username }}</p>
        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
        <p><strong>First Name:</strong> {{ Auth::user()->first_name }}</p>
        <p><strong>Last Name:</strong> {{ Auth::user()->last_name }}</p>
        <p><strong>Balance:</strong> {{ Auth::user()->balance }}€</p>
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

        <a href="{{ route('edit', ['id' => Auth::user()->id]) }}" class="button">Edit Profile</a>
        <a href="{{ route('balance', ['id' => Auth::user()->id]) }}" class="button">Add Funds</a>
        <a href="{{ route('owned.auctions', ['id' => Auth::user()->id, 'pageNr' => 1]) }}" class="button">My Auctions</a>
        <a href="{{ route('myBids', ['id' => Auth::user()->id, 'pageNr' => 1]) }}" class="button">My Bids</a>
    @endif
    
@endsection
