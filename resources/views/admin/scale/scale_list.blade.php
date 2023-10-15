@extends('admin.layouts.app')

@section('content')
  <link href="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.css" rel="stylesheet">
  <script src="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.js"></script>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">Scale Lists</h4>

      <div class="row">
        <!-- Basic Layout -->
        <div class="col-xxl">
          <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
            </div>
            <div class="card-body">
                <div class="col-md-12 bg-light mb-3" style="text-align: right;">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal">Add Scale</button>
                </div>
                
                <div class="table-responsive text-nowrap">
                  <table class="table table-bordered" id="scale_list">
                    <thead>
                      <tr>
                        <th>Name (en)</th>                  
                        <th>Name (bn)</th>                  
                        <th>Point</th>
                        <th style="text-align:center;">Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                          @foreach($scale as $row)
                            <tr>
                              <td>{{$row->scale_name}}</td>
                              <td>{{$row->scale_name_bn}}</td>
                              <td>{{$row->scale_point}}</td>
                              <td style="text-align:center;">
                                <div class="btn-group btn-group-xs btn-group-solid" >
                                  <button id="editField" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#basicModal" sid="{{$row->id}}" name="{{$row->scale_name}}" name_bn="{{$row->scale_name_bn}}" options="{{$row->scale_point}}">Edit</button>
                                  <a class="btn btn-danger deleteField" href="#modal-center"  sid="{{$row->id}}" data-toggle="modal"> Delete </a>
                                </div>
                              </td>
                            </tr>
                          @endforeach
                    </tbody>
                  </table>
                </div>
            </div>
          </div>
        </div>
      </div>


      <!-- Modal -->
      <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <form action="{{ url('/') }}/admin/storeScale" method="POST" class="form-horizontal">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Add Scale</h5>
                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col mb-3">
                    <label for="nameBasic" class="form-label">Name (EN)</label>
                    <input type="text" class="form-control" name="name" id="name_set" placeholder="Name of scale (en)" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                    <label for="nameBasic" class="form-label">Name (BN)</label>
                    <input type="text" class="form-control" name="name_bn" id="name_set_bn" placeholder="Name of scale (bn)" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                    <label for="nameBasic" class="form-label">Point</label>
                    <input type="text" class="form-control" name="scales" id="scales" placeholder="Point of Scale" required>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                  Close
                </button>
                <button type="submit" class="btn btn-primary submitForm">Save changes</button>
              </div>
            </div>
          </form>
        </div>
      </div>


  </div>
  <script type="text/javascript">
    $(document).ready(function() {
      
        new DataTable('#scale_list');

        $(document).on('click','#editField',function(e) {
          var option = $(this).attr('options');
          var id = $(this).attr('sid');
          var name = $(this).attr('name');
          var name_bn = $(this).attr('name_bn');
          $('.up').remove();
          console.log(name);
          $('#name_set').val(name);
          $('#name_set_bn').val(name_bn);
          $('#scales').val(option);
          $(".submitForm").after('<input type="hidden" class="up" name="sid" value="'+id+'" >');
        });
    });
  </script>
@endsection