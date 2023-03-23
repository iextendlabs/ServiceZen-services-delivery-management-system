@extends('layouts.main')
@section('content')
<div class="container">
  <h2>Book an Appointment</h2>
  <form action="" method="post">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="service">Select Service:</label>
          <select class="form-control" id="service" name="service">
            <option value="service1">Service 1</option>
            <option value="service2">Service 2</option>
            <option value="service3">Service 3</option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="staff">Select Staff:</label>
          <select class="form-control" id="staff" name="staff">
            <option value="staff1">Staff 1</option>
            <option value="staff2">Staff 2</option>
            <option value="staff3">Staff 3</option>
          </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="date">Select Date:</label>
          <input type="date" class="form-control" id="date" name="date">
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="time">Select Time:</label>
          <input type="time" class="form-control" id="time" name="time">
        </div>
      </div>
    </div>
    <div class="form-group">
      <label for="name">Your Name:</label>
      <input type="text" class="form-control" id="name" name="name">
    </div>
    <div class="form-group">
      <label for="email">Your Email:</label>
      <input type="email" class="form-control" id="email" name="email">
    </div>
    <div class="form-group">
      <label for="phone">Your Phone:</label>
      <input type="tel" class="form-control" id="phone" name="phone">
    </div>
    <div class="form-group">
      <label for="notes">Notes:</label>
      <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
</div>
@endsection