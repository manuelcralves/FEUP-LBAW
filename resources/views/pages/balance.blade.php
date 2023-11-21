@extends('layouts.app')

@section('title', 'Add Funds')

@section('content')
    <h2>Add Funds to Your Balance</h2>

    <form method="POST" action="{{ route('balance', ['id' => $user->id]) }}">
        @csrf

        <label for="amount">Amount to Add:</label>
        <input id="amount" type="number" step="0.5" name="amount" min="1" required>
        @error('amount')
        <span class="error">{{ $message }}</span>
        @enderror

        <button type="submit">Add Funds</button>
    </form>
@endsection
