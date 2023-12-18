@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (isset($customError))
        <div class="alert alert-danger">
            {{ $customError }}
        </div>
    @endif
    
    <div class="profile-container">
        <h1 class="profile-header">User Information</h1>
        
        <div class="user-info">
            <div class="profile-picture">
                @if ($user->picture == 'default.jpg')
                    <img src="{{ asset('storage/pictures/default.jpg') }}" alt="Default Profile Picture" class="user-profile-image">
                @else
                    <img src="{{ asset('storage/' . $user->picture) }}" alt="{{ $user->username }}'s Profile Picture" class="user-profile-image">
                @endif
            </div>
            <p><strong>Name:</strong> {{ $user->username }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>First Name:</strong> {{ $user->first_name }}</p>
            <p><strong>Last Name:</strong> {{ $user->last_name }}</p>
            @if(Auth::user()->role != 'ADMIN' && Auth::user()->id == $user->id)
                <p><strong>Balance:</strong> {{ $user->balance }}€</p>
            @endif
        </div>

        @if(Auth::check() && Auth::user()->id == $user->id && Auth::user()->role != 'ADMIN')
            <h2 class="addresses-header">{{ $user->first_name }} {{ $user->last_name }} Addresses</h2>
            <div class="addresses-container">
                @foreach ($user->addresses as $address)
                    <div class="address-item">
                        <p>Street: {{ $address->street }}</p>
                        <p>Postal Code: {{ $address->postal_code }}</p>
                        <p>City: {{ $address->city }}</p>
                        <p>Country: {{ $address->country }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="action-buttons">
            @if(Auth::user()->role === 'ADMIN' && Auth::user()->id != $user->id && $user->role !== 'ADMIN')
            <a href="{{ route('edit', ['id' => $user->id]) }}" class="button edit-button">Edit User</a>
            @if ($user->is_blocked)
                <form method="POST" action="{{ route('unblock.user', ['id' => $user->id]) }}" class="admin-form">
                    @csrf
                    <button type="submit" class="button unblock-button">Unblock User</button>
                </form>
            @else
                <form method="POST" action="{{ route('block.user', ['id' => $user->id]) }}" class="admin-form">
                    @csrf
                    <button type="submit" class="button block-button">Block User</button>
                </form>
            @endif
        @endif

            @if(Auth::user()->id == $user->id)
                <a href="{{ route('edit', ['id' => Auth::user()->id]) }}" class="button edit-profile">Edit Profile</a>
                @if(Auth::user()->role != 'ADMIN')
                    <a href="{{ route('balance', ['id' => Auth::user()->id]) }}" class="button add-funds">Add Funds</a>
                    <a href="{{ route('owned.auctions', ['id' => Auth::user()->id, 'pageNr' => 1]) }}" class="button my-auctions">My Auctions</a>
                    <a href="{{ route('myBids', ['id' => Auth::user()->id, 'pageNr' => 1]) }}" class="button my-bids">My Bids</a>
                @endif
                <a href="{{ route('show.users', ['pageNr' => 1]) }}" class="button back-users">Back to Users Page</a>
                <a href="{{ url('/home') }}" class="button back-home">Back to Home Page</a>
            @elseif(Auth::check())
                <a href="{{ route('show.users', ['pageNr' => 1]) }}" class="button back-users">Back to Users Page</a>
                <a href="{{ url('/home') }}" class="button back-home">Back to Home Page</a>
            @endif
        </div>
    </div>
@endsection
