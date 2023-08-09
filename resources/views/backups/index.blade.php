@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center">Backups</h1>
        
        <div class="mb-3 float-right">
            <a href="{{ route('backups.backup') }}" class="btn btn-primary">Create Backup</a> 
            <a href="{{ route('backups.clear') }}" class="btn btn-danger">Clear Backups</a>
        </div><br><br>
        
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-right" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @php
            $i = 0;
        @endphp

        <table class="table-striped table-bordered table">
            <thead>
                <tr>
                    <th class="text-right">SR#</th>
                    <th class="text-left">Backup File</th>
                    <th class="text-right">Last Modified</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($backups as $backup)
                    <tr>
                        <td class="text-right">{{ ++$i }}</td>
                        <td class="text-left">{{ basename($backup['file']) }}</td>
                        <td class="text-right">{{ $backup['modified'] }}</td>
                        <td class="text-right">
                            <a href="{{ route('backups.download', ['filename' => basename($backup['file'])]) }}" class="btn btn-success"><i class="fas fa-download"></i></a> 
                            <a href="{{ route('backups.delete', ['filename' => basename($backup['file'])]) }}" class="btn btn-danger"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection