@extends('backEnd.newmasterpage')
@section('mainContent')




    <style>
        /* ================================
                           Dashboard Grade Styling
                           ================================ */

        /* ================================
                       Reusable Max-Height Scrollable
                       ================================ */
        .max-height {
            max-height: 300px;
            /* adjust as needed */
            overflow-y: auto;
            scrollbar-width: thin;
            /* Firefox */
            scrollbar-color: #b0b8c5 #f1f3f9;
            /* thumb + track */
        }

        /* Chrome/Edge Scrollbar */
        .max-height::-webkit-scrollbar {
            width: 6px;
        }

        .max-height::-webkit-scrollbar-track {
            background: #f1f3f9;
            border-radius: 8px;
        }

        .max-height::-webkit-scrollbar-thumb {
            background-color: #b0b8c5;
            border-radius: 8px;
        }


        /* Card Styling */
        .card {
            border: none;

            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease-in-out;
        }



        /* Card Header */
        .card-header {
            background-color: white;
            color: #212529 !important;
            border-bottom: none
        }

        .card-header h6 {
            font-weight: 600;
            font-size: 1rem;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .card-fixed-lg {
            height: 325px;
            /* large card */
            overflow-y: auto;
        }

        /* Rounded Box Metrics */
        .rounded__box {
            border: 2px solid transparent;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            margin: 0.5rem;
            background: rgb(222, 235, 225);
            min-width: 140px;
            text-align: center;
            transition: all 0.3s ease-in-out;
        }

        .rounded__box:hover {
            background: #eef2fb;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
        }

        /* Font Sizes for Metrics */
        .font-card-large {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1b1e34;
        }

        .font-card-medium {
            font-size: 1.1rem;
            font-weight: 600;
            color: #444;
        }

        /* Sales Table */
        .sales_tab {
            font-size: 0.85rem;
            color: #4e5d78;
        }

        .sales_tab thead {
            background: #f1f3f9;
            font-weight: 600;
        }

        .sales_tab td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        .sales_tab tbody tr:hover {
            background: #f9fbff;
        }

        /* Table Striping */
        .table-striped2 tbody tr:nth-child(odd) {
            background-color: #f8f9fc;
        }

        /* Links inside Metrics */
        .rounded__box a {
            text-decoration: none;
            color: inherit;
        }

        .rounded__box a:hover {
            color: #0b2262;
        }
    </style>

    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex  justify-content-between ">
                <!-- Left: Heading -->
                <h4 class="mb-0">GRN Dashboard</h4>
                <input type="hidden" id="base_url" value="{{ url('/') }}" />



            </div>

        </div>

        <div class="left-nav-list">

            <div class="row mt-3">

                <div class="col-md-6 mb-4">
                    <div class="card shadow p-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="header-title m-0">Do Pending</h4>
                        </div>
                        <div class="card-body pt-0  max-height">
                            <div class="table-responsive table-bordered">
                                <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover"
                                    style="table-layout: fixed;width:100%" id="long-list">
                                    <thead>
                                        <tr>
                                            <th>Deal</th>
                                            <th>Company</th>
                                            <th>Owner</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($do_pending) > 0)
                                            @foreach ($do_pending as $top)
                                                <tr>
                                                    <td><a href="{{ url('crm-deal-track-approval/' . $top->id . '') }}"
                                                            title="View Deal Track"
                                                            class="text-dark">{{ $top->deal_id }}</a></td>
                                                    <td>
                                                        {{ $top->customername->name }}
                                                    </td>
                                                    <td>{{ $top->ownername->full_name }}</td>
                                                    <td>{{ date('d/m/Y', strtotime($top->date)) }}</td>
                                                    <td> <span class="badge bg-warning py-1 px-2">New</span></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive-->

                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card shadow p-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="header-title m-0">GRN Pending for Approval</h4>
                        </div>
                        <div class="card-body pt-0  max-height">
                            <div class="table-responsive table-bordered">
                                <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover"
                                    style="table-layout: fixed;width:100%" id="long-list">
                                    <thead>
                                        <tr>
                                            <th>Deal</th>
                                            <th>Supplier Name</th>
                                            <th>LPO No</th>
                                            <th>Customer</th>
                                            <th>Owner</th>
                                            <th>Delivery Date</th>
                                            <th>GRN NO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($grn_pending) > 0)
                                            @foreach ($grn_pending as $top)
                                                <tr>
                                                    <td>{{ $top->deal_id }}</td>
                                                    <td>{{ $top->supplier_name }}</td>
                                                    <td>{{ $top->lpo_no }}</td>
                                                    <td>
                                                        {{ $top->customername->name }}
                                                    </td>
                                                    <td>{{ $top->ownername->full_name }}</td>
                                                    <td>{{ date('d/m/Y', strtotime($top->delivery_date)) }}</td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <a href="{{ url('crm-deal-track-approval/' . $top->trackid . '') }}"
                                                                title="View Deal Track"
                                                                class="btn btn-light btn-sm">Update</a>
                                                        </div>

                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive-->

                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div>

            </div>


            <!-- Modal GRN-->
            <div class="modal fade" id="ModalGRN" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add GRN No</h5>
                            <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-grn-no-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        <input type="hidden" id="grn_id" name="grn_id" value="0" />
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="mb-3">
                                        <label for="" class="form-label">GRN NO</label>
                                        <input type="text" class="form-control" name="grn_no" id="grn_no" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update GRN No</button>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <!-- Modal GRN-->

        </div>
    </aside>




    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


@endsection
