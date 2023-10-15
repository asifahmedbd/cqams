@extends('admin.layouts.app')

@section('content')
  <style>
    .badge { line-height: 1 !important;  }
  </style>
  <link href="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.css" rel="stylesheet">
  <script src="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.js"></script>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">Scale Set Lists</h4>

      <div class="row">
        <!-- Basic Layout -->
        <div class="col-xxl">
          <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
            </div>
            <div class="card-body">
                <div class="col-md-12 bg-light mb-3" style="text-align: right;">
                    <button type="button" class="btn btn-primary add-scale-set" data-bs-toggle="modal" data-bs-target="#basicModal">Add Scale Set</button>
                </div>
                
                <div class="table-responsive text-nowrap">
                  <table class="table table-bordered" id="scale_set_list">
                    <thead>
                      <tr>
                        <th>#</th>      
                        <th>Name</th>                 
                        <th>Scale</th>
                        <th style="text-align:center;">Action</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                          <?php $i=1; ?>
                          @foreach($scale as $row)
                            <tr>
                              <td>{{$i}}</td>
                              <td>{{$row->name}}</td>
                              <td>
                                @php $option = json_decode($row->options); @endphp
                                @foreach($scaleList as $scaleItem)
                                  @if(in_array($scaleItem->id, $option))
                                    <span class="badge bg-dark">{{$scaleItem->scale_name}}/ {{$scaleItem->scale_name_bn}}</span>
                                    
                                  @endif
                                @endforeach
                              </td>
                              <td style="text-align:center;">
                                <div class="btn-group btn-group-xs btn-group-solid" >
                                  <button id="editField" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#basicModal" sid="{{$row->id}}" name="{{$row->name}}" options="{{$row->options}}">Edit</button>
                                              

                                  <a class="btn btn-danger deleteField" href="#modal-center"  sid="{{$row->id}}" data-toggle="modal"> Delete </a>
                                </div>
                              </td>
                            </tr>
                            <?php $i++; ?>
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
        <div class="modal-dialog modal-xl" role="document">
          <form action="{{ url('/') }}/admin/storeScaleSet" method="POST" class="form-horizontal">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><span class="mtitle">Add</span> Scale Set</h5>
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
                    <input type="text" class="form-control" name="name" id="name_set" placeholder="Name of set" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                    <label for="nameBasic" class="form-label">Scales</label>
                    <div class="row">
                      @foreach($scaleList->chunk(5) as $scaleItem)
                        <div class="col-md-4" style="margin-bottom: 1.5rem;">
                          @foreach($scaleItem as $row=>$val)
                          <label><input type="checkbox" style="font-weight: 600;opacity:1!important;left: 0!important;position: inherit;" class="filled-in" name="scales[]" id="criteria" placeholder="Scale" value="{{$val->id}}"> {{$val->scale_name}} / {{$val->scale_name_bn}}</label><br>
                          @endforeach
                        </div>
                      @endforeach
                    </div>
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
        
        new DataTable('#scale_set_list');

        $(document).on('click','.add-scale-set',function(e) {
          $('#basicModal .modal-header .mtitle').text('Add');
        });

        $(document).on('click','#editField',function(e) {
          var option = JSON.parse($(this).attr('options'),true);
          var id = $(this).attr('sid');
          var name = $(this).attr('name');
          $('input:checkbox').removeAttr('checked');
          $('#name_set').val(name);
          $('#basicModal .modal-header .mtitle').text('Edit');
          for (i=0; i!=option.length;i++) {
            var checkbox = $("input[type='checkbox'][value='"+option[i]+"']");
            checkbox.attr("checked","checked");
          }
          $(".updateForm").after('<input type="hidden" class="up" name="sid" value="'+id+'" >');
        });
    });
  </script>
@endsection