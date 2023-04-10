<x-layout>
	<x-slot name="title">
		{{$title}} {{!empty($subscriber['email']) ? "- ".$subscriber['email'] : ""}}
    </x-slot>

	<ul class="notices">
		@if(count($errors) > 0)
			@foreach ($errors->all() as $error)
				<li class="notice notice-error">{{ $error }}</li>
			@endforeach
		@endif
	</ul>

	<form method="post" action="{{!empty($method) ? route('subscribers.store') : route('subscribers.update',['subscriber'=>$subscriber['email']])}}" novalidate id="subscriber">
		<script type="text/template" class="form-success">{{__('forms.subscriber_saved')}}</script>

		@method($method ?? "PUT")

		<p>
			<label for="email">{{ __('forms.email') }}</label>
			<input id="email" name="email" type="email" {!! (empty($method) ? "readonly" : "") !!} value="{{old('email',$subscriber['email'] ?? '')}}" placeholder="{{__('forms.email_placeholder')}}" />
		</p>

		<p>
			<label for="name">{{ __('forms.name') }}</label>
			<input id="name" name="name" value="{{old('name',$subscriber['name'] ?? '')}}" placeholder="{{__('forms.name_placeholder')}}" />
		</p>

		<p>
			<label for="country">{{ __('forms.country') }}</label>
			<input id="coutnry" name="fields[country]" value="{{old('fields.country',$subscriber['country'] ?? '')}}" placeholder="{{__('forms.country_placeholder')}}" />
		</p>

		<p><button type="submit">{{__('forms.save')}}</button></p>
	</form>

</x-layout>
       

