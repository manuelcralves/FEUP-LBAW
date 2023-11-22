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

    <div class="pagination">
        @if ($bids->currentPage() > 1)
            <a href="{{ route('myBids', ['id' => $id, 'pageNr' => $bids->currentPage() - 1]) }}" class="prev">Previous</a>
        @endif

        @for ($i = 1; $i <= $bids->lastPage(); $i++)
            <a href="{{ route('myBids', ['id' => $id, 'pageNr' => $i]) }}" class="{{ $i == $bids->currentPage() ? 'active' : '' }}">{{ $i }}</a>
        @endfor

        @if ($bids->currentPage() < $bids->lastPage())
            <a href="{{ route('myBids', ['id' => $id, 'pageNr' => $bids->currentPage() + 1]) }}" class="next">Next</a>
        @endif
    </div>
    <a href="{{ route('show', ['id' => Auth::user()->id]) }}" class="button">Back to profile</a>
@endsection
