@extends('templates.cart.success')

@section('content')
<div class="container">
    <h2>Meus Pedidos</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Número do Pedido</th>
                <th>Data</th>
                <th>Status</th>
                <th>Total</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedidos as $pedido)
            <tr>
                <td>{{ $pedido['id'] }}</td>
                <td>{{ $pedido['created_at'] }}</td>
                <td>{{ $pedido['status'] }}</td>
                <td>R$ {{ number_format($pedido['total_amount'], 2, ',', '.') }}</td>
                <td>
                    <a href="/{{ $storeSlug }}/pedido/{{ $pedido['id'] }}" class="btn btn-info btn-sm">Ver</a>
                    <a href="/{{ $storeSlug }}/repetir/{{ $pedido['id'] }}" class="btn btn-warning btn-sm">Repetir</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
