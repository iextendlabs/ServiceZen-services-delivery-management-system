@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center">Backups</h1>
        
        <div class="mb-3 float-right">
            <a href="{{ route('backups.backup') }}" class="btn btn-primary">Create Backup</a>
        </div>

        <table class="table-striped table-bordered table">
            <thead>
                <tr>
                    <th>Backup File</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($backups as $backup)
                    <tr>
                        <td>{{ $backup }}</td>
                        <td><a href="{{ route('backups.download', ['filename' => $backup]) }}" class="btn btn-success">Download</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection