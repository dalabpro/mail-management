@extends('backend.layout.app')

@section('title'){{ $titleIndex }}@endsection

@push('header')

    <div class="page-header page-header-bordered">
        <h1 class="page-title">@yield('title')</h1>
        <ol class="breadcrumb">
            {!! $breadcrumbs !!}
        </ol>
    </div>

@endpush

@section('content')

    <div class="page-content">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">@yield('title')</h3>
                <div class="page-header-actions">
                    @can('delete', $model)
                        <button type="button" class="btn btn-sm btn-outline btn-danger modal-trigger destroy-btn js-delete-items" data-toggle="modal" data-target="#remove">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                            @lang("{$prefix}.general.delete")
                        </button>
                    @endcan

                    @can('create', $model)
                        <a href="{{ route('backend.' . $type . '.create') }}" class="btn btn-sm btn-outline btn-success" data-toggle="tooltip" data-original-title="@lang("{$prefix}.general.create")">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            @lang("{$prefix}.general.create")
                        </a>
                    @endcan

                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive" @if($models->isEmpty())style="text-align: center;"@endif>
                    @if($models->isNotEmpty())
                        {!! Form::open(['url' => route('backend.' . $type . '.destroy'), 'method' => 'DELETE', 'id' => 'delete-form']) !!}
                        <table class="table table-bordered" data-role="content" data-plugin="selectable" data-row-selectable="true">
                            <thead class="bg-blue-grey-100">
                            <tr>
                                <th style="width: 50px;">
                                    <span class="checkbox-custom checkbox-primary">
                                      <input class="selectable-all" id="master-check" type="checkbox">
                                      <label></label>
                                    </span>
                                </th>
                                <th>
                                    <div style="text-align: center;">
                                        @lang("MailManagement::{$prefix}.{$type}.email")
                                    </div>
                                </th>
                                <th>
                                    <div style="text-align: center;">
                                        @lang("MailManagement::{$prefix}.{$type}.smtp_host")
                                    </div>
                                </th>
                                <th>
                                    <div style="text-align: center;">
                                        @lang("MailManagement::{$prefix}.{$type}.imap_host")
                                    </div>
                                </th>
                                <th style="width: 150px;">
                                    <div style="text-align: center;">
                                        @lang("{$prefix}.general.action")
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($models as $key => $model)
                                <tr>
                                    <td style="vertical-align: middle;">
                                    <span class="checkbox-custom checkbox-primary">
                                      <input id="user_{{ $model->id }}" value="{{ $model->id }}" name="ids[]" class="selectable-item js-select-items" type="checkbox">
                                      <label for="user_{{ $model->id }}"></label>
                                    </span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        {{ $model->email }}
                                    </td>
                                    <td style="vertical-align: middle;">
                                        {{ $model->smtp_host }}
                                    </td>
                                    <td style="vertical-align: middle;">
                                        {{ $model->imap_host }}
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <div class="page-header-actions" style="top: inherit; right: 47px;">

                                            @can('update', $model)
                                                <a href="{{ route('backend.' . $type . '.edit', [str_singular($type) => $model->id]) }}" class="btn btn-sm btn-icon btn-warning btn-round btn-outline btn-warning" data-toggle="tooltip" data-original-title="@lang("{$prefix}.general.update")">
                                                    <i class="icon fa-wrench" aria-hidden="true"></i>
                                                </a>
                                            @endcan

                                            @can('delete', $model)
                                                <a href="" class="btn btn-sm btn-icon btn-warning btn-round btn-outline btn-danger js-delete-item" data-toggle="tooltip" data-original-title="@lang("{$prefix}.general.delete")">
                                                    <i class="icon fa-trash" aria-hidden="true"></i>
                                                </a>
                                            @endcan

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        @if($models->isNotEmpty())
                            {{ $models->links('backend.pagination.pagination') }}
                        @endif
                        {{ Form::close() }}
                    @else
                        @lang("{$prefix}.general.nothing")
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('backend.modals.remove')

@endsection

@push('styles')@endpush

@push('scripts')@endpush