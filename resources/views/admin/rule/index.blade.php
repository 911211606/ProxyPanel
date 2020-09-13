@extends('admin.layouts')
@section('css')
    <link href="/assets/global/vendor/bootstrap-table/bootstrap-table.min.css" type="text/css" rel="stylesheet">
    <link href="/assets/global/vendor/bootstrap-select/bootstrap-select.min.css" type="text/css" rel="stylesheet">
@endsection
@section('content')
    <div class="page-content container-fluid">
        <div class="panel">
            <div class="panel-heading">
                <h2 class="panel-title">规则列表</h2>
                <div class="panel-actions">
                    <button data-toggle="modal" data-target="#add" class="btn btn-outline-primary">
                        <i class="icon wb-plus" aria-hidden="true"></i>添加规则
                    </button>
                </div>
            </div>
            <div class="panel-body">
                <div class="form-row">
                    <div class="form-group col-xxl-1 col-lg-3 col-md-3 col-4">
                        <select class="form-control" id="type" name="type" data-plugin="selectpicker" data-style="btn-outline btn-primary" onChange="Search()">
                            <option value="" hidden>类型</option>
                            <option value="1">正则表达式</option>
                            <option value="2">域名</option>
                            <option value="3">IP</option>
                            <option value="4">协议</option>
                        </select>
                    </div>
                    <div class="form-group col-xxl-1 col-lg-3 col-md-3 col-4">
                        <a href="/rule" class="btn btn-danger">重 置</a>
                    </div>
                </div>
                <table class="text-md-center" data-toggle="table" data-mobile-responsive="true">
                    <thead class="thead-default">
                    <tr>
                        <th> #</th>
                        <th> 类型</th>
                        <th> 描述</th>
                        <th> 值</th>
                        <th> 操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($rules as $rule)
                        <tr>
                            <td> {{$rule->id}} </td>
                            <td> {!! $rule->type_label !!} </td>
                            <td>
                                <input type="text" class="form-control" name="rule_name" id="rule_name_{{$rule->id}}" value="{{$rule->name}}"/>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="rule_pattern" id="rule_pattern_{{$rule->id}}" value="{{$rule->pattern}}"/>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editRule('{{$rule->id}}', '{{route('rule.update',$rule->id)}}')">
                                    <i class="icon wb-edit"></i></button>
                                <button class="btn btn-sm btn-outline-danger" onclick="delRule('{{route('rule.destroy',$rule->id)}}','{{$rule->name}}')">
                                    <i class="icon wb-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-4">
                        共 <code>{{$rules->total()}}</code> 条审计规则
                    </div>
                    <div class="col-sm-8">
                        <nav class="Page navigation float-right">
                            {{$rules->links()}}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add" aria-hidden="true" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple modal-center">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">添加规则</h4>
                </div>
                <form action="#" method="post" class="modal-body">
                    <div class="alert alert-danger" style="display: none;" id="msg"></div>
                    <div class="form-row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="col-md-2 col-sm-3 col-form-label" for="add_type">类型</label>
                                <div class="col-xl-4 col-sm-8">
                                    <select class="form-control" name="add_type" id="add_type" data-plugin="selectpicker" data-style="btn-outline btn-primary">
                                        <option value="1">正则表达式</option>
                                        <option value="2">域名</option>
                                        <option value="3">IP</option>
                                        <option value="4">协议</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 col-sm-3 col-form-label" for="name">描述</label>
                                <div class="col-xl-6 col-sm-8">
                                    <input type="text" class="form-control" name="name" id="name" required/>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="col-md-2 col-sm-3 col-form-label" for="pattern">值</label>
                                <div class="col-xl-6 col-sm-8">
                                    <input type="text" class="form-control" name="pattern" id="pattern" required/>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-danger">关 闭</button>
                    <button type="button" class="btn btn-primary" onclick="addRule()">添 加</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="/assets/global/vendor/bootstrap-table/bootstrap-table.min.js" type="text/javascript"></script>
    <script src="/assets/global/vendor/bootstrap-table/extensions/mobile/bootstrap-table-mobile.min.js" type="text/javascript"></script>
    <script src="/assets/global/vendor/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/js/Plugin/bootstrap-select.js" type="text/javascript"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        $('#type').selectpicker('val', {{Request::get('type')}});
      });

      // 添加规则
      function addRule() {
        $.post("{{route('rule.store')}}", {
          _token: '{{csrf_token()}}',
          type: $('#add_type').val(),
          name: $('#name').val(),
          pattern: $('#pattern').val(),
        }, function(ret) {
          $('#add').modal('hide');
          if (ret.status === 'success') {
            swal.fire({title: ret.message, type: 'success', timer: 1000, showConfirmButton: false}).
                then(() => window.location.reload());
          }
          else {
            swal.fire({title: ret.message, type: 'error'}).then(() => window.location.reload());
          }
        });
      }

      // 编辑规则
      function editRule(id, url) {
        $.ajax({
          type: 'PUT',
          url: url,
          data: {
            _token: '{{csrf_token()}}',
            rule_name: $('#rule_name_' + id).val(),
            rule_pattern: $('#rule_pattern_' + id).val(),
          },
          dataType: 'json',
          success: function(ret) {
            if (ret.status === 'success') {
              swal.fire({title: ret.message, type: 'success', timer: 1000, showConfirmButton: false}).
                  then(() => window.location.reload());
            }
            else {
              swal.fire({title: ret.message, type: 'error'}).then(() => window.location.reload());
            }
          },
        });
      }

      // 删除规则
      function delRule(url, name) {
        swal.fire({
          title: '警告',
          text: '确定删除规则 【' + name + '】 ？',
          type: 'warning',
          showCancelButton: true,
          cancelButtonText: '{{trans('home.ticket_close')}}',
          confirmButtonText: '{{trans('home.ticket_confirm')}}',
        }).then((result) => {
          if (result.value) {
            $.ajax({
              type: 'DELETE',
              url: url,
              data: {_token: '{{csrf_token()}}'},
              dataType: 'json',
              success: function(ret) {
                if (ret.status === 'success') {
                  swal.fire({title: ret.message, type: 'success', timer: 1000, showConfirmButton: false}).
                      then(() => window.location.reload());
                }
                else {
                  swal.fire({title: ret.message, type: 'error'}).then(() => window.location.reload());
                }
              },
            });
          }
        });
      }

      //回车检测
      $(document).on('keypress', 'input', function(e) {
        if (e.which === 13) {
          Search();
          return false;
        }
      });

      // 搜索
      function Search() {
        window.location.href = '/rule?type=' + $('#type').val();
      }
    </script>
@endsection