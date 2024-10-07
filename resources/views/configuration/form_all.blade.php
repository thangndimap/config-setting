@extends('layout.app')
@section('content')
    @php
        $config_module = config('configuration.config_module') ?? [];
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
                            <select name="module" class="form-control select2_single" id="select_module" onchange="page_combo.changeModule(this.value);">
                                <option value="">Seclect module</option>
                                @if (!empty($config_module))
                                    @foreach ($config_module as $key => $module)
                                        <option value="{{$key}}">{{$module}}</option>
                                    @endforeach
                                @endif
                            </select>
                            
                        </div>
                    </div>
                    
                    <div class="" id="page_formula_product_list">
                        <div class="product-lists">
                            <div class="product-lists-item">
                                    <div class="table-responsive">
                                        <table class="table table-bordered  ">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Key:</th>
                                                    <th>Value:</th>
                                                    <th>Trạng thái</th>
                                                    <th width="35%">Form:</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="product-list">
                                                <tr class="product-item-list" id="formula_production_choose_0"data-counter="0"> 
                                                    <td>
                                                        <input class="form-control" id="field_name_0" type="text"
                                                            placeholder="" name="config[0][name]"  style="min-width:200px !important" required>
                                                    </td>
                                                    <td>
                                                        <input class="form-control" id="field_name_0" type="text"
                                                            placeholder="" name="config[0][key]"  style="min-width:200px !important" required>
                                                    </td>
                                                    <td>
                                                        <input class="form-control" id="field_operator_0"
                                                            type="text" placeholder="" name="config[0][value]"
                                                             style="min-width:200px !important">
                                                    </td>
                                                    <td>
                                                        <select name="config[0][status]" class="form-control select2_single" require>
                                                            <option value="active">Active</option>
                                                            <option value="inactive">Inactive</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <div class="form-group row">
                                                            <label class="col-lg-3 col-form-label">Loại trường <span style="color: red;">*</span></label>
                                                            <div class="col-lg-9">
                                                                <select name="config[0][type_input]" class="form-control select2_single" onchange="page_combo.changeInput(this.value, 0);">
                                                                    @if (!empty($config_input))
                                                                        @foreach ($config_input as $k => $input)
                                                                            <option value="{{$k}}">{{$input}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-1 row d-none" id="option_value_0">
                                                            <label class="col-lg-3 col-form-label">Giá trị <span style="color: red;">*</span></label>
                                                            <div class="col-lg-9">
                                                                <textarea name="config[0][option_value]" class="form-control" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-3 col-form-label">Required</label>
                                                            <div class="col-lg-9">
                                                                <input type="checkbox" name="config[0][is_required]" value="1" class="form-control-sm">
                                                            </div>
                                                        </div>
                                                        
                                                    </td>
                                                    
                                                    <td class="text-center">
                                                        <a class="list-icons-item" data-action="remove"
                                                            onclick="delete_current_dom('#formula_production_choose_0')"></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                               
                            </div>
                        </div>
                        
                            <button class="btn btn-primary btn-sm mr-2 mt-2" id="button_add_0"
                                onclick="page_combo.pro_addProduct()" type="button">Thêm</button>
                        
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
@stop
@push('scripts')
<script type="text/javascript">
    var page_combo = (function() {
            'use strict';
            var _counter = {{$count ?? 0}};
            var config_input = {!! json_encode(config('configuration.config_input')) !!} || [];
            
            return {
                changeInput: function(value, idx) {
                    if (value === 'select' || value === 'checkbox') {
                        $('#option_value_'+idx).removeClass('d-none');
                    } else {
                        $('#option_value_'+idx).addClass('d-none');
                    }
                },
                changeModule: function(value) {
                    var self = this; 
                    var serviceCode = "{{ config('app.service_code') }}";
                    var local_storage = 'select_module'+serviceCode;
                    
                    localStorage.setItem(local_storage, value);
                    $(".product-item-list").remove();
                    if(!value) {
                        self.pro_addProduct();
                        return false;
                    }
                    var url_load_product = `{{route('configurations.index')}}`;
                    
                    $.ajax({
                        url: url_load_product,
                        headers: {
                            "Authorization": "Bearer " + getCookie(
                                "imap_authen_access_token")
                        },
                        dataType: "json",
                        data: {
                            filter: {
                                module: value
                            },
                            order_by : {_id:'asc'}
                        },
                    }).done(function(response) {
                        if (response.length > 0) {
                            var html = '';
                            
                            response.forEach(function(item, index) {
                                _counter++;
                                var fields = config_input; 
                                var optionsHTML = '';
                                for (var key in fields) {
                                    if (fields.hasOwnProperty(key)) {
                                        optionsHTML += '<option value="' + key + '" '+ (item.type_input === key ? 'selected' : '') + '>' + fields[key] + '</option>';
                                    }
                                }
                                var pid = 'formula_production_choose_' + index;
                                html = '<tr class="product-item-list" id="' + pid + '" data-counter="' + index + '">\
                                                        <td>\
                                                            <input name="config['+ index +'][config_id]" type="hidden" value="'+item._id+'">\
                                                            <input class="form-control" id="field_name_'+ index +'" type="text"\
                                                                placeholder="" name="config['+ index +'][name]"  style="min-width:200px !important" value="'+item.name+'" required>\
                                                        </td>\
                                                        <td>\
                                                            <input class="form-control" id="field_name_'+ index +'"\
                                                            type="text" placeholder="" name="config[' + index + '][key]" value="'+item.key+'" required style="min-width:200px !important">\
                                                        </td>\
                                                        <td>\
                                                            <input class="form-control" id="field_operator_' + index +'" type="text" '+(item.value ?? '') + ''+' placeholder="" name="config[' + index + '][value]" style="min-width:200px !important">\
                                                        </td>\
                                                        <td>\
                                                            <select name="config[' + index + '][status]" class="form-control select2_single" require>\
                                                                <option value="active" '+ (item.status === "active" ? 'selected' : '') + '>Active</option>\
                                                                <option value="inactive" '+ (item.status === "inactive" ? 'selected' : '') + '>Inactive</option>\
                                                            </select>\
                                                        </td>\
                                                        <td>\
                                                             <div class="form-group row">\
                                                                <label class="col-lg-3 col-form-label">Loại trường <span style="color: red;">*</span></label>\
                                                                <div class="col-lg-9">\
                                                                    <select name="config[' + index + '][type_input]" class="form-control select2_single" onchange="page_combo.changeInput(this.value, ' + index + ');">\
                                                                            '+ optionsHTML +'\
                                                                    </select>\
                                                                </div>\
                                                            </div>\
                                                            <div class="form-group mt-1 row d-none" id="option_value_' + index + '">\
                                                                <label class="col-lg-3 col-form-label">Giá trị <span style="color: red;">*</span></label>\
                                                                <div class="col-lg-9">\
                                                                    <textarea name="config[' + index + '][option_value]" class="form-control" rows="3">'+(item.option_value ?? '') + ''+'</textarea>\
                                                                </div>\
                                                            </div>\
                                                            <div class="form-group row">\
                                                                <label class="col-lg-3 col-form-label">Required</label>\
                                                                <div class="col-lg-9">\
                                                                    <input type="checkbox" name="config[' + index + '][is_required]" value="1" '+ (item.is_required == 1 ? 'checked' : '') + ' class="form-control-sm">\
                                                                </div>\
                                                            </div>\
                                                        </td>\
                                                        <td class="text-center"></td>\
                                                    </tr>';
                                $("#product-list").append(html);

                                $(".product-lists").show();
                                $('#product-list').trigger("MainContentReloaded", []);
                                self.changeInput(item.type_input, index);
                            });     
                        } else {
                            self.pro_addProduct();
                        }
                        
                    });
                },
                pro_addProduct: function() {
                    
                    var counter = _counter++;
                    var pid = 'formula_production_choose_' + counter;
                    var fields = config_input; 
                    var optionsHTML = '';
                    for (var key in fields) {
                        if (fields.hasOwnProperty(key)) {
                            optionsHTML += '<option value="' + key + '">' + fields[key] + '</option>';
                        }
                    }
                    
                    var html = '<tr class="product-item-list" id="' + pid + '" data-counter="' + counter + '">\
                                            <td>\
                                                <input class="form-control" id="field_name_'+ counter +'" type="text"\
                                                    placeholder="" name="config['+ counter +'][name]"  style="min-width:200px !important" required>\
                                            </td>\
                                            <td>\
                                                <input class="form-control" id="field_name_'+ counter +'"\
                                                type="text" placeholder="" name="config[' + counter + '][key]" required style="min-width:200px !important">\
                                            </td>\
                                            <td>\
                                                <input class="form-control" id="field_operator_' + counter +'" type="text" placeholder="" name="config[' + counter + '][value]" style="min-width:200px !important">\
                                            </td>\
                                            <td>\
                                                <select name="config[' + counter + '][status]" class="form-control select2_single" require>\
                                                    <option value="active">Active</option>\
                                                    <option value="inactive">Inactive</option>\
                                                </select>\
                                            </td>\
                                            <td>\
                                                <div class="form-group row">\
                                                    <label class="col-lg-3 col-form-label">Loại trường <span style="color: red;">*</span></label>\
                                                    <div class="col-lg-9">\
                                                        <select name="config[' + counter + '][type_input]" class="form-control select2_single" onchange="page_combo.changeInput(this.value, ' + counter + ');">\
                                                                '+optionsHTML+'\
                                                        </select>\
                                                    </div>\
                                                </div>\
                                                <div class="form-group mt-1 row d-none" id="option_value_' + counter + '">\
                                                    <label class="col-lg-3 col-form-label">Giá trị <span style="color: red;">*</span></label>\
                                                    <div class="col-lg-9">\
                                                        <textarea name="config[' + counter + '][option_value]" class="form-control" rows="3"></textarea>\
                                                    </div>\
                                                </div>\
                                                 <div class="form-group row">\
                                                    <label class="col-lg-3 col-form-label">Required</label>\
                                                    <div class="col-lg-9">\
                                                        <input type="checkbox" name="config[' + counter + '][is_required]" value="1" class="form-control-sm">\
                                                    </div>\
                                                </div>\
                                            </td>\
                                            <td class="text-center">\
                                                <a class="list-icons-item" data-action="remove" onclick="delete_current_dom(\'#' +pid + '\')"></a>\
                                            </td>\
                                        </tr>';
                    $("#product-list").append(html);

                    $(".product-lists").show();
                    $('#' + pid).trigger("MainContentReloaded", []);
                },
            }
        })();

        function delete_current_dom(dom) {
            $(dom).remove();
        }
        $(document).ready(function() {
            var serviceCode = "{{ config('app.service_code') }}";
            var local_storage = 'select_module'+serviceCode;
            var activeTab = localStorage.getItem(local_storage) || 'invoices';
            $('#select_module').val(activeTab);
            page_combo.changeModule(activeTab);
        });
    </script>
@endpush
