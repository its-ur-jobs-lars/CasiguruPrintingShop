<div>
    {{-- <h2 class="text-lg font-semibold mb-4">Laptop Inventory</h2> --}}

    
        <!-- Search Filter -->
        <input type="text" wire:model.debounce.500ms="search" placeholder="Search..."
            class="border border-gray-300 rounded-9 px-3 py-2 w-full">
    
          
        <select wire:model="filterActivation" class="border border-gray-300 rounded px-3 py-2 w-full mb-4">
            <option value="1">Active</option>
            <option value="0">Inactive</option>   
        </select>
               
            <div class="overflow-x-auto-1 bg-white shadow-md rounded-lg relative">
                <div class="overflow-y-auto max-h-[500px]">
                    <table class="min-w-full border-collapse">
                        <thead class="sticky top-0 bg-gray-100 z-10">
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2">Employee ID</th>
                                <th class="px-4 py-2">Last Name</th>
                                <th class="px-4 py-2">First Name</th>
                                <th class="px-4 py-2">Middle Name</th>
                                <th class="px-4 py-2">Extension Name</th>
                                <th class="px-4 py-2">Task</th>
                                <th class="px-4 py-2">Date Hired</th>
                                <th class="px-4 py-2">Description</th>
                                 <th class="px-4 py-2">Employee Number</th>
                                 <th class="px-4 py-2">Updated By</th>
                                <th class="px-4 py-2">Updated At</th>
                                <th class="px-4 py-2">Activation</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $index => $record)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $record->employee_id }}</td>

                                    <td class="px-4 py-2">{{ $record->emp_Lastname }}</td>

                                    <td class="px-4 py-2">{{ $record->emp_Firstname }}</td>

                                      <td class="px-4 py-2">{{ $record->emp_Middlename }}</td>
                                     <td class="px-4 py-2">
                                        @php 
                                        $extention_name = [
                                            0 => 'N/A',
                                            1 => 'Jr.',
                                            2 => 'Sr.',
                                            3 => 'II',
                                            4 => 'III',
                                            5 => 'IV',
                                            6 => 'V',
                                        ];
                                        @endphp
                                        {{ $extention_name[$record->ext_name] ?? 'Not Applicable' }}
                                    </td>

                                    <td class="px-4 py-2">{{ $record->task }}</td>

                                    <td class="px-4 py-2">{{ $record->date_Hired }}</td>
                                
                                   <td class="px-4 py-2">{{ $record->description }}</td>

                                    <td class="px-4 py-2">{{ $record->employee_number }}</td>

                                    <td class="px-4 py-2">{{ $record->updated_by ?? 'New Added' }}</td>
                                  
                                    <td class="px-4 py-2">{{ $record->updated_at ?? 'New Added' }}</td>
                                
                                <td class="px-4 py-2">
                                    @if ($record->isActive)
                                        <span class="text-green-600 font-semibold">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    @else
                                        <span class="text-red-600 font-semibold">
                                            <i class="fas fa-times-circle"></i>
                                        </span>
                                    @endif
                                </td>  
                        <td class="px-4 py-2">
                            <div class="button-column">
                                <!-- Edit Button -->
                                <button wire:click="edit({{ $record->id }})"
                                    class="bg-blue-500 text-white px-3 py-1 rounded-1">
                                    <i class="fas fa-solid fa-pen-to-square"></i></button>

                                        <!-- Delete Button
                                        <button wire:click="openChangePasswordModal({{ $record->id }})"
                                            class="bg-red-500 text-white px-3 py-1 rounded-2">
                                            <i class="fas fa-solid fa-key"></i></button> -->
                                    </div>
                                </td>    
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="text-center">No records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            

<!-- Fixed Footer for Row Count -->
<div class="fixed bottom-0 left-0 w-full p-2 z-20">
    <span class="text-sm text-gray-600">Total number of Employees : {{ $this->rowCount }}</span>
