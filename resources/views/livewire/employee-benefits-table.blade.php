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
                                <th class="px-4 py-2">Employee Number</th>
                                <th class="px-4 py-2">Date</th>
                                <th class="px-4 py-2">Employee Name</th>
                                <th class="px-4 py-2">SSS</th>
                                <th class="px-4 py-2">SSS Number</th>
                                <th class="px-4 py-2">Pagibig Number</th>
                                <th class="px-4 py-2">Pagibig</th>
                                <th class="px-4 py-2">Philhealth Number</th>
                                <th class="px-4 py-2">Philhealth</th>
                                <th class="px-4 py-2">Updated At</th>
                                <th class="px-4 py-2">Activation</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $index => $record)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $record->employee_number }}</td>

                                    <td class="px-4 py-2">{{ $record->date }}</td>

                                    <td class="px-4 py-2">{{ $record->employee_name }}</td>
                                    <td class="px-4 py-2">{{ $record->sss }}</td>

                                    <td class="px-4 py-2">{{ $record->sss_number }}</td>
                                    <td class="px-4 py-2">{{ $record->pagibig }}</td>
                                    <td class="px-4 py-2">{{ $record->pagibig_number }}</td>
                                    

                                    <td class="px-4 py-2">{{ $record->philhealth }}</td>

                                    <td class="px-4 py-2">{{ $record->philhealth_number }}</td>
                                
                            
                                   
                                    <td class="px-4 py-2">{{ $record->updated_at ?? 'Unknown' }}</td>
                                
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
                                    </div>
                                </td>    
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            

<!-- Fixed Footer for Row Count -->
<div class="fixed bottom-0 left-0 w-full p-2 z-20">
    <span class="text-sm text-gray-600">Total number of Employee With Benefits : {{ $this->rowCount }}</span>
</div>
       
            {{-- Edit Function --}}
        @if($isEditModalOpen)
        <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color:black;">Edit Benefits Information</h5>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="update">
                            {{-- Step 1 --}}
                            @if ($step === 1)

                              <div class="form-group">
                                <label for="employee_number">Employee</label>
                               <select wire:model="editBenefit.employee_number" class="form-control" required>
                                    <option value="">-- Select Employee --</option>
                                    @foreach ($Employee as $employee)
                                        <option value="{{ $employee->employee_number }}">
                                            {{ $employee->employee_number }} - {{ $employee->emp_Firstname }} {{ $employee->emp_Lastname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            

                                <div class="form-group">
                                    <label style="color:black;">Customer Name</label>
                                    <input type="text" wire:model="editBenefit.employee_name" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Date Paid</label>
                                    <input type="date" wire:model="editBenefit.date" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">SSS Number</label>
                                    <input type="text" wire:model="editBenefit.sss_number" class="form-control" required>
                                </div>

                                 

                                

                                

                                <div class="form-group text-center">
                                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                                    <button type="button" class="btn btn-success" wire:click="nextStep()">Next</button>
                                </div>
                            @endif

                            {{-- Step 2 --}}
                            @if ($step === 2)

                                <div class="form-group">
                                    <label style="color:black;">SSS Contribution</label>
                                    <input type="text" wire:model="editBenefit.sss" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Pag-ibig Number</label>
                                    <input type="text" wire:model="editBenefit.pagibig_number" class="form-control" required>
                                </div>


                                <div class="form-group">
                                    <label style="color:black;">Pag-ibig Contribution</label>
                                    <input type="text" wire:model="editBenefit.pagibig" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">PhilHealth Number</label>
                                    <input type="text" wire:model="editBenefit.philhealth_number" class="form-control" required>
                                </div>

                                 <div class="form-group">
                                    <label style="color:black;">PhilHealth</label>
                                    <input type="text" wire:model="editBenefit.philhealth" class="form-control" required>
                                </div>

                                <div class="form-group">
                                <label style="color:black;">Status</label>
                               <select wire:model="editBenefit.isActive" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                                <div class="form-group text-center">
                                    <button type="button" class="btn btn-secondary" wire:click="previousStep()">Previous</button>
                                    <button type="submit" class="btn btn-success">Save Employee Benefits</button>
                                </div>
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