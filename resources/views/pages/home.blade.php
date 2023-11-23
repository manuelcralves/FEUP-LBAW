@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="content-container">
        <div class="auctions-container">
            <h1>Top Auctions</h1>
            @foreach ($topAuctions as $auction)
                <div class="auction-item">
                    <h2>{{ $auction->title }}</h2>
                    <p>Current Price: ${{ $auction->current_price }}</p>
                </div>
            @endforeach
        </div>

        <div class="bidders-container">
            <h1>Top Bidders</h1>
            @foreach ($topBidders as $bidder)
                <div class="bidder-item">
                    <p>Bidder: {{ $bidder->authenticatedUser->username }}</p>
                    <p>Total Bids: {{ $bidder->total_bids }}</p>
                    <p>Total Bid Amount: ${{ $bidder->total_bid_amount }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
