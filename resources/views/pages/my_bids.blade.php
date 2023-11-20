@extends('layouts.app')

@section('title', 'My Bids')

@section('content')
    <h2>My Bids</h2>

    @if ($bids->isEmpty())
        <p>No bids found.</p>
    @else
        <ul>
            @foreach ($bids as $bid)
                <li>
                    <strong>Auction Title:</strong> {{ $bid->auctions->title }}<br>
                    <strong>Auction ID:</strong> {{ $bid->auction }}<br>
                    <strong>Value:</strong> {{ $bid->value }}€<br>
                    <strong>Creation Date:</strong> {{ $bid->creation_date }}<br>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
