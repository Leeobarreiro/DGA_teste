<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gantt de Produção</title>

    <!-- FullCalendar + Scheduler (via CDN) -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.11/index.global.min.js'></script>


    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        #calendar {
            max-width: 100%;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <h1>Gantt de Produção</h1>

    <div id='calendar'></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives', // uso de teste comercial (Não esquecer)
                initialView: 'resourceTimelineDay',
                locale: 'pt-br',
                nowIndicator: true,
                aspectRatio: 2.0,
            editable: true,
            eventDrop: function (info) {
                const evento = info.event;

            fetch('{{ url('tarefas/atualizar') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        id: evento.id,
                        maquina_id: evento.getResources()[0]?.id ?? evento._def.resourceIds[0],
                        inicio: evento.start.toISOString(),
                        fim: evento.end.toISOString(),
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message);
                        info.revert(); // volta a tarefa para posição anterior
                    }
                })
                .catch(() => {
                    alert('Erro ao mover tarefa.');
                    info.revert();
                });
            },
                headerToolbar: {
                    left: 'today prev,next',
                    center: 'title',
                    right: 'resourceTimelineDay,resourceTimelineWeek'
                },
                resourceAreaHeaderContent: 'Máquinas',

                resources: {!! json_encode(
                \App\Models\Maquina::select('id', 'nome as title')->get()
                ) !!},

                events: '{{ url('tarefas/json') }}'
            });

            calendar.render();
        });
    </script>
</body>
</html>
