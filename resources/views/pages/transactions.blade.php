@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
<div class="container">
    <h1>Transactions</h1>
    <table class="table custom-table">
        <thead>
            <tr>
                <th>Value</th>
                <th>Transaction Date</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
            <tr>
                <td>{{ $transaction->value }}</td>
                <td>{{ $transaction->transaction_date }}</td>
                <td>{{ $transaction->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination">
        @if ($transactions->currentPage() > 1)
            <a href="{{ route('transactions.user', ['id' => Auth::id(), 'pageNr' => $transactions->currentPage() - 1]) }}" class="prev">Previous</a>
        @endif

        @for ($i = 1; $i <= $transactions->lastPage(); $i++)
            <a href="{{ route('transactions.user', ['id' => Auth::id(), 'pageNr' => $i]) }}" class="{{ $i == $transactions->currentPage() ? 'active' : '' }}">{{ $i }}</a>
        @endfor

        @if ($transactions->currentPage() < $transactions->lastPage())
            <a href="{{ route('transactions.user', ['id' => Auth::id(), 'pageNr' => $transactions->currentPage() + 1]) }}" class="next">Next</a>
        @endif
    </div>
</div>
@endsection
