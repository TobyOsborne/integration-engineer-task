<x-layout>
	<x-slot name="title">
		{{$title}}
    </x-slot>
	
	@if(!$has_key)
		<div class="new-user">
			<h2>{{__('views.new_user_title')}}</h2>
			<p>{!!__('views.new_user_message')!!}</p>
		</div>
	@endif

	<ul class="notices">
		@if(count($errors) > 0)
			@foreach ($errors->all() as $error)
				<li class="notice notice-error">{{ $error }}</li>
			@endforeach
		@endif
	</ul>

	<form method="post" action="{{ route('settings.update') }}" novalidate id="settings">
		<script type="text/template" class="form-success">{{__('forms.settings_saved')}}</script>

		@method('PUT')

		<p>
			<label for="api_key">{{ __('forms.api_key') }}</label>
			<textarea rows="12" id="api_key" name="api_key" placeholder="{{__('forms.api_key_placeholder')}}" >{{old('api_key',$has_key ? "**********" : "")}}</textarea>
		</p>

		<p><button type="submit">{{__('forms.save')}}</button></p>

	</form>
</x-layout>
       

