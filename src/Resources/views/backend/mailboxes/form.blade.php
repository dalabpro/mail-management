<div class="form-group col-md-12 form-material floating{{ $errors->has('email') ? ' has-danger' : '' }}" data-plugin="formMaterial">
    {{ Form::text('email', null, ['class'=>'form-control', 'required']) }}
    <label class="floating-label col-md-12">
        @lang("MailManagement::{$prefix}.{$type}.email")
    </label>
    @if ($errors->has('email'))
        <span class="help-block form-control-label">
            {{ $errors->first('email') }}
        </span>
    @endif
</div>
<div class="row">

    <div class="col-md-6">
        <h3 class="panel-title">@yield('title') smtp</h3>
        <div class="form-group col-md-12 form-material floating{{ $errors->has('smtp_host') ? ' has-danger' : '' }}" data-plugin="formMaterial">
            {{ Form::text('smtp_host', null, ['class'=>'form-control', 'required']) }}
            <label class="floating-label col-md-12">
                @lang("MailManagement::{$prefix}.{$type}.smtp_host")
            </label>
            @if ($errors->has('smtp_host'))
                <span class="help-block form-control-label">
                    {{ $errors->first('smtp_host') }}
                </span>
            @endif
        </div>
        <div class="form-group col-md-12 form-material floating{{ $errors->has('smtp_port') ? ' has-danger' : '' }}" data-plugin="formMaterial">
            {{ Form::text('smtp_port', null, ['class'=>'form-control', 'required']) }}
            <label class="floating-label col-md-12">
                @lang("MailManagement::{$prefix}.{$type}.smtp_port")
            </label>
            @if ($errors->has('smtp_port'))
                <span class="help-block form-control-label">
                    {{ $errors->first('smtp_port') }}
                </span>
            @endif
        </div>
        <div class="form-group col-md-12 form-material floating{{ $errors->has('smtp_username') ? ' has-danger' : '' }}" data-plugin="formMaterial">
            {{ Form::text('smtp_username', null, ['class'=>'form-control', 'required']) }}
            <label class="floating-label col-md-12">
                @lang("MailManagement::{$prefix}.{$type}.smtp_username")
            </label>
            @if ($errors->has('smtp_username'))
                <span class="help-block form-control-label">
                    {{ $errors->first('smtp_username') }}
                </span>
            @endif
        </div>
        <div class="form-group col-md-12 form-material floating{{ $errors->has('smtp_password') ? ' has-danger' : '' }}" data-plugin="formMaterial">
            {{ Form::text('smtp_password', null, ['class'=>'form-control', 'required']) }}
            <label class="floating-label col-md-12">
                @lang("MailManagement::{$prefix}.{$type}.smtp_password")
            </label>
            @if ($errors->has('smtp_password'))
                <span class="help-block form-control-label">
                    {{ $errors->first('smtp_password') }}
                </span>
            @endif
        </div>
        <div class="form-group col-md-12 form-material floating{{ $errors->has('smtp_encription') ? ' has-danger' : '' }}" data-plugin="formMaterial">
            {{ Form::text('smtp_encription', null, ['class'=>'form-control', 'required']) }}
            <label class="floating-label col-md-12">
                @lang("MailManagement::{$prefix}.{$type}.smtp_encription")
            </label>
            @if ($errors->has('smtp_encription'))
                <span class="help-block form-control-label">
                    {{ $errors->first('smtp_encription') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <h3 class="panel-title">@yield('title') imap</h3>
        <div class="form-group col-md-12 form-material floating{{ $errors->has('imap_host') ? ' has-danger' : '' }}" data-plugin="formMaterial">
            {{ Form::text('imap_host', null, ['class'=>'form-control', 'required']) }}
            <label class="floating-label col-md-12">
                @lang("MailManagement::{$prefix}.{$type}.imap_host")
            </label>
            @if ($errors->has('imap_host'))
                <span class="help-block form-control-label">
                    {{ $errors->first('imap_host') }}
                </span>
            @endif
        </div>
        <div class="form-group col-md-12 form-material floating{{ $errors->has('imap_port') ? ' has-danger' : '' }}" data-plugin="formMaterial">
            {{ Form::text('imap_port', null, ['class'=>'form-control', 'required']) }}
            <label class="floating-label col-md-12">
                @lang("MailManagement::{$prefix}.{$type}.imap_port")
            </label>
            @if ($errors->has('imap_port'))
                <span class="help-block form-control-label">
                    {{ $errors->first('imap_port') }}
                </span>
            @endif
        </div>
        <div class="form-group col-md-12 form-material floating{{ $errors->has('imap_username') ? ' has-danger' : '' }}" data-plugin="formMaterial">
            {{ Form::text('imap_username', null, ['class'=>'form-control', 'required']) }}
            <label class="floating-label col-md-12">
                @lang("MailManagement::{$prefix}.{$type}.imap_username")
            </label>
            @if ($errors->has('imap_username'))
                <span class="help-block form-control-label">
                    {{ $errors->first('imap_username') }}
                </span>
            @endif
        </div>
        <div class="form-group col-md-12 form-material floating{{ $errors->has('imap_password') ? ' has-danger' : '' }}" data-plugin="formMaterial">
            {{ Form::text('imap_password', null, ['class'=>'form-control', 'required']) }}
            <label class="floating-label col-md-12">
                @lang("MailManagement::{$prefix}.{$type}.imap_password")
            </label>
            @if ($errors->has('imap_password'))
                <span class="help-block form-control-label">
                    {{ $errors->first('imap_password') }}
                </span>
            @endif
        </div>
        <div class="form-group col-md-12 form-material floating{{ $errors->has('imap_encryption') ? ' has-danger' : '' }}" data-plugin="formMaterial">
            {{ Form::text('imap_encryption', null, ['class'=>'form-control', 'required']) }}
            <label class="floating-label col-md-12">
                @lang("MailManagement::{$prefix}.{$type}.imap_encryption")
            </label>
            @if ($errors->has('imap_encryption'))
                <span class="help-block form-control-label">
            {{ $errors->first('imap_encryption') }}
        </span>
            @endif
        </div>
        <div class="form-group col-md-12 form-material floating{{ $errors->has('imap_validate_cert') ? ' has-danger' : '' }}" data-plugin="formMaterial">
            {{ Form::text('imap_validate_cert', null, ['class'=>'form-control']) }}
            <label class="floating-label col-md-12">
                @lang("MailManagement::{$prefix}.{$type}.imap_validate_cert")
            </label>
            @if ($errors->has('imap_validate_cert'))
                <span class="help-block form-control-label">
                    {{ $errors->first('imap_validate_cert') }}
                </span>
            @endif
        </div>

    </div>

</div>
