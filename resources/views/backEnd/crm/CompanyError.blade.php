@extends('backEnd.masterpage')
@section('mainContent')

<?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Error</h2>
            <span class="page-label">Company Changed</span>
        </div>
        <div>

        </div>
    </div>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <style>
                .status-warning {
                    display: flex;
                    align-items: center;
                    background-color: #fff3cd;
                    color: #856404;
                    border: 1px solid #ffeeba;
                    border-left: 8px solid #ffcc00;
                    padding: 16px 24px;
                    margin: 20px 0;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                    font-family: 'Segoe UI', sans-serif;
                    animation: slideDown 0.4s ease;
                }
            
                .status-code {
                    font-size: 36px;
                    font-weight: bold;
                    margin-right: 20px;
                    color: #ff9900;
                }
            
                .status-message {
                    font-size: 16px;
                    line-height: 1.5;
                }
            
                @keyframes slideDown {
                    from { transform: translateY(-20px); opacity: 0; }
                    to { transform: translateY(0); opacity: 1; }
                }
            </style>
            <div id="companyChangeNotice" class="status-warning pt-5 pb-5" style="margin:50px 0px 100px 0px;">
                <div class="status-code">303</div>
                <div class="status-message">
                    <strong>Company Changed:</strong> <br /> The company selection has been changed. You can reopen the pages from the menu and continue.
                </div>
            </div>
        </div>
    </div>
</div>

</div>



<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection