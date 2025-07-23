<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gantt de Produção</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])


    <!-- FullCalendar + Scheduler -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.11/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales-all.min.js'></script>

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8fafc;
        }

        
        h1 {
            font-size: 2rem;
            font-weight: bold;
            color: #1e3a8a;
            text-align: center
        }

        .fc-event:hover {
            filter: brightness(1.1) !important;;
            cursor: pointer !important;;
        }

        .fc-toolbar-title {
            font-size: 1.5rem !important;
        }

        .fc-button {
            background-color: #2563eb !important;
            border: none !important;
            color: white !important;
        }

        .fc-button:hover {
            background-color: #1d4ed8 !important;
        }

        .fc-resource-cell {
            font-weight: bold;
        }

        /* Modal estilo */
        .modal-hidden {
        display: none !important;
        }

        #modalTarefa {
            position: fixed;
            inset: 0;
            background-color: rgba(0,0,0,0.4);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 50;
        }

        .modal-content {
            background-color: white;
            border-radius: 0.5rem;
            padding: 2rem;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            animation: fade-in 0.3s ease-out;
        }

        @keyframes fade-in {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 >Gantt de Produção</h1>
            <button id="btnNovaTarefa" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                + Nova Tarefa
            </button>
        </div>

        <div id='calendar' class="bg-white rounded-lg shadow p-4"></div>
    </div>

  <!-- MODAL -->
<div id="modalTarefa" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md relative animate-fade-in">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Criar Nova Tarefa</h2>
            <button id="btnCancelar" class="text-gray-400 hover:text-red-600 text-2xl font-bold">&times;</button>
        </div>

        <form id="formNovaTarefa">
            @csrf

            <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
            <input type="text" name="titulo" class="w-full border rounded-lg px-3 py-2 mb-4 focus:ring-2 focus:ring-blue-500" required>

            <label class="block text-sm font-medium text-gray-700 mb-1">Máquina</label>
            <select name="maquina_id" class="w-full border rounded-lg px-3 py-2 mb-4 focus:ring-2 focus:ring-blue-500" required>
                @foreach(\App\Models\Maquina::all() as $maquina)
                    <option value="{{ $maquina->id }}">{{ $maquina->nome }}</option>
                @endforeach
            </select>

            <label class="block text-sm font-medium text-gray-700 mb-1">Início</label>
            <input type="datetime-local" name="inicio" class="w-full border rounded-lg px-3 py-2 mb-4 focus:ring-2 focus:ring-blue-500" required>

            <label class="block text-sm font-medium text-gray-700 mb-1">Fim</label>
            <input type="datetime-local" name="fim" class="w-full border rounded-lg px-3 py-2 mb-4 focus:ring-2 focus:ring-blue-500" required>

            <label class="block text-sm font-medium text-gray-700 mb-1">Cor</label>
            <input type="color" name="cor" class="w-full h-10 rounded-lg mb-6 cursor-pointer" value="#3B82F6">

            <div class="flex justify-end gap-2">
                <button type="button" id="btnCancelar2" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded-lg">Cancelar</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg">Salvar</button>
            </div>
        </form>
    </div>
</div>


<script>
    let calendar;

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modalTarefa');
        const btnNovaTarefa = document.getElementById('btnNovaTarefa');
        const btnCancelar = document.getElementById('btnCancelar');
        const btnCancelar2 = document.getElementById('btnCancelar2');

        btnNovaTarefa?.addEventListener('click', () => modal.classList.remove('modal-hidden'));
        btnCancelar?.addEventListener('click', () => modal.classList.add('modal-hidden'));
        btnCancelar2?.addEventListener('click', () => modal.classList.add('modal-hidden'));


        document.getElementById('formNovaTarefa').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const novaTarefa = {
                titulo: formData.get('titulo'),
                maquina_id: formData.get('maquina_id'),
                inicio: formData.get('inicio'),
                fim: formData.get('fim'),
                cor: formData.get('cor')
            };

            const eventos = calendar.getEvents();

            if (tarefaColideComOutra(novaTarefa, eventos, novaTarefa.maquina_id)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Conflito de Horário',
                    text: 'Já existe uma tarefa neste intervalo para esta máquina.',
                });
                return;
            }

            fetch('{{ url('tarefas/criar') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Tarefa criada!',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    modal.classList.add('hidden');
                    calendar.refetchEvents();
                    this.reset();
                } else {
                    Swal.fire('Erro', data.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Erro', 'Erro ao criar tarefa.', 'error');
            });
        });

        // CALENDÁRIO
        const calendarEl = document.getElementById('calendar');

        fetch('{{ url('horarios/maquinas') }}')
            .then(res => res.json())
            .then(horariosPorMaquina => {
                const horariosFormatados = Object.entries(horariosPorMaquina).flatMap(([maquinaId, horarios]) =>
                    horarios.map(h => ({
                        daysOfWeek: [h.daysOfWeek],
                        startTime: h.startTime,
                        endTime: h.endTime,
                        resourceId: maquinaId
                    }))
                );

                calendar = new FullCalendar.Calendar(calendarEl, {
                    schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
                    initialView: 'resourceTimelineDay',
                    nowIndicator: true,
                    aspectRatio: 2.0,
                    editable: true,
                    eventDurationEditable: false,
                    businessHours: horariosFormatados,
                    locale: 'pt-br',
                    buttonText: {
                    today: 'Hoje',
                    month: 'Mês',
                    week: 'Semana',
                    day: 'Dia',
                    }, 
                    
                    slotLabelFormat: [
                    { weekday: 'short' }, // "Seg", "Ter"
                    { hour: '2-digit', minute: '2-digit', hour12: false } // 08:00
                    ],


                    headerToolbar: {
                        left: 'today prev,next',
                        center: 'title',
                        right: 'resourceTimelineDay'
                    },

                    resourceAreaHeaderContent: 'Máquinas',

                    resources: {!! json_encode(
                        \App\Models\Maquina::select('id', 'nome as title')->get()
                    ) !!},

                    events: '{{ url('tarefas/json') }}',

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
                            inicio: evento.start.toLocaleString('sv-SE'),
                            fim: evento.end.toLocaleString('sv-SE'),
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 3000
                            });
                            info.revert();
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Tarefa atualizada com sucesso!',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Erro ao mover tarefa.',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        info.revert();
                    });
                },

                eventClick: function(info) {
                    Swal.fire({
                        title: 'Deseja excluir esta tarefa?',
                        text: info.event.title,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, excluir',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`{{ url('/tarefas') }}/${info.event.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        toast: true,
                                        icon: 'success',
                                        position: 'top-end',
                                        title: 'Tarefa excluída!',
                                        showConfirmButton: false,
                                        timer: 2000
                                    });
                                    info.event.remove();
                                } else {
                                    Swal.fire('Erro', data.message, 'error');
                                }
                            })
                            .catch(() => {
                                Swal.fire('Erro', 'Erro ao excluir a tarefa.', 'error');
                            });
                        }
                    });
                },

                });

                calendar.render();
                calendar.setOption('locale', 'pt-br'); // força a tradução após render
            });
            
            function tarefaColideComOutra(novaTarefa, eventos, maquinaId) {
                return eventos.some(evento => {
                    const mesmoRecurso = evento.getResources()[0]?.id == maquinaId;
                    const inicioNova = new Date(novaTarefa.inicio);
                    const fimNova = new Date(novaTarefa.fim);
                    const inicioExistente = new Date(evento.start);
                    const fimExistente = new Date(evento.end);

                    return mesmoRecurso && (
                        (inicioNova < fimExistente && fimNova > inicioExistente)
                    );
                });
            }

    });
</script>



</body>
</html>
