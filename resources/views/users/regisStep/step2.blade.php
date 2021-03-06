@extends('master')

@section('title')
	Register
@endsection

@section('register')
 active
@endsection 

@section('breadcrumb')
 <li><a href="{{ route('register') }}">Register</a></li>
@endsection 

@section('content')
<style type="text/css">
	
	.input-group,select,button{
		margin-bottom: 5px;
	}
</style>
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3">
			<div class="panel panel-primary">
				<div class="panel-heading">
					ลงทะเบียน Step2 
				</div>
				<div class="panel-body">
					{!! Form::open(array('url' => route('register'))) !!}
					{!! Form::hidden('step','3') !!}
					{!! Form::hidden('number',$request['number']) !!}
					{!! Form::hidden('id',$memberDetail->id) !!}
						<fieldset>
							<legend>ยืนยันข้อมูลนักเรียน <span style="color:red;font-size:0.7em">* จำเป็นต้องระบุข้อมูล</span></legend>
							@if(count($errors) > 0)
							<div class="alert alert-danger">
								<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
								
								@foreach($errors->all() as $message)
									<b>{{$message}}</b><br>
								@endforeach
								
							</div>
							@endif
							<div class="row">
								<div class="col-sm-12">
									<div class="alert alert-info" align="center">
										หมายเลขประจำตัวนักเรียน <strong>{{ $memberDetail->studenNo }}</strong>, 
										หมายเลขประจำตัวประชาชน <strong>{{ $memberDetail->idCardNo }}</strong>,
										<br>
										ชั้นมัธยมศึกษาปีที่ <strong>{{ $memberDetail->class }}/{{ $memberDetail->room }}</strong>,
										เลขที่ <strong>{{ $memberDetail->CRNo }}</strong>,
										ปีจบการศึกษา <strong>{{ $memberDetail->gradYear }}</strong>
										<br>
										<span style="color:red">หากข้อมูลไม่ถูกต้องกรุณาติดต่อผู้ดูแลระบบ</span>

										
									</div>
								</div>
								<div class="col-sm-12 col-md-3">
									<select name="titleName" class="form-control">
										@foreach($titleNames as $titleName)
									    	<option <?php if($memberDetail->titleName == $titleName->title) echo 'selected'; ?> value="{{$titleName->title}}">{{$titleName->title}}</option>
									    @endforeach
									</select>
								</div>
								<div class="col-sm-12 col-md-4">
							 		{!! Form::text('name',$memberDetail->name,array('class' => 'form-control','placeholder' => 'ชื่อ')) !!}	
								</div>
								<div class="col-sm-12 col-md-5">
							 		{!! Form::text('lastname',$memberDetail->lastname,array('class' => 'form-control','placeholder' => 'นามสกุล')) !!}	
								</div>
								
								<div class="col-sm-12">
									<div class="input-group">
										<span class="input-group-addon">ที่อยู่</span>
							 			{!! Form::text('address',$memberDetail->address,array('class' => 'form-control','placeholder' => 'บ้านเลขที่,หมู่,หมู่บ้าน,ตำบล,อำเภอ,จังหวัด,รหัสไปรษณีย์')) !!}	
									</div>
								</div>
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">ชื่อเล่น</span>
							 			{!! Form::text('nickname',$memberDetail->nickname,array('class' => 'form-control','placeholder' => 'เช่น ฟ้าใส')) !!}	
									</div>
								</div>
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">วันเกิด</span>
										<?php
											$birthday = ($memberDetail->birthday != '0000-00-00')?
															$memberDetail->birthday:'';
										?>
							 			{!! Form::text('birthday',$birthday,array('class' => 'datepicker form-control','placeholder' => 'ปีคศ./เดือน/วัน เช่น 1996/01/05')) !!}	
									</div>
								</div>
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">โทรศัพท์</span>
							 			{!! Form::text('tel',$memberDetail->tel,array('class' => 'form-control','placeholder' => 'เช่น 08-000-0000')) !!}	
									</div>
								</div>
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">ติดต่ออื่นๆ</span>
							 			{!! Form::text('contact',$memberDetail->contact,array('class' => 'form-control','placeholder' => 'เช่น Facebook,Line')) !!}	
									</div>
								</div>

								<!--<div class="col-sm-12 col-md-2">
									<div class="input-group">
										<span class="input-group-addon">หมายเลขประจำตัวนักเรียน</span>
							 			{!! Form::text('number',$memberDetail->name,array('class' => 'form-control','placeholder' => 'ชื่อ')) !!}	
										
									</div>
								</div>
								-->
							</div>
						</fieldset>
						<fieldset>
							<legend>ข้อมูลระบบ <span style="color:red;font-size:0.7em">* จำเป็นต้องระบุข้อมูล</span></legend>
							
							<div class="row">
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">Email</span>
							 			{!! Form::text('email',$memberDetail->email,array('class' => 'form-control','placeholder' => 'เช่น abc@def.com')) !!}	
									</div>
								</div>
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">Username(ใช้ Login)</span>
							 			{!! Form::text('username',$memberDetail->username,array('class' => 'form-control','placeholder' => '4-20 ตัวเลขหรือตัวอักษรภาษาอังกฤษเท่านั้น!')) !!}	
									</div>
								</div>
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">Password</span>
										<input class="form-control" placeholder="4-30 ตัวเลขหรือตัวอักษรภาษาอังกฤษเท่านั้น!" name="password" type="password" value="">
							 			
									</div>
								</div>
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">Re-Password</span>
							 			<input class="form-control" placeholder="4-30 ตัวเลขหรือตัวอักษรภาษาอังกฤษเท่านั้น!" name="password_confirmation" type="password" value="">
									</div>
								</div>
								<div class="col-sm-12" align="center">
									<button type="submit" class="btn btn-success" style="width:100%">ลงทะเบียน</button>
								</div>


							</div>
						</fieldset>
					{!! Form::close() !!}

					
				</div>
			</div>
		</div>
	</div>

<script type="text/javascript">
$(document).ready(function(){

	$('.btn').bind('click',function(){
		$(this).val('Loading.....').addClass('disabled');

	});

});
</script>
@endsection