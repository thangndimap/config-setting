@extends('common.filter_layout')
@section('section_filter')
    @php
        $config_input = config('configuration.config_input') ?? [];
    @endphp
<div class="sidebar-section">
    <div class="sidebar-section-header">
        <span class="font-weight-semibold">Sidebar search</span>
        <div class="list-icons ml-auto">
            <a href="#sidebar-search" class="list-icons-item" data-toggle="collapse">
                <i class="icon-arrow-down12"></i>
            </a>
        </div>
    </div>
    <div class="collapse show" id="sidebar-search-1">
        <div class="sidebar-section-body">
            <div class="form-group">
                <label class="control-label" >Module</label>
                <select name="filter[module]" class="form-control select2_single">
                    <option value="">Seclect module</option>
                    @if(!empty($config_input))
                        @foreach ($config_input as $k => $input)
                            <option value="{{$k}}" @selected(!empty($filter['module']) && $filter['module'] == $k)>{{$input}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="form-group">
                <label class="control-label" >Key</label>
                <input type="text" name="filter[key][like]" placeholder="" class="form-control" value="{{$filter['key']['like']??''}}">
            </div>
            <div class="form-group">
                <label class="control-label" >Trạng thái</label>
                <select name="filter[status]" class="form-control select2_single">
                    <option value="">----</option>
                    <option value="active" @selected(!empty($filter['status']) && $filter['status'] == 'active')>
                        Active
                    </option>
                    <option value="inactive" @selected(!empty($filter['status']) && $filter['status'] == 'inactive')>
                        Inactive
                    </option>
                </select>
            </div>

        </div>
    </div>
</div>
@endsection