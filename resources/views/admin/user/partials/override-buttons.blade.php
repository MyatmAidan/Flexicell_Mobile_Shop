<div class="override-group d-flex align-items-center gap-2" data-perm-id="{{ $perm['id'] }}" data-role="{{ $perm['role_has'] ? '1' : '0' }}">
    <div class="btn-group btn-group-sm" role="group">
        <input type="radio"
            class="btn-check"
            name="permissions[{{ $perm['id'] }}]"
            id="inherit_{{ $perm['id'] }}"
            value="inherit"
            {{ $perm['override'] === null ? 'checked' : '' }}>
        <label class="btn btn-outline-secondary px-2" for="inherit_{{ $perm['id'] }}" title="Inherit from Role">I</label>

        <input type="radio"
            class="btn-check"
            name="permissions[{{ $perm['id'] }}]"
            id="grant_{{ $perm['id'] }}"
            value="grant"
            {{ $perm['override'] === 'grant' ? 'checked' : '' }}>
        <label class="btn btn-outline-success px-2" for="grant_{{ $perm['id'] }}" title="Grant Permission">G</label>

        <input type="radio"
            class="btn-check"
            name="permissions[{{ $perm['id'] }}]"
            id="revoke_{{ $perm['id'] }}"
            value="revoke"
            {{ $perm['override'] === 'revoke' ? 'checked' : '' }}>
        <label class="btn btn-outline-danger px-2" for="revoke_{{ $perm['id'] }}" title="Revoke Permission">R</label>
    </div>
    <div class="effective-indicator ms-1">
        @if($perm['effective'])
            <i class="fas fa-check-circle text-success" title="Allowed"></i>
        @else
            <i class="fas fa-ban text-danger" title="Denied"></i>
        @endif
    </div>
</div>
