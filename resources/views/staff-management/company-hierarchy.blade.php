@extends('layouts.app')
@section('title', 'User Management | Pro-leadexpertz')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0">
                    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0 text-light">User Hierarchy Visualization</h3>
                    </div>
                    <div class="card-body p-0">
                        <div id="chart-container" style="position: relative;">
                            <div id="chart_div" class="org-chart-container"></div>
                            <div id="loadingSpinner" class="text-center py-5">
                                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted">Building organizational hierarchy...</p>
                            </div>
                            <div id="no-data" class="text-center py-5" style="display: none;">
                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No hierarchy data available</h4>
                                <p class="text-muted">The organizational hierarchy appears to be empty</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                        <small class="text-muted">Total Users: {{ count($data) }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() 
    {
        let chart;
        let currentZoom = 100;
        const minZoom = 50;
        const maxZoom = 200;
        const zoomStep = 10;
        document.getElementById('chart_div').style.display = 'none';
        google.charts.load('current', { 
            packages: ['orgchart'],
            callback: drawChart 
        });

        function drawChart() 
        {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Name');
            data.addColumn('string', 'Manager');
            data.addColumn('string', 'ToolTip');

            if (@json($data).length === 0) 
            {
                document.getElementById('loadingSpinner').style.display = 'none';
                document.getElementById('no-data').style.display = 'block';
                return;
            }

            data.addRows(@json($data));

            var container = document.getElementById('chart_div');
            chart = new google.visualization.OrgChart(container);

            google.visualization.events.addListener(chart, 'ready', function() 
            {
                document.getElementById('loadingSpinner').style.display = 'none';
                container.style.display = 'block';
                document.querySelectorAll('.google-visualization-orgchart-node').forEach(node => {
                    node.classList.add('org-node');
                    node.addEventListener('click', function() 
                    {
                        document.querySelectorAll('.org-node').forEach(n => 
                            n.classList.remove('org-node-selected'));
                        this.classList.add('org-node-selected');
                    });
                });
            });

            chart.draw(data, {
                allowHtml: true,
                allowCollapse: true,
                size: window.innerWidth < 768 ? 'small' : 'large',
                nodeClass: 'org-node',
                selectedNodeClass: 'org-node-selected'
            });
            function updateNodeStyles() 
            {
                setTimeout(() => {
                    document.querySelectorAll('.google-visualization-orgchart-node').forEach(node => {
                        node.classList.add('org-node');
                    });
                }, 100);
            }
        }
    });
</script>

<style>
    .card 
    {
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .org-chart-container 
    {
        width: 100%;
        height: 500px;
        padding: 15px;
        overflow: auto;
        transition: transform 0.3s ease;
    }

    .org-node 
    {
        border: 1px solid #CF5D3B !important;
        border-radius: 6px;
        padding: 8px 10px;
        background: white;
        box-shadow: 0 2px 6px rgba(23, 20, 112, 0.05);
        font-size: 0.7rem;
    }

    .org-node:hover 
    {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }

    .org-node-selected 
    {
        border-color: #1e3f82ff !important;
        background: #f8fff9;
        box-shadow: 0 2px 6px rgba(40, 167, 69, 0.1);
    }

    .google-visualization-orgchart-node 
    {
        min-width: 120px;
    }

    .google-visualization-orgchart-collapse 
    {
        width: 18px !important;
        height: 18px !important;
        line-height: 18px !important;
        font-size: 0.7rem !important;
    }

    #chart-container 
    {
        height: 500px;
    }

    #loadingSpinner 
    {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        z-index: 10;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .chart-controls .btn 
    {
        border-radius: 15px;
        padding: 0.15rem 0.5rem;
        font-size: 0.7rem;
    }

    @media (max-width: 768px) 
    {
        .org-chart-container
        {
            height: 400px;
            padding: 10px;
        }

        .google-visualization-orgchart-node
        {
            min-width: 100px;
            padding: 6px 8px;
            font-size: 0.8rem;
        }

        .card-header 
        {
            flex-direction: row;
            align-items: center;
            padding: 0.5rem 0.75rem;
        }

        h4.card-title 
        {
            font-size: 1rem;
        }
    }
</style>
@endsection