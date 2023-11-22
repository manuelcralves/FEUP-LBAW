@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h1>Welcome to StyleSwap!</h1>
    @if(Auth::check())
        <p>LOGED</p>
    @else
        <p>User is not logged in.</p>
    @endif
@endsection
