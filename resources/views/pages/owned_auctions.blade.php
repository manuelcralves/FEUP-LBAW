@extends('layouts.app')

@section('title', 'Your Owned Auctions')

@section('content')
    <h2>Your Owned Auctions</h2>

    @if ($ownedAuctions->isEmpty())
        <p>No auctions</p>
    @else
        <ul>
            @foreach ($ownedAuctions as $auction)
                <li>
                    <a href="{{ route('auction.show', ['id' => $auction->id]) }}">
                        {{ $auction->title }}
                    </a>
                    <br>
                    <strong>Title:</strong> {{ $auction->title }}<br>
                    <strong>Description:</strong> {{ $auction->description }}<br>
                    <strong>Current Price:</strong> {{ $auction->current_price }}<br>
                    <strong>Status:</strong> {{ $auction->status }}<br>
                </li>
            @endforeach
        </ul>

        <div class="pagination">
        @if ($ownedAuctions->currentPage() > 1)
            <a href="{{ route('owned.auctions', ['id' => Auth::id(), 'pageNr' => $ownedAuctions->currentPage() - 1]) }}" class="prev">Previous</a>
        @endif

        @for ($i = 1; $i <= $ownedAuctions->lastPage(); $i++)
            <a href="{{ route('owned.auctions', ['id' => Auth::id(), 'pageNr' => $i]) }}" class="{{ $i == $ownedAuctions->currentPage() ? 'active' : '' }}">{{ $i }}</a>
        @endfor

        @if ($ownedAuctions->currentPage() < $ownedAuctions->lastPage())
            <a href="{{ route('owned.auctions', ['id' => Auth::id(), 'pageNr' => $ownedAuctions->currentPage() + 1]) }}" class="next">Next</a>
        @endif
        </div>
    @endif
@endsection
