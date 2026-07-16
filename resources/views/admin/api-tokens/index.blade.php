@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-12 mb-3">
        @if(session('success'))
            <div class="alert alert-success">
                {!! session('success') !!}
            </div>
        @endif
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Active API Tokens</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Last Used</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tokens as $token)
                        <tr>
                            <td>{{ $token->name }}</td>
                            <td>{{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Never' }}</td>
                            <td>{{ $token->created_at->format('d M Y') }}</td>
                            <td>
                                <form action="{{ route('admin.api-tokens.destroy', $token->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Revoke this token? Applications using it will lose access.')">Revoke</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No API tokens generated yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Generate New Token</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.api-tokens.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label>Token Name</label>
                        <input type="text" name="token_name" class="form-control" placeholder="e.g. Biometric Scanner" required>
                        <small class="text-muted">Give it a recognizable name.</small>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100">Generate Token</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
