@extends('layouts.modern-dashboard')

@section('title', 'Site Configuration - ' . $category->name)

@section('page-title', 'Site Configuration: ' . $category->name)

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <a href="{{ route('admin.categories') }}" class="text-muted">
                        <i class="fas fa-arrow-left"></i> Back to Domains
                    </a>
                </h5>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form action="{{ route('admin.categories.site-config.update', $category) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <!-- Domain Settings -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-globe me-2"></i>Domain Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Domain URL</label>
                        <input type="text" name="domain" class="form-control" value="{{ old('domain', $category->domain) }}" placeholder="e.g., plumber.com">
                        <small class="text-muted">The domain where this site will be hosted</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Site Description</label>
                        <textarea name="site_description" class="form-control" rows="3" placeholder="Brief description of the service...">{{ old('site_description', $category->site_description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Branding -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-palette me-2"></i>Branding</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Primary Color</label>
                            <input type="color" name="primary_color" class="form-control form-control-color" value="{{ old('primary_color', $category->primary_color ?: '#0066cc') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Secondary Color</label>
                            <input type="color" name="secondary_color" class="form-control form-control-color" value="{{ old('secondary_color', $category->secondary_color ?: '#00cc66') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Logo & Images -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-images me-2"></i>Logo & Images</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        @if($category->logo_url)
                            <div class="mb-2">
                                <img src="{{ asset($category->logo_url) }}" alt="Logo" style="max-height: 100px; max-width: 200px;" class="img-thumbnail">
                                <br>
                                <a href="{{ route('admin.categories.site-config.remove-logo', $category) }}" class="btn btn-sm btn-danger mt-2" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i> Remove
                                </a>
                            </div>
                        @endif
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <small class="text-muted">Recommended: PNG, SVG, or JPG (max 2MB)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Favicon</label>
                        @if($category->favicon_url)
                            <div class="mb-2">
                                <img src="{{ asset($category->favicon_url) }}" alt="Favicon" style="max-height: 32px; max-width: 32px;" class="img-thumbnail">
                                <br>
                                <a href="{{ route('admin.categories.site-config.remove-favicon', $category) }}" class="btn btn-sm btn-danger mt-2" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i> Remove
                                </a>
                            </div>
                        @endif
                        <input type="file" name="favicon" class="form-control" accept="image/*">
                        <small class="text-muted">Recommended: ICO or PNG 32x32 (max 512KB)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">OG Image (Open Graph)</label>
                        @if($category->og_image_url)
                            <div class="mb-2">
                                <img src="{{ asset($category->og_image_url) }}" alt="OG Image" style="max-height: 200px; max-width: 400px;" class="img-thumbnail">
                                <br>
                                <a href="{{ route('admin.categories.site-config.remove-og-image', $category) }}" class="btn btn-sm btn-danger mt-2" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i> Remove
                                </a>
                            </div>
                        @endif
                        <input type="file" name="og_image" class="form-control" accept="image/*">
                        <small class="text-muted">Recommended: 1200x630px JPG or PNG (max 2MB)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta Tags -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Meta Tags (SEO)</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $category->meta_title) }}" placeholder="e.g., Professional Plumber Services | Plumber.com" maxlength="255">
                        <small class="text-muted">Recommended: 50-60 characters</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="3" placeholder="Brief description for search engines..." maxlength="500">{{ old('meta_description', $category->meta_description) }}</textarea>
                        <small class="text-muted">Recommended: 150-160 characters</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $category->meta_keywords) }}" placeholder="plumber, plumbing, repair, installation">
                        <small class="text-muted">Comma-separated keywords</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deployment Status -->
    @if($category->last_deployed_at || $category->deploy_status)
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-rocket me-2"></i>Deployment Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            @if($category->deploy_status === 'success')
                                <span class="badge bg-success">Success</span>
                            @elseif($category->deploy_status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($category->deploy_status === 'failed')
                                <span class="badge bg-danger">Failed</span>
                            @else
                                <span class="badge bg-secondary">Not Deployed</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Last Deployed:</strong>
                            {{ $category->last_deployed_at ? $category->last_deployed_at->format('Y-m-d H:i:s') : 'Never' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- API Info -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-code me-2"></i>API Endpoint for VPS Script</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label">Configuration URL:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ url('/api/categories/' . $category->code . '/site-config') }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard(this)">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    <small class="text-muted">Use this URL in your VPS deployment script to fetch the site configuration.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Save Configuration
                    </button>
                    <a href="{{ route('admin.categories') }}" class="btn btn-secondary btn-lg ms-2">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function copyToClipboard(button) {
    const input = button.closest('.input-group').querySelector('input');
    input.select();
    document.execCommand('copy');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i> Copied!';
    setTimeout(() => {
        button.innerHTML = originalText;
    }, 2000);
}
</script>
@endpush
@endsection

