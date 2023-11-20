@extends('layouts.app')

@section('title', 'Auction Details')

@section('content')
    <h2>Auction Details</h2>

    <p><strong>Title:</strong> {{ $auction->title }}</p>
    <p><strong>Current Price:</strong> {{ $auction->current_price }}</p>
    <p><strong>Status:</strong> {{ $auction->status }}</p>
    <p><strong>Description:</strong> {{ $auction->description }}<br></p>
    <a class="button" href="{{ route('auction.index', ['pageNr' => 1]) }}">Back to Auctions</a>
@endsection
