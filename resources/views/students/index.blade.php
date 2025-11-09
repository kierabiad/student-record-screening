<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Record System | Laravel 12 CRUD</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js for interactivity (modals) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Custom scrollbar for better visual appearance */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-thumb { background-color: #3b82f6; border-radius: 4px; }
        ::-webkit-scrollbar-track { background-color: #f1f5f9; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen p-4 sm:p-8">

    <div class="max-w-7xl mx-auto" x-data="{
        // Initial state for modals, triggered by session flashes on validation error
        // Using json_encode for safe, valid JavaScript boolean output
        showCreateModal: {{ json_encode(session('show_create_modal', false)) }},
        showEditModal: false,
        showViewModal: false,
        studentToEdit: null,
        studentToView: null,
        
        // Function to open the Edit Modal and set the dynamic data
        openEditModal(student) {
            this.studentToEdit = student;
            this.showEditModal = true;
        },
        
        // Function to open the View Modal and set the dynamic data
        openViewModal(student) {
            this.studentToView = student;
            this.showViewModal = true;
        },

        // Helper function for date formatting in the View modal
        formatDate(dateString) {
            if (!dateString) return '';
            // Only take the date part if it's a full date-time string
            const dateOnly = dateString.split(' ')[0]; 
            const date = new Date(dateOnly);
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }
    }">

        <!-- Header -->
        <header class="mb-8">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-2">Student Record System</h1>
            <p class="text-lg text-gray-600">Full CRUD Implementation with Laravel 12 and MySQL.</p>
        </header>

        <!-- Notification Area -->
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg" role="alert">
                {{ session('error') }}
            </div>
        @endif
        
        <!-- Validation Error Message and Auto-Open Edit Modal on Error -->
        @if ($errors->any())
            @if (session('show_edit_modal_for_id'))
                <!-- This initiates Alpine to open the edit modal with the student data that caused the error -->
                <script>
                    document.addEventListener('alpine:init', () => {
                        // Use json_encode for safe JavaScript injection
                        const student = {!! json_encode($students->firstWhere('id', session('show_edit_modal_for_id'))) !!};
                        // The student object might be null if the ID was invalid, so check it before calling the function
                        if (student) {
                            // Find the Alpine data scope and call the function
                            const app = document.querySelector('[x-data]');
                            if (app && app.__x) {
                                app.__x.$data.openEditModal(student);
                            } else {
                                // Fallback for delayed Alpine initialization
                                setTimeout(() => {
                                    document.querySelector('[x-data]').__x.$data.openEditModal(student);
                                }, 100);
                            }
                        }
                    });
                </script>
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg" role="alert">
                    Please correct the errors below in the **Edit Student** form.
                </div>
            @else
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg" role="alert">
                    Please correct the errors below in the **Create New Student** form.
                </div>
            @endif
        @endif

        <!-- Action Button -->
        <div class="mb-6 flex justify-end">
            <button @click="showCreateModal = true" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                + Create New Student
            </button>
        </div>

        <!-- Students Table (List View) -->
        <div class="bg-white shadow-xl rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Level</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($students as $student)
                            <tr class="hover:bg-gray-50 transition duration-100">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->student_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->full_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->course }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->year_level }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="openViewModal({{ $student->toJson() }})" class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                                    <button @click="openEditModal({{ $student->toJson() }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                    <form action="{{ route('students.destroy', $student) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('CONFIRMATION REQUIRED: Are you absolutely sure you want to delete the record for {{ $student->full_name }} ({{ $student->student_id }})? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No student records found. Click "Create New Student" to add the first one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- --- MODALS START HERE (Using Tailwind/Alpine for a dynamic Single Page App feel) --- -->

        <!-- CREATE MODAL -->
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showCreateModal = false" aria-hidden="true"></div>
        
                <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-xl leading-6 font-medium text-gray-900" id="modal-title">Create New Student</h3>
                        <form action="{{ route('students.store') }}" method="POST" class="mt-5">
                            @csrf
                            <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-6">
                                
                                <!-- Student ID -->
                                <div>
                                    <label for="student_id" class="block text-sm font-medium text-gray-700">Student ID (Unique)</label>
                                    <input value="{{ old('student_id') }}" type="text" name="student_id" id="student_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('student_id') border-red-500 @enderror">
                                    @error('student_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                
                                <!-- Full Name -->
                                <div>
                                    <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input value="{{ old('full_name') }}" type="text" name="full_name" id="full_name" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('full_name') border-red-500 @enderror">
                                    @error('full_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                
                                <!-- Date of Birth -->
                                <div>
                                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                    <input value="{{ old('date_of_birth') }}" type="date" name="date_of_birth" id="date_of_birth" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('date_of_birth') border-red-500 @enderror">
                                    @error('date_of_birth') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                
                                <!-- Gender -->
                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                                    <select name="gender" id="gender" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('gender') border-red-500 @enderror">
                                        <option value="">Select Gender</option>
                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                
                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email (Unique)</label>
                                    <input value="{{ old('email') }}" type="email" name="email" id="email" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('email') border-red-500 @enderror">
                                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                
                                <!-- Course -->
                                <div>
                                    <label for="course" class="block text-sm font-medium text-gray-700">Course</label>
                                    <input value="{{ old('course') }}" type="text" name="course" id="course" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('course') border-red-500 @enderror">
                                    @error('course') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                
                                <!-- Year Level -->
                                <div>
                                    <label for="year_level" class="block text-sm font-medium text-gray-700">Year Level (1-6)</label>
                                    <input value="{{ old('year_level') }}" type="number" name="year_level" id="year_level" required min="1" max="6"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('year_level') border-red-500 @enderror">
                                    @error('year_level') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            
                            <div class="mt-5 bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Save Student
                                </button>
                                <button type="button" @click="showCreateModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- EDIT MODAL -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditModal = false; studentToEdit = null" aria-hidden="true"></div>
        
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-xl leading-6 font-medium text-gray-900" id="modal-title">Edit Record: <span x-text="studentToEdit ? studentToEdit.full_name : ''"></span></h3>
                        <form x-bind:action="studentToEdit ? `{{ url('students') }}/${studentToEdit.id}` : '#'" method="POST" class="mt-5">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-6">
                                
                                <!-- Student ID -->
                                <div>
                                    <label for="edit_student_id" class="block text-sm font-medium text-gray-700">Student ID (Unique)</label>
                                    <input :value="studentToEdit ? studentToEdit.student_id : ''" type="text" name="student_id" id="edit_student_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('student_id') border-red-500 @enderror">
                                    @error('student_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Full Name -->
                                <div>
                                    <label for="edit_full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input :value="studentToEdit ? studentToEdit.full_name : ''" type="text" name="full_name" id="edit_full_name" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('full_name') border-red-500 @enderror">
                                    @error('full_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Date of Birth -->
                                <div>
                                    <label for="edit_date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                    <input :value="studentToEdit ? studentToEdit.date_of_birth : ''" type="date" name="date_of_birth" id="edit_date_of_birth" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('date_of_birth') border-red-500 @enderror">
                                    @error('date_of_birth') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Gender -->
                                <div>
                                    <label for="edit_gender" class="block text-sm font-medium text-gray-700">Gender</label>
                                    <select :value="studentToEdit ? studentToEdit.gender : ''" name="gender" id="edit_gender" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('gender') border-red-500 @enderror">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    @error('gender') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="edit_email" class="block text-sm font-medium text-gray-700">Email (Unique)</label>
                                    <input :value="studentToEdit ? studentToEdit.email : ''" type="email" name="email" id="edit_email" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email') border-red-500 @enderror">
                                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Course -->
                                <div>
                                    <label for="edit_course" class="block text-sm font-medium text-gray-700">Course</label>
                                    <input :value="studentToEdit ? studentToEdit.course : ''" type="text" name="course" id="edit_course" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('course') border-red-500 @enderror">
                                    @error('course') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Year Level -->
                                <div>
                                    <label for="edit_year_level" class="block text-sm font-medium text-gray-700">Year Level (1-6)</label>
                                    <input :value="studentToEdit ? studentToEdit.year_level : ''" type="number" name="year_level" id="edit_year_level" required min="1" max="6"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('year_level') border-red-500 @enderror">
                                    @error('year_level') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            
                            <div class="mt-5 bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Update Record
                                </button>
                                <button type="button" @click="showEditModal = false; studentToEdit = null" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- VIEW MODAL -->
        <div x-show="showViewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showViewModal = false; studentToView = null" aria-hidden="true"></div>
        
                <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-xl leading-6 font-medium text-gray-900 border-b pb-2 mb-4">Student Profile</h3>
                        
                        <dl class="divide-y divide-gray-100">
                            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900">Student ID</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0" x-text="studentToView ? studentToView.student_id : ''"></dd>
                            </div>
                            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900">Full Name</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0" x-text="studentToView ? studentToView.full_name : ''"></dd>
                            </div>
                            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900">Email Address</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0" x-text="studentToView ? studentToView.email : ''"></dd>
                            </div>
                            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900">Course</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0" x-text="studentToView ? studentToView.course : ''"></dd>
                            </div>
                            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900">Year Level</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0" x-text="studentToView ? studentToView.year_level : ''"></dd>
                            </div>
                            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900">Gender</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0" x-text="studentToView ? studentToView.gender : ''"></dd>
                            </div>
                            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900">Date of Birth</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0" x-text="studentToView ? formatDate(studentToView.date_of_birth) : ''"></dd>
                            </div>
                        </dl>

                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 mt-4">
                            <button type="button" @click="showViewModal = false; studentToView = null" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>