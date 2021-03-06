@extends('master')

@section('title')
	เพิ่มข้อมูลนักเรียน
@endsection

@section('studen')
	active
@endsection

@section('breadcrumb')
 <li><a href="{{ route('member') }}">{{ Auth::user()->username }}</a></li>
 <li><a href="{{ route('studen') }}">เพิ่มข้อมูลนักเรียน</a></li>
@endsection 

@section('content')

<style type="text/css">
	.input-group{
		margin-bottom:5px; 
	}
	.input-title{
		width:80px;
		text-align: left;;
	}
	.studenList{
		cursor: pointer;
	}
	a{
		cursor: pointer;
	}


</style>

<div class="row">
	<div class="col-md-4">
	{!! Form::open(array('class'=>'updateForm')) !!}
	{!! Form::hidden('id','',array('id'=>'idMemberUpdate')) !!}
		<div class="panel panel-primary">
		  <div class="panel-heading">
		  	<div class="row">
		  		<div class="col-md-8">
		  			เพิ่ม/แก้ไขข้อมูลนักเรียน
		  		</div>
		  		<div class="col-md-4">
		  			{!! Form::checkbox("admin","true") !!} ผู้ดูแลระบบ
		  		</div>
		  	</div>
		  	
		  </div>
		  <div class="panel-body">
		  	
		  		<div class="input-group">
				  <span class="input-group-addon" id="basic-addon1"><div class="input-title">ปีจบการศึกษา</div></span>
				  <input value="{{ date('Y')+542 }}" type="text" class="form-control" name="gradYear" aria-describedby="basic-addon1">
				</div>
		  		<div class="input-group">
				  <span class="input-group-addon"><div class="input-title">สายชั้น</div></span>
				  <select class="form-control" id="sel1" name="classRoom">
				  	@foreach($classTeachers as $classTeacher)
				    	<option class="classRoomOption" value="{{$classTeacher->title}},{{$classTeacher->value}}">{{$classTeacher->title}}/{{$classTeacher->value}} {{$classTeacher->detail}}</option>
				    @endforeach
				  </select>
				</div>
				<div class="input-group">
				  <span class="input-group-addon" id="basic-addon1"><div class="input-title">เลขที่</div></span>
				  <input type="text" class="form-control" name="CRNo" aria-describedby="basic-addon1">
				</div>
				<div class="input-group">
				  <span class="input-group-addon" id="basic-addon1"><div class="input-title">รหัสนักเรียน</div></span>
				  <input type="text" id="studenNo" class="form-control" name="studenNo" aria-describedby="basic-addon1">
				</div>
				<div class="input-group">
				  <span class="input-group-addon" id="basic-addon1"><div class="input-title">รหัสประชาชน</div></span>
				  <input type="text" class="form-control" name="idCardNo" aria-describedby="basic-addon1">
				</div>
				<div class="input-group">
				  <span class="input-group-addon"><div class="input-title">คำนำหน้าชื่อ</div></span>
				  <select class="form-control" name="titleName" id="sel2">
				  	@foreach($titleNames as $titleName)
				    	<option class="titleNameOption" value="{{$titleName->title}}">{{$titleName->title}}</option>
				    @endforeach
				  </select>
				</div>
				<div class="input-group">
				  <span class="input-group-addon" id="basic-addon1"><div class="input-title">ชื่อ</div></span>
				  <input type="text" class="form-control" name="name" aria-describedby="basic-addon1">
				</div>
				<div class="input-group">
				  <span class="input-group-addon" id="basic-addon1"><div class="input-title">นามสกุล</div></span>
				  <input type="text" class="form-control" name="lastname" aria-describedby="basic-addon1">
				</div>
				<div class="row" align="center">
					 <div class="col-md-6">
						<button onclick="clearInputUpdate();" type="reset" style="width:100%" class="btn btn-warning">Reset</button>
					 </div>
					 <div class="col-md-6">
						<button id="updateSaveBtn" type="submit" onclick="$(this).addClass('disabled').text('Loading....');" style="width:100%" class="btn btn-success">บันทึกข้อมูล</button> 	
					 </div>
				</div>

		  </div>
		</div>
	{!! Form::close(); !!}
	</div>
	<div class="col-md-8">
		<div class="panel panel-info">
			<div class="panel-heading">
				{!! Form::open(array('url'=>route('studenView'),'method'=>'get','class'=>'searchForm')) !!}
				{!! Form::hidden('page','',array('id'=>'pageSearch')) !!}
				<div class="row">
					<div class="col-md-1">
						ค้นหา
					</div>
					<div class="col-md-3">
						<select class="form-control input-sm" name="column">
							<option value="studenNo">รหัสนักเรียน</option>
							<option value="idCardNo">รหัสประชาชน</option>
							<option value="name">ชื่อ-นามสกุล</option>
							<option value="CRNo">เลขที่</option>
							<option value="room">ห้อง</option>
							<option value="class">ชั้น</option>
							<option value="address">ที่อยู่</option>
							<option value="tel">เบอร์โทรศัพท์</option>
							<option value="birthday">วันเกิด</option>
							<option value="gradYear">ปีจบการศึกษา</option>
							<option value="id">id</option>
							<option value="username">username</option>
							<option value="email">email</option>
						</select>
					</div>
					<div class="col-md-5">
						<div class="input-group">
							{!! Form::text('key','',array('class'=>'form-control key input-sm','placeholder'=>'คำค้นหา')) !!}
							<span class="input-group-btn">
								<button class="btn btn-success input-sm" style="width:100%;">ค้นหา</button>
							</span>
						</div>
					</div>
					<div class="col-md-3">
						<div class="input-group">
							{!! Form::text('limit',$studenListLimit,array('class'=>'form-control input-sm listLimit','placeholder'=>'จำนวน','style'=>'text-align:center;','onchange'=>'$(".searchForm").submit()')) !!}
							<span class="input-group-addon">ชื่อ/หน้า</span>
						</div>
						
					</div>
				</div>
				{!! Form::close(); !!}
			</div>
			<div class="panel-body search-result" style="margin: 0; padding: 0; width: 100%; overflow: auto;" align="center">
				<br><img src="//{{$_SERVER['SERVER_NAME']}}/picture/ajax-loader.gif"><br><br>
			</div>
			
		</div>
	</div>
