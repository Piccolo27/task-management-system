<x-mail::message>
    Hello {{ optional($admin)->employee_name }}

    Here are a list of Not Reported Members for today.

    | Date | Employee ID | Employee Name |
    @foreach ($notReportedMembers as $member)
        | {{ today()->format('d-m-Y') }} | {{ optional($member)->employee_id }} | {{ optional($member)->employee_name }} |
    @endforeach
</x-mail::message>
