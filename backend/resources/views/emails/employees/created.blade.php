<x-mail::message>
Hello {{ $employee->employee_name }}

A new user for task management system has been created for {{ $employee->employee_name }}.

Your password is "{{ config('app.employee_defalut_password') }}".

Here is the link to login to the system.

{{ $loginUrl }}

Thank you.
</x-mail::message>