</div><!--end row-->

<script type="text/javascript">

	function clearInputUpdate(){
		$('input[name=admin]').prop( "checked", false );
		$('input[name=CRNo]').val('');
		$('input[name=studenNo]').val('');
		$('input[name=idCardNo]').val('');
		$('input[name=name]').val('');
		$('input[name=lastname]').val('');
		$('input[name=id]').val('');


	}

	function pagination(page,column,key){
		limit = $('.listLimit').val();
		$('#pageSearch').val(page);
		$('.search-result').html('<br><img src="//{{$_SERVER["SERVER_NAME"]}}/picture/ajax-loader.gif"><br><br>');
		$('.searchForm').submit();
		//$('.search-result').load('http://ubr.local/admin/studen/view?page='+page+'&limit='+limit+'&column='+column+'&key='+key);
	}
	function updateStuden(id){
		var ele = $('#list-'+id);

		var gradYear = ele.attr('data-gradYear');
		var schoolClass = ele.attr('data-class');
		var CRNo = ele.attr('data-CRNo');
		var studenNo = ele.attr('data-studenNo');
		var idCardNo = ele.attr('data-idCardNo');
		var titleName = ele.attr('data-titleName');
		var name = ele.attr('data-name');
		var lastname = ele.attr('data-lastname');
		var admin = ele.attr('data-admin');
		var room = ele.attr('data-room');
		$('input[name=id]').val(id);
		$('input[name=gradYear]').val(gradYear);
		
		$('.classRoomOption').each(function(){
			
			$(this).removeAttr('selected');
			if($(this).attr('value') == schoolClass+','+room){
				$(this).attr('selected','selected');
			}
		});
		//$('input[name=class]').val(schoolClass);
		$('input[name=CRNo]').val(CRNo);
		$('input[name=studenNo]').val(studenNo);
		$('input[name=idCardNo]').val(idCardNo);
		$('.titleNameOption').each(function(){
			$(this).removeAttr('selected');
			if($(this).attr('value') == titleName){
				$(this).attr('selected','selected');
			}
		});



		//$('input[name=titleName]').val(titleName);
		$('input[name=name]').val(name);
		$('input[name=lastname]').val(lastname);
		if(admin == 1){
			$('input[name=admin]').prop( "checked", true );
		}else{
			$('input[name=admin]').prop( "checked", false );
		}

		


	}

	function seleteDel(){
		if($('input[name=delAll]').is(':checked')){
			$('input[name=del]').prop( "checked", true );
		}else{
			$('input[name=del]').prop( "checked", false );
		}
	}

	function delStuden(){
		var idDel = [];
			$('input[name=del]:checked').each(function(){
				idDel.push($(this).val());
			});
			if(idDel.length == 0){
				alert('กรุณาเลือกรายการที่ต้องการลบ');
			}else if(confirm('ท่านต้องการลบรายชื่อที่เลือกหรือไม่?')){
				$.get('{{route("studenDel")}}?id='+idDel,function(data){
					if(data == 'false'){
						alert('ไม่สามารถลบรายการนี้ได้!');
					}else{
						$('.searchForm').submit();
					}
				});
			}
	}

$(document).ready(function(){

	var optionsUpdate = { 
	    success:    function(data) { 
	        $('#updateSaveBtn').removeClass('disabled').text('บันทึกข้อมูล');
			if(data == 'false'){
				alert('ข้อมูลไม่ตรงตามเงื่อนไข กรุณาตรวจสอบข้อมูล!');
			}else{
				$('.searchForm').submit();
				clearInputUpdate();
				
			}
	    },
	    error: function(){
	    	$('#updateSaveBtn').removeClass('disabled').text('บันทึกข้อมูล');
	    	alert('ไม่สามารถทำรายการได้ในขณะนี้!');
	    	clearInputUpdate();
	    }
	}; 
	$('.updateForm').each(function(){
		$(this).ajaxForm(optionsUpdate)
	});
	var options = { 
		beforeSubmit: function(){
			$('.search-result').html('<br><img src="//{{$_SERVER["SERVER_NAME"]}}/picture/ajax-loader.gif"><br><br>');
		},
	    success:    function(data) { 
	        $('.search-result').html(data);
	        $('.studenList').each(function(){
				$(this).bind('click',function(){
					$('.studenList').removeClass('active');
					$(this).addClass('active');
				});
			});
	    },
	    error: function(){
	    	alert('กรุณาตรวจสอบการเชื่อมต่อ!');
	    }
	}; 
	$('.searchForm').ajaxForm(options);
	$('.searchForm').submit();

});
</script>



@endsection