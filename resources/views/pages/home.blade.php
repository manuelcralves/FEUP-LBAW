@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h1>User Information</h1>
    @if(Auth::check())
        <p><strong>Name:</strong> {{ Auth::user()->username }}</p>
        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
        <p><strong>First Name:</strong> {{ Auth::user()->first_name }}</p>
        <p><strong>Last Name:</strong> {{ Auth::user()->last_name }}</p>
        <h2>Addresses</h2>
        @foreach (Auth::user()->addresses as $address)
            <div>
                <p>Street: {{ $address->street }}</p>
                <p>Postal Code: {{ $address->postal_code }}</p>
                <p>City: {{ $address->city }}</p>
                <p>Country: {{ $address->country }}</p>
            </div>
        @endforeach
    @else
        <p>User is not logged in.</p>
    @endif
@endsection
