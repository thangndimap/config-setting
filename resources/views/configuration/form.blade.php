@extends('layout.app')
@section('content')
    @php
        $config_input = config('configuration.config_input') ?? [];
    @endphp
    <div class="content">
        <div class="card">
            <form class="ajax-submit-form" action="{{ $action }}" method="{{ $method }}">
                <div class="card-header header-elements-inline bg-">
                    <h5 class="card-title">Thông tin cấu hình </h5>
                    <div class="header-elements">
                        <button id="submit_class" type="submit" class="btn btn-success ajax-submit-button">Lưu thông tin<i class="icon-paperplane ml-2"></i></button>
                        @csrf
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="control-label col-sm-2 col-xs-12">Module <span style="color: red;">*</span></label>
                        <div class="col-sm-10 col-xs-12 validation_form">
                            <div  class="form-control">{{ __('finance.config_module.'.$row['module']) }}</div>
                            
                            
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-sm-2 col-xs-12">Name <span style="color: red;">*</span></label>
                        <div class="col-sm-10 col-xs-12 validation_form">
                            <input type="text" name="name" value="{{ $row['name'] ?? '' }}" autocomplete="off"
                                class="form-control">
                            
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-sm-2 col-xs-12">Key <span style="color: red;">*</span></label>
                        <div class="col-sm-10 col-xs-12 validation_form">
                            <input type="text" name="key" value="{{ $row['key'] ?? '' }}" autocomplete="off"
                                class="form-control">
                            
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-lg-2">Value</label>
                        <div class="col-sm-10 col-xs-12 validation_form">
                            <input type="text" name="value" value="{{ $row['value'] ?? '' }}" autocomplete="off"
                                class="form-control">
                            
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-sm-2 col-xs-12">Loại trường<span style="color: red;">*</span></label>
                        <div class="col-sm-10 col-xs-12 validation_form">
                            <select name="type_input" class="form-control select2_single" onchange="changeInput(this.value);">
                                @if(!empty($config_input))
                                    @foreach ($config_input as $k => $input)
                                        <option value="{{$k}}" @selected(!empty($row['type_input']) && $row['type_input'] == $k)>{{$input}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group row @if(!empty($row['type_input']) && $row['type_input'] == 'input') d-none @endif " id="option_value">
                        <label class="control-label col-sm-2 col-xs-12">Giá trị<span style="color: red;">*</span></label>
                        <div class="col-sm-10 col-xs-12 validation_form">
                            <textarea name="option_value" class="form-control" rows="3">{!! $row['option_value'] ?? '' !!}</textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Required</label>
                        <div class="col-lg-10">
                            <input type="checkbox" name="is_required" value="1" class="form-control-sm" @checked(!empty($row['is_required']))>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-lg-2">Status</label>
                        <div class="col-sm-10 col-xs-12 validation_form">
                            <select name="status" class="form-control select2_single" require>
                                <option value="active" @selected(!empty($row['status']) && $row['status'] == 'active')>
                                    Active
                                </option>
                                <option value="inactive" @selected(!empty($row['status']) && $row['status'] == 'inactive')>
                                    Inactive
                                </option>
                            </select>
                            
                        </div>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
@stop
@push('scripts')
<script type="text/javascript">
    $(document).on('click', '#number_use_checkbox', function() {
        if(this.checked) {
            $('#number_use').val('');
            $("#number_use").prop('disabled', true);
        } else {
            $("#number_use").prop('disabled', false);
        }

    });
    function changeInput(value) {
        
        if (value === 'select' || value === 'checkbox') {
            $('#option_value').removeClass('d-none');
        } else {
            $('#option_value').addClass('d-none');
        }
                
    }
    </script>
@endpush
