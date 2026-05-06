@extends('backEnd.newmasterpage')
@section('mainContent')
  @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
@php
  $isEdit = isset($editData);
@endphp

<div class="content-container col-12">
  <div class="tab-content display-flex-tabs">

    {{-- ===== Header ===== --}}
    <div class="purchase-order-content-header d-flex justify-content-between align-items-center mb-3">
      <h4 class="purchase-order-content-header-left">
        {{ $isEdit ? 'Edit Shift' : 'Add New Shift' }}
      </h4>
      <div class="purchase-order-content-header-right">
        <a href="{{ route('shift.index') }}" class="btn btn-light text-dark">
          <i class="ico icon-outline-list text-primary"></i> View List
        </a>
      </div>
    </div>

    {{-- ===== Form Card ===== --}}
    <div class="card mb-3">
      <div class="card-body">

        {{-- Form start --}}
        @if($isEdit)
          {{ Form::open(['url' => route('shift.update', $editData->id), 'method' => 'PUT', 'class' => 'form-horizontal']) }}
        @else
          {{ Form::open(['url' => route('shift.store'), 'method' => 'POST', 'class' => 'form-horizontal']) }}
        @endif

        <div class="row">
          {{-- Code --}}
          <div class="col-lg-3 mb-3">
            <label class="form-label">Shift Code <span class="text-danger">*</span></label>
            <input type="text" name="code" maxlength="32" class="form-control"
                   value="{{ old('code', $isEdit ? $editData->code : '') }}" required>
            @if($errors->has('code'))
              <span class="text-danger small">{{ $errors->first('code') }}</span>
            @endif
          </div>

          {{-- Name --}}
          <div class="col-lg-3 mb-3">
            <label class="form-label">Shift Name <span class="text-danger">*</span></label>
            <input type="text" name="name" maxlength="100" class="form-control"
                   value="{{ old('name', $isEdit ? $editData->name : '') }}" required>
            @if($errors->has('name'))
              <span class="text-danger small">{{ $errors->first('name') }}</span>
            @endif
          </div>

          {{-- Start Time --}}
          <div class="col-lg-3 mb-3">
            <label class="form-label">Start Time</label>
            <input type="time" name="start_time" class="form-control"
                   value="{{ old('start_time', $isEdit ? $editData->start_time : '') }}">
          </div>

          {{-- End Time --}}
          <div class="col-lg-3 mb-3">
            <label class="form-label">End Time</label>
            <input type="time" name="end_time" class="form-control"
                   value="{{ old('end_time', $isEdit ? $editData->end_time : '') }}">
          </div>

          {{-- Break Minutes --}}
          <div class="col-lg-3 mb-3">
            <label class="form-label">Break (Minutes)</label>
            <input type="number" name="break_minutes" min="0" class="form-control"
                   value="{{ old('break_minutes', $isEdit ? $editData->break_minutes : '') }}">
          </div>

          {{-- Description --}}
          <div class="col-lg-3 mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" maxlength="255" class="form-control" rows="2">{{ old('description', $isEdit ? $editData->description : '') }}</textarea>
          </div>

          {{-- Status --}}
          <div class="col-lg-3 mb-3">
            <label class="form-label d-block">Status</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="is_active" value="1"
                     {{ old('is_active', $isEdit ? $editData->is_active : 1) == 1 ? 'checked' : '' }}>
              <label class="form-check-label">Active</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="is_active" value="0"
                     {{ old('is_active', $isEdit ? $editData->is_active : 1) == 0 ? 'checked' : '' }}>
              <label class="form-check-label">Inactive</label>
            </div>
          </div>
        </div>

        {{-- ===== Buttons (Under Form) ===== --}}
        <div class="row mt-4">
          <div class="col-lg-12 d-flex justify-content-end align-items-center">
            <a href="{{ route('shift.index') }}" class="btn btn-light text-dark me-2">
              <i class="ico icon-outline-list text-primary"></i> View List
            </a>
            <button class="btn btn-light text-dark" type="submit">
              <i class="ico icon-outline-bookmark-opened text-success"></i>
              {{ $isEdit ? 'Update' : 'Add' }}
            </button>
          </div>
        </div>

        {{ Form::close() }}
      </div>
    </div>

  </div>
</div>

@endsection
