<table>
    <thead>
        <tr>
            <!-- Header Row 1: Days of the week and Totals headers -->
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; background-color: #fce4d6; border: 1px solid #000000; width: 25px;">E.Name</th>
            @foreach($dates as $date)
                @php
                    $carbonDate = \Carbon\Carbon::parse($date);
                    $isWeekend = $carbonDate->isSunday();
                    $isHoliday = in_array($date, $holidays);
                    $bgColor = ($isWeekend || $isHoliday) ? '#c00000' : '#fff2cc';
                    $color = ($isWeekend || $isHoliday) ? '#ffffff' : '#000000';
                @endphp
                <th style="font-weight: bold; text-align: center; background-color: {{ $bgColor }}; color: {{ $color }}; border: 1px solid #000000; width: 5px;">
                    {{ $carbonDate->format('D') }}
                </th>
            @endforeach
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; background-color: #70ad47; color: #ffffff; border: 1px solid #000000; width: 10px;">TOTAL PRESENT</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; background-color: #c00000; color: #ffffff; border: 1px solid #000000; width: 10px;">TOTAL ABSENT</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; background-color: #ed7d31; color: #ffffff; border: 1px solid #000000; width: 10px;">TOTAL LEAVE</th>
        </tr>
        <tr>
            <!-- Header Row 2: Dates -->
            @foreach($dates as $date)
                @php
                    $carbonDate = \Carbon\Carbon::parse($date);
                    $isWeekend = $carbonDate->isSunday();
                    $isHoliday = in_array($date, $holidays);
                    $bgColor = ($isWeekend || $isHoliday) ? '#c00000' : '#fff2cc';
                    $color = ($isWeekend || $isHoliday) ? '#ffffff' : '#000000';
                @endphp
                <th style="font-weight: bold; text-align: center; background-color: {{ $bgColor }}; color: {{ $color }}; border: 1px solid #000000;">
                    {{ $carbonDate->format('d') }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $empId => $data)
            <tr>
                <!-- Employee Name -->
                <td style="border: 1px solid #000000; background-color: #e2efda;">{{ $data['name'] }}</td>
                
                <!-- Daily Status -->
                @foreach($dates as $date)
                    @php
                        $status = $data['attendance'][$date] ?? '';
                        $isWeekend = \Carbon\Carbon::parse($date)->isSunday();
                        $isHoliday = in_array($date, $holidays);
                        
                        $bgColor = '#e2efda'; // Default light green for empty/normal days
                        $color = '#000000';
                        $text = $status;
                        
                        if ($isWeekend || $isHoliday) {
                            $bgColor = '#c00000'; // Red
                            $color = '#ffffff';
                            $text = 'H';
                        } else {
                            if ($status === 'P') {
                                $bgColor = '#e2efda'; // Light green
                                $color = '#38761d';
                            } elseif ($status === 'A') {
                                $bgColor = '#f4cccc'; // Light red
                                $color = '#cc0000';
                            } elseif ($status === 'L') {
                                $bgColor = '#fff2cc'; // Light yellow
                                $color = '#b45f06';
                            } elseif ($status === 'H') {
                                $bgColor = '#d9ead3'; // Another green shade
                                $color = '#274e13';
                            } elseif ($status === 'LV') {
                                $bgColor = '#fce5cd'; // Light orange
                                $color = '#b45f06';
                            }
                        }
                    @endphp
                    <td style="text-align: center; border: 1px solid #000000; background-color: {{ $bgColor }}; color: {{ $color }};">
                        {{ $text }}
                    </td>
                @endforeach
                
                <!-- Totals -->
                <td style="text-align: center; border: 1px solid #000000; background-color: #e2efda;">{{ $data['present'] }}</td>
                <td style="text-align: center; border: 1px solid #000000; background-color: #f4cccc;">{{ $data['absent'] }}</td>
                <td style="text-align: center; border: 1px solid #000000; background-color: #fff2cc;">{{ $data['leave'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
