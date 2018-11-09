<a href="{{ $link }}" class="btn btn-sm btn-outline btn-info">
    <i class="fa fa-refresh" aria-hidden="true"></i>&nbsp;
    @lang("{$prefix}.{$type}.check_email")
</a>

    {{--{!! Form::button('' . trans("{$prefix}.{$type}.check_email"), array(--}}
            {{--'type' => 'submit',--}}
            {{--'class' => 'btn btn-sm btn-outline btn-info js-check-email-btn',--}}
            {{--'data-url' => route("{$type}.check.email"),--}}
            {{--'title' => trans("{$prefix}.{$type}.check_email"),--}}
            {{--'onclick' => "return false;",--}}
    {{--)) !!}--}}