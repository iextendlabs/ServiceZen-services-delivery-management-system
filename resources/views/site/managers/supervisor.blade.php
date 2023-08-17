@extends('site.layout.app')
@section('content')
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-12 py-5 text-center">
                <h2>Supervisors</h2>
            </div>
        </div>
        <div>
            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            @if(count($supervisors) != 0)
            <table class="table table-striped table-bordered album bg-light">
                <tr>
                    <th>Sr#</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
                @foreach($supervisors as $supervisor)
                @foreach($supervisor->supervisor as $user)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                </tr>
                @endforeach
                @endforeach

            </table>
            @else
            <div class="text-center">
                <h4>There is no Supervisor.</h4>
            </div>
            @endif
        </div>
    </div>
</div>
<script>
    function printDiv() {
        window.print();
    }
</script>
@endsection