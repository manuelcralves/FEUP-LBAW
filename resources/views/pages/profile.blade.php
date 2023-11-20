@extends('layouts.app')

@section('title', 'Profile')

@section('content')
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
    @else
        <p>User is not logged in.</p>
    @endif
@endsection
