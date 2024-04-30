@extends('layouts.app') @section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Edit Affiliate</h2>
                </div>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br /><br />
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('affiliates.update', $affiliate->id) }}" method="POST">
            <input type="hidden" value="{{ $affiliate->affiliate->id ?? '' }}" name="affiliate_id" />
            <input type="hidden" value="{{ $affiliate_join }}" name="affiliate_join" />
            <input type="hidden" name="url" value="{{ url()->previous() }}" />
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red">*</span><strong>Name:</strong>
                        <input type="text" name="name" value="{{ $affiliate->name }}" class="form-control"
                            placeholder="Name" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red">*</span><strong>Email:</strong>
                        <input type="email" name="email" value="{{ $affiliate->email }}" class="form-control"
                            placeholder="abc@gmail.com" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Password:</strong>
                        <input type="password" name="password" class="form-control" placeholder="Password" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Confirm Password:</strong>
                        <input type="password" name="confirm-password" class="form-control"
                            placeholder="Confirm Password" />
                    </div>
                </div>
                <hr>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Phone Number:</strong>
                        <input id="number_country_code" type="hidden" name="number_country_code" />
                        <input type="tel" id="number" name="number" value="{{ $affiliate->affiliate->number ?? '' }}"
                            class="form-control">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Whatsapp Number:</strong>
                        <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                        <input type="tel" id="whatsapp" name="whatsapp"
                            value="{{ $affiliate->affiliate->whatsapp ?? '' }}" class="form-control">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red">*</span><strong>Code:</strong>
                        <input type="text" name="code" value="{{ $affiliate->affiliate->code ?? '' }}"
                            class="form-control" placeholder="Code" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red">*</span><strong>Commission:</strong>
                        <input type="number" name="commission" value="{{ $affiliate->affiliate->commission ?? '' }}"
                            class="form-control" placeholder="Commission In %" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Expire after days:</strong>
                        <input type="number" name="expire" class="form-control"
                            value="{{ $affiliate->affiliate->expire ?? '' }}" placeholder="Enter days like 20" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Fix Salary:</strong>
                        <input type="number" name="fix_salary" value="{{ $affiliate->affiliate->fix_salary ?? '' }}"
                            class="form-control" placeholder="Fix Salary" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Parent Affiliate:</strong>
                        <select name="parent_affiliate_id" class="form-control">
                            <option value=""></option>
                            @foreach ($affiliates as $single_affiliate)
                                @if ($single_affiliate->id !== $affiliate->id)
                                    <option value="{{ $single_affiliate->id }}"
                                        @if ($affiliate->affiliate && $affiliate->affiliate->parent_affiliate_id == $single_affiliate->id) selected @endif> {{ $single_affiliate->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red">*</span><strong>Customer Display:</strong>
                        <select name="display_type" id="display_type" class="form-control">
                            <option value="1" @if ($affiliate->affiliate && $affiliate->affiliate->display_type == 1) selected @endif>Enable
                            </option>
                            <option value="0" @if ($affiliate->affiliate && $affiliate->affiliate->display_type == 0) selected @endif>Disable
                            </option>
                            <option value="2" @if ($affiliate->affiliate && $affiliate->affiliate->display_type == 2) selected @endif>Selected Customer
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12" style="display: none" id="customer">
                    @if (count($affiliateUser) > 0)
                        <div class="form-group @if (count($affiliateUser) > 6) scroll-div @endif">
                            <span style="color: red">*</span><strong>Select Customer To Display:</strong>
                            <input type="text" name="customer-search" id="customer-search" class="form-control"
                                placeholder="Search Customer By Name or Email" />
                            <table class="table table-striped table-bordered customer-table">
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                                @foreach ($affiliateUser as $user)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="customer_checkbox"
                                                @if ($user->display == '1') checked @endif name="customerId[]"
                                                value="{{ $user->user_id }}" />
                                        </td>
                                        <td>{{ $user->customer->name }}</td>
                                        <td>{{ $user->customer->email }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        <div class="form-group">
                            <span style="color: red">*</span><strong>Selected Customer:</strong>
                            <table class="table table-striped table-bordered selected-customer-table">
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                                @if ($affiliateUser->where('display', 1)->count() > 0)
                                    @foreach ($affiliateUser->where('display', 1) as $user)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="selected_customer_checkbox" checked
                                                    name="selectedCustomerId[]" value="{{ $user->user_id }}" />
                                            </td>
                                            <td>{{ $user->customer->name }}</td>
                                            <td>{{ $user->customer->email }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </table>
                        </div>
                    @else
                        <div class="text-center">
                            <h4>There is no customer</h4>
                        </div>
                    @endif
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function() {
            if ($('.selected-customer-table tr').length > 6) {
                $('.selected-customer-table').parent().addClass('scroll-div');
            }
            $(document).on('change', '.customer_checkbox', function() {
                if ($('.selected-customer-table tr').length > 6) {
                    $('.selected-customer-table').parent().addClass('scroll-div');
                }
                var row = $(this).closest('tr').clone();
                if ($(this).is(':checked')) {
                    row.find('.customer_checkbox')
                        .removeClass('customer_checkbox')
                        .addClass('selected_customer_checkbox')
                        .attr('name', function(i, name) {
                            return name.replace(/^customerId/, 'selectedCustomerId');
                        });
                    $('.selected-customer-table').append(row);
                } else {
                    var userId = $(this).val();
                    $('.selected_customer_checkbox[value="' + userId + '"]').closest('tr').remove();
                }
            });

            // Handler for selected customers table checkboxes
            $(document).on('change', '.selected_customer_checkbox', function() {
                if ($('.selected-customer-table tr').length > 6) {
                    $('.selected-customer-table').parent().addClass('scroll-div');
                }
                var userId = $(this).val();
                if (!$(this).is(':checked')) {
                    // Remove from selected customers table
                    $(this).closest('tr').remove();
                    // Uncheck in customer table
                    $('.customer_checkbox[value="' + userId + '"]').prop('checked', false);
                }
            });
        });
    </script>

    <script>
        $("#display_type").on("change", function() {
            var selectedValue = $(this).val();
            if (selectedValue == 2) {
                $("#customer").show();
            } else {
                $("#customer").hide();
            }
        });
        $(document).ready(function() {
            var selectedValue = $("#display_type").val();
            if (selectedValue == 2) {
                $("#customer").show();
            } else {
                $("#customer").hide();
            }
            $("#customer-search").keyup(function() {
                var value = $(this).val().toLowerCase();

                $(".customer-table tr").hide();

                $(".customer-table tr").each(function() {
                    $row = $(this);

                    var name = $row.find("td:first").next().text().toLowerCase();

                    var email = $row.find("td:last").text().toLowerCase();

                    if (name.indexOf(value) != -1) {
                        $(this).show();
                    } else if (email.indexOf(value) != -1) {
                        $(this).show();
                    }
                });
            });
        });
    </script>
@endsection
