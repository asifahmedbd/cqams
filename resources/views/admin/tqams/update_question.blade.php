<style>
    #UpdateModal .select2-container{
      width: 220px !important;
    }
  </style>  
<div class="row">
  <div class="col-md-3 mb-3">
    <label for="exampleFormControlSelect1" class="form-label">Category<span class="text-danger">*</span></label>
      <select class="form-select field_type" name="field_type" required>
        <option value="" disabled="disabled">Select Category</option>
        @foreach($category as $row=>$val)
          @php
            $selected = ($val->evalution_criteria_row_id == $evaluation_fields->evalution_criteria_row_id) ? 'selected' : '';
          @endphp
        <option value="{{$val->evalution_criteria_row_id}}" {{ $selected }}>{{$val->criteria_name_en}}</option>
        @endforeach             
      </select>
  </div>
  <div class="col-md-3 mb-3">
    <label for="exampleFormControlSelect1" class="form-label">Sub Category<span class="text-danger">*</span></label>
      <select  class="form-select criteria_type" name="criteria_type" required>
        <option value="" disabled="disabled">Select Sub-Category</option>
        @foreach($sub_category as $row=>$val)
          @php
            $selected = ($val->evalution_sub_criteria_row_id == $selected_sub_category->evalution_sub_criteria_row_id) ? 'selected' : '';
          @endphp
        <option value="{{$val->evalution_sub_criteria_row_id}}" {{ $selected }}>{{$val->criteria_name_en}}</option>
        @endforeach
      </select>
  </div>
  <div class="col-md-2 mb-3">
    <label for="exampleFormControlSelect1" class="form-label">Scale<span class="text-danger">*</span></label>
      <select  class="form-select" name="scale" required>
        @foreach($scale as $row=>$val)
          @php
            $selected = ($val->id == $evaluation_fields->scale_set) ? 'selected' : '';
          @endphp
        <option value="{{$val->id}}" {{ $selected }}>{{$val->name}}</option>
        @endforeach   
      </select>
  </div>
  <div class="col-md-4 mb-3">
    <label for="exampleFormControlSelect2" class="form-label" style="display: block;">Type<span class="text-danger">*</span></label>
      <select class="js-example-basic-multiple_edit form-select" name="inst_type[]" multiple="multiple">
        <option value="">Select type</option>
          @foreach($type as $types=>$typ)
            @php
              $selected = (in_array($typ->id, $inst_types)) ? 'selected' : '';
            @endphp
            <option value="{{$typ->id}}" {{ $selected }}>{{$typ->name}}</option>
          @endforeach
      </select>
  </div>
</div>

<div class="row">
  <div class="col-md-6 mb-3">
    <label for="exampleFormControlSelect1" class="form-label">Evaluation Question(English)<span class="text-danger">*</span></label>
    <input type="text" class="form-control title_en" name="field_title_en" placeholder="Enter Evaluation Question (en)" value="{{ $evaluation_fields->field_name_en }}" required />  
  </div>
  <div class="col-md-6 mb-3">
    <label for="exampleFormControlSelect1" class="form-label">Evaluation Question(Bangla)<span class="text-danger">*</span></label>
    <input type="text" class="form-control title_bn" name="field_title_bn" placeholder="Enter Evaluation Question (bn)" value="{{ $evaluation_fields->field_name_bn }}" required />
  </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.js-example-basic-multiple_edit').select2({
          dropdownParent: $('#UpdateModal')
        });
    });
  </script>