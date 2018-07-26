@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">You can see product table!</div>
                <div class="card-body">
                    <table border="1" width="100%" cellpadding="5">
                        <tr><th>id</th><th>name</th><th>category</th></tr>
                        @forelse($products as $product)
                            <tr>
                                <td>{{$product->id}}</td>
                                <td>{{$product->name}}</td>
                                <td>
                                    @foreach ($product->categories as $category)
                                        {{ $category->name }}
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <p>Table empty</p>
                        @endforelse
                    </table>
                </br> </br> </br>
                <form action="product" method="POST">
                    <input type="text" name="id" value="id">
                    <input type="text" name="category_id" value="category_id">
                    <input type="text" name="product" value="product">
                    </br>
                    <input type="submit" name='type' value="ADD">
                    <input type="submit" name='type' value="EDIT">
                    <input type="submit" name='type' value="DELETE">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
