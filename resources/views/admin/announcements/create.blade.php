@extends('layouts.admin')

@section('title', '発行')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12">
					<h1>
						<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.users.index')}}');" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>ユーザーズ
						</a>
					</h1>
				</div>
			</div>
		</div>
	</div>

	<hr class="hr-thick">

	<div class="row">
		<div class="col-12 col-md-8 mx-auto">
			<div class="card dark-shadow mb-5" id="inner-content">
				<div class="card-body">
					<form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data">
						{{ csrf_field() }}
						
						{{-- ANNOUNCEMENT POSTER --}}
						<div class="row">
							<div class="col-12 col-lg-6">
								<div class="form-group text-center text-lg-left w-100" style="max-height: 20rem;">
									<label class="h5" for="image">発行のイメージ</label><br>
									<img src="{{ asset('uploads/announcements/default.png') }}" class="img-fluid cursor-pointer border" style="border-width: 0.25rem!important; max-height: 16.25rem;" id="image" alt="Announcement Image">
									<input type="file" name="image" class="d-none" accept=".jpg,.jpeg,.png"><br>
									<small class="text-muted pt-0 mt-0"><b>初認された形式：</b> JPEG, JPG, PNG, WEBP</small><br>
									<small class="text-muted pt-0 mt-0"><b>最大サイズ：</b> 5MB</small>
								</div>
							</div>

							{{-- TITLE --}}
							<div class="col-12 col-lg-6">
								<div class="form-group">
									<label class="h5" for="title">タイトル</label>
									<input class="form-control" type="text" name="title" value="{{ old('title') }}"/>
								</div>

								<div class="form-group">
									<label class="h5" for="source">Source</label>
									<input class="form-control" type="text" name="source" value="{{ old('source') }}"/>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col">
								<label class="h5" for="content">内容</label>
								<textarea class="summernote" name="content" rows="5">{!! old('content') !!}</textarea>
							</div>
						</div>

						<div class="row py-3">
							<div class="col">
								<button class="btn btn-success ml-auto" type="submit" data-action="submit">提出する</button>
								<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.announcements.index')}}');" class="btn btn-danger ml-3 mr-auto">キャンセル</a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('css')
<style type="text/css">
	.note-toolbar {
		display: flex;
		flex-wrap: wrap;
		align-content: center;
	}
</style>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/util/disable-on-submit.js') }}"></script>
<script type="text/javascript">
	function openInput(obj) {
		$("[name=" + obj.attr("id") + "]").trigger("click");
	}

	function swapImg(obj) {
		if (obj.files && obj.files[0]) {
			let reader = new FileReader();

			reader.onload = function(e) {
				$("#image").attr("src", e.currentTarget.result);
			}

			reader.readAsDataURL(obj.files[0])
		}
		else {
			$("#image").attr("src", "{{ asset('uploads/announcements/default.png') }}");
		}
	}

	$(document).ready(function() {
		// Profile Image Changing
		$("#image").on("click", function() {openInput($(this))});
		$("[name=image]").on("change", function() {swapImg(this)});

		// Summernote
		$('.summernote').summernote({
			lang: 'ja-JP',
			minHeight: 128,
			maxHeight: 384,
			height: 256,
			placeholder: 'ここに内容が入れます。。。',
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'underline', 'clear']],
				['fontname', ['fontname', 'fontsize']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['insert', ['link', 'hr', 'picture', 'video']],
				['view', ['fullscreen', 'codeview', 'help']],
				['history', ['undo', 'redo']]
			],
			popover: {
				image: [
					['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
					['float', ['floatLeft', 'floatRight', 'floatNone']],
					['remove', ['removeMedia']]
				],
				air: [
					['insert', ['link']],
				],
				link: [
					['link', ['linkDialogShow', 'unlink']]
				]
			}
		});
	});
</script>
@endsection