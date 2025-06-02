<div class="card mb-4">
    <div class="card-header">
        <h5 class="font-weight-semi-bold mb-0">Order Deadlines Calendar</h5>
    </div>
    <div class="card-body">
        <div id="orderCalendar"></div>
    </div>
</div>

@push('scripts')
<!-- FullCalendar CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('orderCalendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 600,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: @json($orders),
            eventContent: function(arg) {
                return { html: arg.event.title };
            }
        });

        calendar.render();
    });
</script>
{{--
@push('styles')
<style>
    :root {
        --fc-button-active-bg-color: #007bff;        /* Bootstrap blue */
        --fc-button-active-border-color: #007bff;
        --fc-button-text-color: #ffffff;
    }

    .fc .fc-button-primary {
        background-color: #007bff;
        border-color: #007bff;
        color: #ffffff;
    }

    .fc .fc-button-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
</style>
@endpush --}}
