@extends('layout.app')
@section('content')    
@include('common.content_header')
<div class="content">
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">Danh sách cấu hình</h5>
            <div class="header-elements ">
                <a class="load_not_ajax btn btn-teal" href="{{route('configuration.create')}}">Tạo mới</a>
            </div>
        </div>
               
            <table class="table datatable-fixed-both" width="100%">
                <thead id="checkbox_all">
                <tr>
                    <th>STT</th>
                    <th>Name</th>
                    <th>Key </th>
                    <th>Module</th>
                    <th>Loại trường</th>
                    <th>Trạng thái</th>
                    <th>Value</th>
                    <th class="all" style="display: block"><i class="icon-checkmark3"></i></th>
                </tr>
                </thead>
                <tbody>
                @php $i = 0 @endphp
                @foreach($rows as $row)
                @php $i++; @endphp
                <tr>
                    <td>{{$i}}</td>
                    <td>{{$row['name'] ?? ''}}</td>
                    <td>
                        {{ $row['key'] ?? '' }}
                    </td>                    
                    
                    <td>
                        {{ __('configuration.config_module.'.$row['module']) }}
                    </td>
                    <td>
                        {{ $row['type_input'] ?? '' }}
                    </td> 
                    <td>
                        {{ $row['status'] ?? '' }}
                    </td> 
                    <td>
                        {{ $row['value'] ?? '' }}
                    </td> 
                    <td>

                        <div class="dropdown dropleft">
                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                <i class="icon-menu9"></i>
                            </a>

                            <div class="dropdown-menu">
                                {!!  menuConfiguration($row) !!}
                            </div>
                        </div>
                        
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>             
        <div class="card-footer">
            {!! $pagination ?? '' !!}
        </div>
    </div>            
</div>
@endsection
@section('left-slidebar')
    @include('configuration.section.filter')
@endsection