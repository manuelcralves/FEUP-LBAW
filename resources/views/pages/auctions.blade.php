@extends('layouts.app')

@section('title', 'Auctions')

@section('content')
    <h2 class="auctions-title">All Auctions</h2>

    <form method="GET" action="{{ route('auction.index', ['pageNr' => 1]) }}" id="search-form" class="auction-search-form">
        <input type="text" name="query" placeholder="Search auctions..." value="{{ $query }}" class="search-input">
        <button type="submit" class="search-button">Search</button>
    </form>

    @if ($auctions->isEmpty())
        <p class="no-auctions-message">No auctions found.</p>
    @else
        <div class="auctions-container">
            @foreach ($auctions as $auction)
                <div class="auction-card">
                    <a href="{{ route('auction.show', ['id' => $auction->id]) }}" class="auction-title">
                        {{ $auction->title }}
                    </a>
                    <div class="auction-details">
                        <p><strong>Title:</strong> {{ $auction->title }}</p>
                        <p><strong>Description:</strong> {{ $auction->description }}</p>
                        <p><strong>Current Price:</strong> {{ $auction->current_price }}</p>
                        <p><strong>Status:</strong> {{ $auction->status }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination">
            @if ($auctions->currentPage() > 1)
                <a href="{{ route('auction.index', ['pageNr' => $auctions->currentPage() - 1, 'query' => $query]) }}" class="prev">Previous</a>
            @endif

            @for ($i = 1; $i <= $auctions->lastPage(); $i++)
                <a href="{{ route('auction.index', ['pageNr' => $i, 'query' => $query]) }}" class="{{ $i == $auctions->currentPage() ? 'active' : '' }}">{{ $i }}</a>
            @endfor

            @if ($auctions->currentPage() < $auctions->lastPage())
                <a href="{{ route('auction.index', ['pageNr' => $auctions->currentPage() + 1, 'query' => $query]) }}" class="next">Next</a>
        @endif
            </div>
    @endif
@endsection
