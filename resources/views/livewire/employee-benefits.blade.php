<div>
    @if(session()->has('messageInsert'))
        <div class="bg-green-500 text-black p-2 rounded mb-2">
            {{ session('messageInsert') }}
        </div>
    @endif

    {{-- Button to Open Modal --}}
    <button wire:click="openModal" class="btn btn-primary">Add Employee Benefits</button>

    {{-- Product Form Modal --}}
    @if($isOpen)
        <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color:black;">Request Order Receipt</h5>
                        <button type="button" class="close" wire:click="closeModal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            {{-- Step 1 --}}
                            @if ($step === 1)

                              <div class="form-group">
                                <label for="employee_number">Employee</label>
                               <select wire:model="data.employee_number" class="form-control" required>
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
                                    <input type="text" wire:model="data.employee_name" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Date Paid</label>
                                    <input type="date" wire:model="data.date" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">SSS Number</label>
                                    <input type="text" wire:model="data.sss_number" class="form-control" required>
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
                                    <input type="text" wire:model="data.sss" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Pag-ibig Number</label>
                                    <input type="text" wire:model="data.pagibig_number" class="form-control" required>
                                </div>


                                <div class="form-group">
                                    <label style="color:black;">Pag-ibig Contribution</label>
                                    <input type="text" wire:model="data.pagibig" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">PhilHealth Number</label>
                                    <input type="text" wire:model="data.philhealth_number" class="form-control" required>
                                </div>

                                 <div class="form-group">
                                    <label style="color:black;">PhilHealth</label>
                                    <input type="text" wire:model="data.philhealth" class="form-control" required>
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

   

    @livewireScripts
</div>
