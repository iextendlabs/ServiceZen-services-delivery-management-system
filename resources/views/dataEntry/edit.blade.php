@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Edit Data Entry User</h2>
                </div>
            </div>
        </div>
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
        <form action="{{ route('dataEntry.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="url" value="{{ url()->previous() }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Name:</strong>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control"
                            placeholder="Name">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Email:</strong>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control"
                            placeholder="abc@gmail.com">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Password:</strong>
                        <input type="password" name="password" class="form-control" placeholder="Password">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Confirm Password:</strong>
                        <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Status:</strong>
                        <select name="status" class="form-control">
                            <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}> Enable
                            </option>
                            <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}> Disable
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group scroll-div">
                        <strong>Category:</strong>
                        <input type="text" name="search-category" id="search-category" class="form-control"
                            placeholder="Search category By Name, Price And Duration">
                        <table class="table table-striped table-bordered category-table">
                            <tr>
                                <th></th>
                                <th>Title</th>
                            </tr>
                            <tr>
                                <td>

                                    <input type="checkbox" class="category-checkbox" name="category" value="all">
                                </td>
                                <td>All</td>
                            </tr>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>

                                        <input type="checkbox" class="category-checkbox" name="category_ids[]"
                                            value="{{ $category->id }}"
                                            {{ in_array($category->id, old('category_ids', $category_ids)) ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ $category->title }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <script>
        $('.category-checkbox').click(function() {
            var categoryId = $(this).val();

            if (categoryId === 'all') {
                var allCheckboxState = $(this).prop('checked');
                $('.category-checkbox').prop('checked', allCheckboxState);
            }
        });
    </script>
@endsection
