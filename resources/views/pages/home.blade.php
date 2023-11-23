@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="content-container">
        <div class="auctions-container">
            <h1>Top Auctions</h1>
            @foreach ($topAuctions as $auction)
                <div class="auction-item">
                    <h2><a class="buttonColor"href="{{ route('auction.show', ['id' => $auction->id]) }}">{{ $auction->title }}</a></h2>
                    <p>Current Price: ${{ $auction->current_price }}</p>
                </div>
            @endforeach
        </div>

        <div class="bidders-container">
            <h1>Top Bidders</h1>
            @foreach ($topBidders as $bidder)
                <div class="bidder-item">
                    <h2><a class="buttonColor"href="{{ route('show', ['id' => $bidder->authenticatedUser->id]) }}">{{ $bidder->authenticatedUser->username }}</a></h2>
                    <p>Total Bids: {{ $bidder->total_bids }}</p>
                    <p>Total Bid Amount: ${{ $bidder->total_bid_amount }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
