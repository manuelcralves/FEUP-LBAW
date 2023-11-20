@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h1>User Information</h1>
    @if(Auth::check())
        <p><strong>Name:</strong> {{ Auth::user()->username }}</p>
        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
        <p><strong>First Name:</strong> {{ Auth::user()->first_name }}</p>
        <p><strong>Last Name:</strong> {{ Auth::user()->last_name }}</p>
        <p><strong>Balance:</strong> {{ Auth::user()->balance }}€</p>
    @else
        <p>User is not logged in.</p>
    @endif
@endsection
