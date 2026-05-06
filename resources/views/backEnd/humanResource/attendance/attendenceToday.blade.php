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
                <h4 class="mb-0">Todays Attendance</h4>
                <input type="hidden" id="base_url" value="{{ url('/') }}" />
            </div>
        </div>
        <div class="left-nav-list">

            <div class="row mt-3">

                <div class="col-lg-3 mb-3">
                    <div class="card p-4 max-height">
                        <div>
                            <h4 class="page-heading mb-3">Total Employees</h4><hr>
                        </div>
                        <div>
                            <table class="table table-hover mb-0 align-middle">
                                <tr>
                                    <td>Total</td><td>{{ $totalemp }}</td>
                                </tr>
                                <tr>
                                    <td>Present</td><td>{{ $totalemp_present }}</td>
                                </tr>
                                <tr>
                                    <td>Absent</td><td>{{ abs($totalemp-$totalemp_present) }}</td>
                                </tr>
                            </table>

                        </div>
                    </div>
                </div>

            </div>

            <div class="row mt-3">                
                <div class="col-lg-6 mb-3">
                    <div class="card p-4 max-height">
                        <div>
                            <h4 class="page-heading mb-3">Today Attendance</h4><hr>
                        </div>
                        <div>
                            @if(count($attendance_list)>0)
                            <table class="table table-hover mb-0 align-middle">
                                <tr>
                                    <td>#</td>
                                    <td style="width:50px;">Photo</td>
                                    <td>Name</td>
                                    <td>In</td>
                                    <td>Out</td>
                                    <td>Late</td>
                                </tr>
                                @foreach($attendance_list as $list)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                         @if (!empty($employee->staff_photo))
                                            @php
                                                $photoUrl = null;
                                                // If path starts with 'public/' it's already accessible via asset()
                                                if (strpos($list->staff_photo, 'public/') === 0) {
                                                    $photoUrl = asset($list->staff_photo);
                                                } elseif (Storage::disk('public')->exists($list->staff_photo)) {
                                                    $photoUrl = asset('storage/app/public/' . $list->staff_photo);
                                                } elseif (file_exists(public_path($list->staff_photo))) {
                                                    $photoUrl = asset($list->staff_photo);
                                                } else {
                                                    $photoUrl = asset('public/design/assets/images/profile_img.png');
                                                }
                                            @endphp
                                            <img src="{{ $photoUrl }}" alt="Staff Photo" style="width:100%;height:100%;object-fit:cover;display:block;">
                                        @else
                                            <img src="{{ asset('public/design/assets/images/profile_img.png') }}" alt="Staff Photo" style="width:100%;height:100%;object-fit:cover;display:block;">
                                        @endif
                                    </td>
                                    <td>{{ $list->full_name }}</td>
                                    <td>{{ !empty($list->in_time) 
                                        ? \Carbon\Carbon::parse($list->in_time)->format('h:i a') 
                                        : '--' }}</td>
                                    <td>{{ !empty($list->out_time) 
                                        ? \Carbon\Carbon::parse($list->out_time)->format('h:i a') 
                                        : '--' }}</td>
                                    <td>
                                        @php
                                        $late = '-';
                                        if (!empty($list->in_time) && !empty($list->start_time)) {
                                            $in = \Carbon\Carbon::parse($list->in_time);
                                            $start = \Carbon\Carbon::parse($list->start_time);
                                            if ($in->gt($start)) {
                                                $late = $start->diff($in)->format('%H:%I');
                                            } else {
                                                $late = 'On Time';
                                            }
                                        }
                                    @endphp
                                    {{ $late }}
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-3">
                    <div class="card p-4 max-height">
                        <div>
                            <h4 class="page-heading mb-3">Today Remote Attendance</h4><hr>
                        </div>
                        <div>
                            @if(count($attendance_list_re)>0)
                            <table class="table table-hover mb-0 align-middle">
                                <tr>
                                    <td>#</td>
                                    <td style="width:50px;">Photo</td>
                                    <td>Name</td>
                                    <td>In</td>
                                    <td>Out</td>
                                    <td>Late</td>
                                </tr>
                                @foreach($attendance_list_re as $list)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                         @if (!empty($employee->staff_photo))
                                            @php
                                                $photoUrl = null;
                                                // If path starts with 'public/' it's already accessible via asset()
                                                if (strpos($list->staff_photo, 'public/') === 0) {
                                                    $photoUrl = asset($list->staff_photo);
                                                } elseif (Storage::disk('public')->exists($list->staff_photo)) {
                                                    $photoUrl = asset('storage/app/public/' . $list->staff_photo);
                                                } elseif (file_exists(public_path($list->staff_photo))) {
                                                    $photoUrl = asset($list->staff_photo);
                                                } else {
                                                    $photoUrl = asset('public/design/assets/images/profile_img.png');
                                                }
                                            @endphp
                                            <img src="{{ $photoUrl }}" alt="Staff Photo" style="width:100%;height:100%;object-fit:cover;display:block;">
                                        @else
                                            <img src="{{ asset('public/design/assets/images/profile_img.png') }}" alt="Staff Photo" style="width:100%;height:100%;object-fit:cover;display:block;">
                                        @endif
                                    </td>
                                    <td>{{ $list->full_name }}</td>
                                    <td>{{ !empty($list->in_time) 
                                        ? \Carbon\Carbon::parse($list->in_time)->format('h:i a') 
                                        : '--' }}</td>
                                    <td>{{ !empty($list->out_time) 
                                        ? \Carbon\Carbon::parse($list->out_time)->format('h:i a') 
                                        : '--' }}</td>
                                    <td>
                                        @php
                                        $late = '-';
                                        if (!empty($list->in_time) && !empty($list->start_time)) {
                                            $in = \Carbon\Carbon::parse($list->in_time);
                                            $start = \Carbon\Carbon::parse($list->start_time);
                                            if ($in->gt($start)) {
                                                $late = $start->diff($in)->format('%H:%I');
                                            } else {
                                                $late = 'On Time';
                                            }
                                        }
                                    @endphp
                                    {{ $late }}
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </aside>

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection
