@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('update', ['id' => $user->id]) }}">
    @csrf
    @method('POST')

    <label for="username">Username</label>
    <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}" required autofocus>
    @error('username')
    <span class="error">{{ $message }}</span>
    @enderror

    <label for="first_name">First Name</label>
    <input id="first_name" type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
    @error('first_name')
    <span class="error">{{ $message }}</span>
    @enderror

    <label for="last_name">Last Name</label>
    <input id="last_name" type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
    @error('last_name')
    <span class="error">{{ $message }}</span>
    @enderror

    <label for="email">E-Mail Address</label>
    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
    @error('email')
    <span class="error">{{ $message }}</span>
    @enderror

    <label for="password">New Password (optional)</label>
    <input id="password" type="password" name="password">
    @error('password')
    <span class="error">{{ $message }}</span>
    @enderror

    <label for="password_confirmation">Confirm New Password (optional)</label>
    <input id="password_confirmation" type="password" name="password_confirmation">

    <button type="submit">
        Update Profile
    </button>
    <a class="button button-outline" href="{{ route('show', ['id' => $user->id]) }}">Cancel</a>
</form>
@endsection
