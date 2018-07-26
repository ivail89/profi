@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">You can see categories table!</div>
                    <table border="1" width="100%" cellpadding="5">
                        <tr><th>id</th><th>name</th></tr>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{$category->id}}</td>
                                <td>{{$category->name}}</td>
                            </tr>
                        @empty
                            <p>Table empty</p>
                        @endforelse
                    </table>
                </div>
                </br> </br> </br>
                <form action="categories" method="POST">
                    <input type="text" name="id" value="id">
                    <input type="text" name="category" value="category">
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
@endsection
