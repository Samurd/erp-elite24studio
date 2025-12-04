<div>
    <div class="w-full">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ isset($employee) ? 'Editar Empleado' : 'Nuevo Empleado' }}
                    </h1>
                    <p class="text-gray-600 mt-1">
                        {{ isset($employee) ? 'Actualiza la información del empleado' : 'Completa el formulario para registrar un nuevo empleado' }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('rrhh.employees.index') }}" 
                       class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form wire:submit.prevent="save">
                <!-- Work Information -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                        <i class="fas fa-briefcase text-purple-600 mr-2"></i>Información Laboral
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="lg:col-span-2 mb-3">
                            <label for="full_name" class="block font-semibold">Nombre Completo</label>
                            <x-input id="full_name" type="text" name="full_name" class="w-full"
                                placeholder="Nombre completo del empleado" wire:model="form.full_name"  />
                            @error('form.full_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="job_title" class="block font-semibold">Cargo</label>
                            <x-input id="job_title" type="text" name="job_title" class="w-full"
                                placeholder="Cargo del empleado" wire:model="form.job_title"  />
                            @error('form.job_title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="work_email" class="block font-semibold">Email de Trabajo</label>
                            <x-input id="work_email" type="email" name="work_email" class="w-full"
                                placeholder="Email de trabajo" wire:model="form.work_email"  />
                            @error('form.work_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="mobile_phone" class="block font-semibold">Teléfono Móvil</label>
                            <x-input id="mobile_phone" type="text" name="mobile_phone" class="w-full"
                                placeholder="Teléfono móvil" wire:model="form.mobile_phone"  />
                            @error('form.mobile_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="work_schedule" class="block font-semibold">Horario Laboral</label>
                            <x-input id="work_schedule" type="text" name="work_schedule" class="w-full"
                                placeholder="Horario laboral" wire:model="form.work_schedule"  />
                            @error('form.work_schedule') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="department_id" class="block font-semibold">Departamento/Area</label>
                            <select id="department_id" name="department_id" wire:model="form.department_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="">Seleccionar...</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                            @error('form.department_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="lg:col-span-2 mb-3">
                            <label for="work_address" class="block font-semibold">Dirección de Trabajo</label>
                            <x-input id="work_address" type="text" name="work_address" class="w-full"
                                placeholder="Dirección de trabajo" wire:model="form.work_address"  />
                            @error('form.work_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Private Information -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                        <i class="fas fa-user text-purple-600 mr-2"></i>Información Personal
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="mb-3">
                            <label for="identification_number" class="block font-semibold">Número de Identificación</label>
                            <x-input id="identification_number" type="text" name="identification_number" class="w-full"
                                placeholder="Número de identificación" wire:model="form.identification_number"  />
                            @error('form.identification_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="social_security_number" class="block font-semibold">Número de Seguro Social</label>
                            <x-input id="social_security_number" type="text" name="social_security_number" class="w-full"
                                placeholder="Número de seguro social" wire:model="form.social_security_number" />
                            @error('form.social_security_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="passport_number" class="block font-semibold">Número de Pasaporte</label>
                            <x-input id="passport_number" type="text" name="passport_number" class="w-full"
                                placeholder="Número de pasaporte" wire:model="form.passport_number" />
                            @error('form.passport_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="gender_id" class="block font-semibold">Género</label>
                            <select id="gender_id" name="gender_id" wire:model="form.gender_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="">Seleccionar...</option>
                                @foreach($genders as $gender)
                                    <option value="{{ $gender->id }}">{{ $gender->name }}</option>
                                @endforeach
                            </select>
                            @error('form.gender_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="birth_date" class="block font-semibold">Fecha de Nacimiento</label>
                            <x-input id="birth_date" type="date" name="birth_date" class="w-full"
                                wire:model="form.birth_date" />
                            @error('form.birth_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="birth_place" class="block font-semibold">Lugar de Nacimiento</label>
                            <x-input id="birth_place" type="text" name="birth_place" class="w-full"
                                placeholder="Lugar de nacimiento" wire:model="form.birth_place" />
                            @error('form.birth_place') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="birth_country" class="block font-semibold">País de Nacimiento</label>
                            <x-input id="birth_country" type="text" name="birth_country" class="w-full"
                                placeholder="País de nacimiento" wire:model="form.birth_country" />
                            @error('form.birth_country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="education_type_id" class="block font-semibold">Educación</label>
                            <select id="education_type_id" name="education_type_id" wire:model="form.education_type_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="">Seleccionar...</option>
                                @foreach($educationTypes as $educationType)
                                    <option value="{{ $educationType->id }}">{{ $educationType->name }}</option>
                                @endforeach
                            </select>
                            @error('form.education_type_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="marital_status_id" class="block font-semibold">Estado Civil</label>
                            <select id="marital_status_id" name="marital_status_id" wire:model="form.marital_status_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="">Seleccionar...</option>
                                @foreach($maritalStatuses as $maritalStatus)
                                    <option value="{{ $maritalStatus->id }}">{{ $maritalStatus->name }}</option>
                                @endforeach
                            </select>
                            @error('form.marital_status_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="number_of_dependents" class="block font-semibold">Número de Dependientes</label>
                            <x-input id="number_of_dependents" type="number" name="number_of_dependents" class="w-full"
                                min="0" max="20" wire:model="form.number_of_dependents" />
                            @error('form.number_of_dependents') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="lg:col-span-2 mb-3">
                            <label for="home_address" class="block font-semibold">Dirección Particular</label>
                            <x-input id="home_address" type="text" name="home_address" class="w-full"
                                placeholder="Dirección particular" wire:model="form.home_address" />
                            @error('form.home_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="personal_email" class="block font-semibold">Email Personal</label>
                            <x-input id="personal_email" type="email" name="personal_email" class="w-full"
                                placeholder="Email personal" wire:model="form.personal_email" />
                            @error('form.personal_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="private_phone" class="block font-semibold">Teléfono Particular</label>
                            <x-input id="private_phone" type="text" name="private_phone" class="w-full"
                                placeholder="Teléfono particular" wire:model="form.private_phone" />
                            @error('form.private_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="bank_account" class="block font-semibold">Numero Cuenta Bancaria</label>
                            <x-input id="bank_account" type="text" name="bank_account" class="w-full"
                                placeholder="Numero Cuenta bancaria" wire:model="form.bank_account" />
                            @error('form.bank_account') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Disability Information -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                        <i class="fas fa-wheelchair text-purple-600 mr-2"></i>Información de Discapacidad
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center mb-3">
                            <input type="checkbox" 
                                   id="has_disability" 
                                   name="has_disability"
                                   wire:model="form.has_disability"
                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="has_disability" class="ml-2 block text-sm text-gray-700">
                                ¿Tiene alguna discapacidad?
                            </label>
                        </div>
                        
                        @if($form->has_disability)
                            <div class="mb-3">
                                <label for="disability_details" class="block font-semibold">Detalles de la Discapacidad</label>
                                <textarea id="disability_details" name="disability_details" wire:model="form.disability_details"
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                          placeholder="Describa los detalles de la discapacidad..."></textarea>
                                @error('form.disability_details') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                        <i class="fas fa-phone-alt text-purple-600 mr-2"></i>Contacto de Emergencia
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-3">
                            <label for="emergency_contact_name" class="block font-semibold">Nombre del Contacto</label>
                            <x-input id="emergency_contact_name" type="text" name="emergency_contact_name" class="w-full"
                                placeholder="Nombre del contacto de emergencia" wire:model="form.emergency_contact_name"  />
                            @error('form.emergency_contact_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="emergency_contact_phone" class="block font-semibold">Teléfono del Contacto</label>
                            <x-input id="emergency_contact_phone" type="text" name="emergency_contact_phone" class="w-full"
                                placeholder="Teléfono del contacto de emergencia" wire:model="form.emergency_contact_phone"  />
                            @error('form.emergency_contact_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                @if(isset($employee))
                @livewire('modules.cloud.components.model-attachments', [
                    'model' => $employee,
                    'area' => 'rrhh'
                ])
            @else
                @livewire('modules.cloud.components.model-attachments-creator', [
                    'modelClass' => \App\Models\Employee::class,
                    'areaSlug' => 'rrhh'
                ])
            @endif

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <a href="{{ route('rrhh.employees.index') }}" 
                       class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        {{ isset($employee) ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Modal selector de carpetas -->
        @livewire('components.notification')
        
    </div>
</div>
