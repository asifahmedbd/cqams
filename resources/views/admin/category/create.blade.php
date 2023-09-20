@extends('admin.layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Manage Categories</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Category Add</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Add New Category</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif

              <!-- @error('category_name')
                  <div class="alert alert-danger">{{ $message }}</div>
              @enderror
              @error('parent_id')
                  <div class="alert alert-danger">{{ $message }}</div>
              @enderror -->
            <form role="form" action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data">
	            	@csrf
              <div class="form-group">
                <label for="inputName">Category Name</label>
                <input type="text" id="inputName" class="form-control" name="category_name" required>
              </div>
              <div class="form-group">
                <label for="inputStatus">Category Level</label>
                <select name="parent_id" class = "form-control" required>
                    <option value="">Select</option>
                    <option value="0"> Main Category </option>
                    @foreach($all_categories as $row)
                        <option value="{{ $row->category_row_id}}">
                        <!-- @if($row->level == 0) <b>  @endif -->  
                        @if($row->level == 0) <b>  @endif 
                        @if($row->level == 1) &nbsp; - @endif   
                        @if($row->level == 2) &nbsp; &nbsp; - - @endif     
                        @if($row->level == 3) &nbsp; &nbsp; &nbsp; - - - @endif       
                        @if($row->level == 4) &nbsp; &nbsp; &nbsp; &nbsp; - - - - @endif       
                        @if($row->level == 5) &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  - - - - - @endif       
                        @if($row->level > 5)  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; - - - @endif
                        {{ $row->category_name }} 
                        @if($row->level == 0) </b>  @endif  
                        </option>
                      @endforeach
                  </select>
              </div>
              <div class="form-group">
                <label for="inputDescription">Category Description</label>
                <textarea id="inputDescription" class="form-control" rows="4" name="category_description"></textarea>
              </div>
              
              <div class="form-group">
                <label for="exampleInputFile">Category Image</label>
                <div class="input-group">
                  <div class="custom-file">
                    <input type="file" id="exampleInputFile"  class="custom-file-input"  name="category_image">
                    <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                  </div>
                  <div class="input-group-append">
                    <span class="input-group-text">Upload</span>
                  </div>
                </div>
              </div>

              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1" name="is_featured">
                <label class="form-check-label" for="exampleCheck1">Featured Category</label>
              </div>
            </div>
            <!-- /.card-body -->
            <div class="col-12" style="margin: 10px">
		          <a href="#" class="btn btn-secondary">Cancel</a>
		          <input type="submit" value="Create new category" class="btn btn-success float-right">
		        </div>
          </form>
          </div>
          <!-- /.card -->
        </div>

      </div>

    </section>
    <!-- /.content -->
  </div>

  <script type="text/javascript">
    $(document).ready(function(){
      $('.alert-danger').delay(3000).fadeOut('slow');
    });
  </script>
@endsection