<div class="input-group" style="margin-top: -12px">
    <span class="input-group-addon iconos">
        <i class="fa-solid fa-staff-snake"></i>
    </span>
    <div class="d-inline-block">
        <label class="control-label d-block m-0">¿Tienes alguna patología? <small>(requerido)</small></label>
        <div class="custom-control custom-radio custom-control-inline">
            <input name="hasPathology" type="radio" id="pathology1" value="1" class="custom-control-input" {{isset($user) && $user->cliente?->pathology != null ? 'checked=checked' : '' }}>
            <label class="custom-control-label" for="pathology1">Si</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input name="hasPathology" type="radio" id="pathology2" value="0" class="custom-control-input" {{ isset($user) && $user->cliente?->pathology != null ? '': 'checked=checked'  }}>
            <label class="custom-control-label" for="pathology2">No</label>
        </div>
    </div>
</div>
<div id="pathology-description" class="input-group mt-2" style="margin-left: 45px; {{isset($user) && $user->cliente?->pathology != null ? '' : 'display:none'}}">
    <div class="form-group label-floating">
        <label class="control-label">¿Cúal? <small>(requerido)</small></label>
        <input name="pathology" class="form-control" required value="{{isset($user) && $user->cliente?->pathology }}">
    </div>
</div>

@push('scripts')
    <script src="{{asset('js/pathology.js')}}"></script>
@endpush