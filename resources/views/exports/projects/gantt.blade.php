<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gantt Chart - {{ $project->name }}</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: sans-serif;
            font-size: 9px;
            color: #333;
        }

        .header-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .header-table td {
            padding: 4px;
            vertical-align: top;
        }

        .text-bold {
            font-weight: bold;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 4px;
        }

        /* Main Gantt Table */
        .gantt-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .gantt-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .gantt-table th,
        .gantt-table td {
            border: 1px solid #9ca3af;
            padding: 3px 4px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            height: 16px;
        }

        .gantt-table th {
            background-color: #e5e7eb;
            font-weight: bold;
            text-align: center;
            font-size: 8px;
            text-transform: uppercase;
        }

        /* Specific Column Widths */
        .col-id {
            width: 25px;
        }

        .col-task {
            width: 140px;
        }

        .col-owner {
            width: 60px;
        }

        .col-date {
            width: 50px;
        }

        .col-dur {
            width: 30px;
        }

        .col-check {
            width: 30px;
        }

        /* Timeline Grid via Nested Table */
        .timeline-cell {
            padding: 0 !important;
            vertical-align: top;
            height: 16px;
            position: relative;
        }

        .timeline-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: none;
        }

        .timeline-table td {
            border-right: 1px solid #e5e7eb;
            border-top: none;
            border-bottom: none;
            border-left: none;
            padding: 0;
            height: 16px;
        }

        .timeline-table td:last-child {
            border-right: none;
        }

        .gantt-bar-container {
            position: absolute;
            top: 4px;
            left: 0;
            width: 100%;
            height: 12px;
            z-index: 10;
        }

        .gantt-bar {
            position: absolute;
            height: 100%;
            background-color: #4b5563;
            border-radius: 2px;
            opacity: 0.9;
        }

        .section-header {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
            padding-left: 8px;
        }

        .checkbox-square {
            display: inline-block;
            width: 8px;
            height: 8px;
            border: 1px solid #6b7280;
            background-color: white;
        }
    </style>
</head>

<body>

    <!-- Header Info -->
    <table class="header-table">
        <tr>
            <td width="50%">
                <div class="title">Project Schedule</div>
                <div style="font-size: 10px; color: #666;">{{ $project->name }}</div>
            </td>
            <td width="50%">
                <table style="width: 100%; border: 1px solid #d1d5db; font-size: 9px; border-collapse: collapse;">
                    <tr>
                        <td class="bg-gray text-bold"
                            style="width: 30%; background:#f9fafb; border: 1px solid #d1d5db;">Manager</td>
                        <td style="border: 1px solid #d1d5db;">{{ $manager->name }}</td>
                    </tr>
                    <tr>
                        <td class="bg-gray text-bold" style="background:#f9fafb; border: 1px solid #d1d5db;">Plan</td>
                        <td style="border: 1px solid #d1d5db;">{{ $plan->name }}</td>
                    </tr>
                    <tr>
                        <td class="bg-gray text-bold" style="background:#f9fafb; border: 1px solid #d1d5db;">Date</td>
                        <td style="border: 1px solid #d1d5db;">{{ $generatedAt }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Calculations for Timeline -->
    @php
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $weeks = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $weeks[] = $current->copy();
            $current->addWeek();
        }
    @endphp

    <table class="gantt-table">
        <thead>
            <tr>
                <th class="col-id">ID</th>
                <th class="col-task" style="text-align:left; padding-left: 8px;">Task Name</th>
                <th class="col-owner">Assigned</th>
                <th class="col-date">Start</th>
                <th class="col-date">End</th>
                <th class="col-dur">Dur</th>
                <th class="col-check">Done</th>
                <!-- Timeline Header using Nested Table -->
                <th style="width: auto; padding: 0;">
                    <table class="timeline-table">
                        <tr>
                            @foreach($weeks as $week)
                                <td
                                    style="text-align: center; font-size: 7px; vertical-align: middle; border-right: 1px solid #9ca3af;">
                                    {{ $week->format('M d') }}
                                </td>
                            @endforeach
                        </tr>
                    </table>
                </th>
            </tr>
        </thead>
        <tbody>
            @php $currentBucket = null;
            $index = 1; @endphp
            @foreach($tasks as $task)
                @if($currentBucket !== $task->parent_name)
                    @php $currentBucket = $task->parent_name; @endphp
                    <tr class="section-header">
                        <td colspan="8" style="text-align: left;">{{ $currentBucket }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="text-center">{{ $index++ }}</td>
                    <td title="{{ $task->title }}" style="text-align: left;">{{ Str::limit($task->title, 30) }}</td>
                    <td class="text-center">{{ Str::limit($task->owner, 8) }}</td>
                    <td class="text-center">{{ $task->start_date->format('m/d/y') }}</td>
                    <td class="text-center">{{ $task->due_date->format('m/d/y') }}</td>
                    <td class="text-center">{{ $task->duration }}</td>
                    <td class="text-center">
                        <div class="checkbox-square"></div>
                    </td>

                    <!-- Valid Timeline Cell with Nested Table Background -->
                    <td class="timeline-cell">

                        <!-- Bar container absolute positioned over the cell -->
                        <div class="gantt-bar-container">
                            @php
                                $totalDuration = $totalDays;
                                $daysFromStart = $startDate->diffInDays($task->start_date);
                                $durationDays = $task->duration > 0 ? $task->duration : 1;

                                $leftPct = ($daysFromStart / $totalDuration) * 100;
                                $widthPct = ($durationDays / $totalDuration) * 100;

                                $printColor = '#4b5563';
                                if ($task->progress == 100)
                                    $printColor = '#1f2937';
                            @endphp
                            <div class="gantt-bar"
                                style="left: {{ $leftPct }}%; width: {{ $widthPct }}%; background-color: {{ $printColor }};">
                            </div>
                        </div>

                        <!-- Grid background via table -->
                        <table class="timeline-table">
                            <tr>
                                @foreach($weeks as $w)
                                    <td></td>
                                @endforeach
                            </tr>
                        </table>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>