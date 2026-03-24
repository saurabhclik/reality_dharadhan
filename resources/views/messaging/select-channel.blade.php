@extends('layouts.app')

@section('title', 'Create Template - Select Channel')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>Create New Template
                    </h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h4>Select Channel for Template</h4>
                            <p class="text-muted">Choose the communication channel for your template</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('messaging.templates.create', ['channel' => 'whatsapp']) }}" 
                                   class="card channel-card text-decoration-none">
                                    <div class="card-body text-center">
                                        <i class="fab fa-whatsapp fa-4x text-success mb-3"></i>
                                        <h5>WhatsApp Template</h5>
                                        <p class="text-muted">Create template for WhatsApp messages</p>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-md-6">
                                <a href="{{ route('messaging.templates.create', ['channel' => 'email']) }}" 
                                   class="card channel-card text-decoration-none">
                                    <div class="card-body text-center">
                                        <i class="fas fa-envelope fa-4x text-danger mb-3"></i>
                                        <h5>Email Template</h5>
                                        <p class="text-muted">Create template for email messages</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.channel-card {
    transition: all 0.3s;
    border: 2px solid transparent;
    height: 100%;
}
.channel-card:hover {
    transform: translateY(-5px);
    border-color: #007bff;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>
@endsection