<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ isset($interview) ? 'Editar Entrevista' : 'Nueva Entrevista' }}
                </h1>
                <p class="text-gray-600 mt-1">
                    {{ isset($interview) ? 'Actualiza la información de la entrevista' : 'Complete los datos para programar una nueva entrevista' }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('rrhh.interviews.index') }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Candidato -->
                <div>
                    <label for="applicant_id" class="block text-sm font-medium text-gray-700 mb-2">Candidato <span class="text-red-500">*</span></label>
                    <select id="applicant_id" wire:model="form.applicant_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Seleccionar candidato</option>
                        @foreach($applicants as $applicant)
                            <option value="{{ $applicant->id }}">
                                {{ $applicant->full_name }}
                                @if($applicant->identification_number) - {{ $applicant->identification_number }} @endif
                                ({{ $applicant->email }})
                                @if($applicant->vacancy) - {{ $applicant->vacancy->title }} @endif
                            </option>
                        @endforeach
                    </select>
                    @error('form.applicant_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fecha y Hora -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Fecha <span class="text-red-500">*</span></label>
                        <input type="date" id="date" wire:model="form.date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        @error('form.date')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="time" class="block text-sm font-medium text-gray-700 mb-2">Hora</label>
                        <input type="time" id="time" wire:model="form.time"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        @error('form.time')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Entrevistador -->
                <div>
                    <label for="interviewer_id" class="block text-sm font-medium text-gray-700 mb-2">Entrevistador</label>
                    <select id="interviewer_id" wire:model="form.interviewer_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Seleccionar entrevistador</option>
                        @foreach($interviewerOptions as $interviewer)
                            <option value="{{ $interviewer->id }}">{{ $interviewer->name }}</option>
                        @endforeach
                    </select>
                    @error('form.interviewer_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tipo de Entrevista -->
                <div>
                    <label for="interview_type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Entrevista</label>
                    <select id="interview_type_id" wire:model="form.interview_type_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Seleccionar tipo</option>
                        @foreach($interviewTypeOptions as $interviewType)
                            <option value="{{ $interviewType->id }}">{{ $interviewType->name }}</option>
                        @endforeach
                    </select>
                    @error('form.interview_type_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="status_id" wire:model="form.status_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Seleccionar estado</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('form.status_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Segunda Fila -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Resultado -->
                <div>
                    <label for="result_id" class="block text-sm font-medium text-gray-700 mb-2">Resultado</label>
                    <select id="result_id" wire:model="form.result_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Seleccionar resultado</option>
                        @foreach($resultOptions as $result)
                            <option value="{{ $result->id }}">{{ $result->name }}</option>
                        @endforeach
                    </select>
                    @error('form.result_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Calificación -->
                <div>
                    <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">Calificación (0-10)</label>
                    <input type="number" id="rating" wire:model="form.rating" min="0" max="10" step="0.1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    @error('form.rating')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Tercera Fila -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Plataforma -->
                <div>
                    <label for="platform" class="block text-sm font-medium text-gray-700 mb-2">Plataforma</label>
                    <input type="text" id="platform" wire:model="form.platform"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                           placeholder="Ej: Google Meet, Zoom, Teams">
                    @error('form.platform')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- URL de Plataforma -->
                <div>
                    <label for="platform_url" class="block text-sm font-medium text-gray-700 mb-2">URL de Plataforma</label>
                    <input type="url" id="platform_url" wire:model="form.platform_url"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                           placeholder="https://meet.google.com/...">
                    @error('form.platform_url')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Cuarta Fila -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Resultados Esperados -->
                <div>
                    <label for="expected_results" class="block text-sm font-medium text-gray-700 mb-2">Resultados Esperados</label>
                    <textarea id="expected_results" wire:model="form.expected_results" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                              placeholder="Habilidades técnicas que se esperan evaluar..."></textarea>
                    @error('form.expected_results')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Observaciones del Entrevistador -->
                <div>
                    <label for="interviewer_observations" class="block text-sm font-medium text-gray-700 mb-2">Observaciones del Entrevistador</label>
                    <textarea id="interviewer_observations" wire:model="form.interviewer_observations" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                              placeholder="Notas y comentarios del entrevistador..."></textarea>
                    @error('form.interviewer_observations')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex justify-end space-x-3 mt-6">
            <a href="{{ route('rrhh.interviews.index') }}"
               class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                <i class="fas fa-times mr-2"></i>Cancelar
            </a>
            <button type="submit"
                    class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-save mr-2"></i>{{ isset($interview) ? 'Actualizar' : 'Guardar' }} Entrevista
            </button>
        </div>
        </form>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
</div>