</div>
       
            {{-- Edit Function --}}
        @if($isEditModalOpen)
        <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color:black;">Edit Employee Information</h5>
                    </div>
                    <div class="modal-body">
                        <form>
                            @if ($step === 1)

                            {{-- Last Name --}}
                            <div class="form-group">
                                <label style="color:black;">Last Name</label>
                                <input type="text" wire:model="editEmployee.emp_Lastname" class="form-control" required>
                            </div>

                            {{-- First Name --}}
                            <div class="form-group">
                                <label style="color:black;">First Name</label>
                                <input type="text" wire:model="editEmployee.emp_Firstname" class="form-control" required>
                            </div>

                            {{-- Middle Name --}}
                            <div class="form-group">
                                <label style="color:black;">Middle Name</label>
                                <input type="text" wire:model="editEmployee.emp_Middlename" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label style="color:black;">Extension Name</label>
                               <select wire:model="editEmployee.ext_name" class="form-control" required>
                                 <option value="">Select extension name...</option>
                                <option value="0">N/A</option>
                                <option value="1">Jr.</option>
                                <option value="2">Sr.</option>
                                <option value="3">II</option>
                                <option value="4">III</option>
                                <option value="5">IV</option>
                                <option value="6">V</option>
                             </select>
                            </div>

                            {{-- Username --}}
                            <div class="form-group">
                                <label style="color:black;">Task</label>
                                <input type="text" wire:model="editEmployee.task" class="form-control" required>
                            </div>

                            {{-- Next Button--}}
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-secondary" wire:click="closeModal1()">Cancel</button>
                                <button type="button" class="btn btn-success" wire:click="nextStep()">Next</button>
                            </div>
                            
                            @endif

                            <!-- Page 2 -->
                            @if ($step === 2)

                          

                            {{-- Employee Number --}}
                            <div class="form-group">
                                <label style="color:black;">Employee Number</label>
                                <input type="text" wire:model="editEmployee.employee_number" class="form-control" required>
                            </div>

                             {{-- Date Hired --}}
                            <div class="form-group">
                                <label style="color:black;">Date Hired</label>
                                <input type="date" wire:model="editEmployee.date_Hired" class="form-control" required>
                            </div>

                             {{-- Description --}}
                            <div class="form-group">
                                <label style="color:black;">Description</label>
                                <input type="text" wire:model="editEmployee.description" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label style="color:black;">Activation</label>
                                <select wire:model="editEmployee.isActive" class="form-control" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
          

                            {{-- Submit Button --}}
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-secondary" wire:click="previousStep()">Previous</button>
                               {{-- <button wire:click="update()" class="bg-green-500 text-white px-3 py-1 rounded-3 mt-2">Apply changes</button> --}}
                               <button type="button"  class="btn btn-success"  wire:click="update()">Apply changes</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

        



@if (session()->has('message') || session()->has('error'))
    <div class="fixed-3 inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow-lg w-1/3 text-center m-4">
            @if (session()->has('message'))
                <div class="text-green-600 font-semibold text-lg">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="text-red-600 font-semibold text-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- <button class="mt-4 bg-red-500 text-white px-4 py-2 rounded-8" onclick="closeAlertDelete()">OK</button> --}}
        </div>
    </div>
@endif




@if (session()->has('messageUpdate') || session()->has('error'))
    <div class="fixed-3 inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow-lg w-1/3 text-center m-4">
            @if (session()->has('messageUpdate'))
                <div class="text-green-600 font-semibold text-lg">
                    {{ session('messageUpdate') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="text-red-600 font-semibold text-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- <button class="mt-4 bg-red-500 text-white px-4 py-2 rounded-8" onclick="closeAlertDelete()">OK</button> --}}
        </div>
    </div>
@endif


<script>
    function closeAlertDelete() {
        document.querySelector('.fixed.inset-0').style.display = 'none';
    }
</script>

</div> 