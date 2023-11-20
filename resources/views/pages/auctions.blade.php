@extends('layouts.app')

@section('title', 'Auctions')

@section('content')
    <h2>All Auctions</h2>

    <ul>
        @foreach ($auctions as $auction)
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
    @if ($auctions->currentPage() > 1)
        <a href="{{ route('auction.index', ['pageNr' => $auctions->currentPage() - 1]) }}" class="prev">Previous</a>
    @endif

    @for ($i = 1; $i <= $auctions->lastPage(); $i++)
        <a href="{{ route('auction.index', ['pageNr' => $i]) }}" class="{{ $i == $auctions->currentPage() ? 'active' : '' }}">{{ $i }}</a>
    @endfor

    @if ($auctions->currentPage() < $auctions->lastPage())
        <a href="{{ route('auction.index', ['pageNr' => $auctions->currentPage() + 1]) }}" class="next">Next</a>
   @endif
    </div>
@endsection
